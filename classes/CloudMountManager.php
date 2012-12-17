<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   cloud-api 
 * @author    David Molineus <http://www.netzmacht.de>
 * @license   GNU/LGPL 
 * @copyright Copyright 2012 David Molineus netzmacht creative 
 **/

namespace Netzmacht\Cloud\Api;
use System;


/**
 * CloudMountManager provides method to handle the mounting into the file system of Contao
 * 
 * 
 */
class CloudMountManager extends System implements syncListenable
{
	
	/**
	 * cache for getChildRecords
	 * 
	 * @var array
	 */
	protected $arrCloudChildIds = array();
	
	/**
	 * ids of all found files
	 * 
	 * @var array
	 */
	protected $arrFound = array();
	
	/**
	 * all loaded mount models
	 * 
	 * @var Netzmacht\Cloud\Api\Model\CloudMountModel 
	 */
	protected $arrMount = array();
	
	/**
	 * ids of all found files
	 * 
	 * @var array
	 */
	protected $arrIds = array();
	
	/**
	 * sync listeners
	 * 
	 * @var array
	 */
	protected $arrSyncListeners = array(); 
	
	/**
	 * 
	 */
	protected $blnHasMore = false;
	
	/**
	 * we limit the downloaded not at each time
	 */
	protected $intDownloadedTime = 0;
	
	/**
	 * 
	 */
	protected $intDownloaded = 0;
	
	
	/**
	 * load the database
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->import('Database');
		$this->loadLanguageFile('cloudapi');
	}
	
	
	/**
	 * call every registered sync listener
	 * 
	 * @param mixed string or CloudNodeModel current model or path
	 * @param string action can be info,update,create,delete,error
	 * @param string provided message
	 * @param CloudApi passed cloud api object
	 */
	public function callSyncListener($strAction, $mixedNodeOrPath=null, $strMessage=null, $objApi=null)
	{	
		if(empty($this->arrSyncListeners))
		{
			return;
		}
		
		//foreach($this->arrSyncListeners as $mixedListener)
		for($i = 0; $i < count($this->arrSyncListeners); $i++)
		{
			call_user_func($this->arrSyncListeners[$i], $strAction, $mixedNodeOrPath, $strMessage, $objApi);
		}
	}
	
	
	/**
	 * register a sync listener
	 * 
	 * @param mixed variable which is callable by call_user_func
	 * @param string method name
	 * @param bool true if its a static call 
	 */
	public function registerSyncListener($mixedSource, $strMethod, $blnCallStatic = false)
	{
		if(is_string($mixedSource) && !$blnCallStatic)
		{
			$mixedSource = new $mixedSource();
		}
		
		$this->arrSyncListeners[] = array($mixedSource, $strMethod);
	}
	
	
	/**
	 * listen to the cloud api syncing
	 * 
	 * @param string action
	 * @param string path or node
	 * @param string message
	 * @param CloudApi
	 */
	public function syncListener($strAction, $mixedNodeOrPath, $strMessage=null, $objApi=null)
	{
		
		// initialize the cloud mounts
		if($strAction == 'start')
		{
			$objResult = \CloudMountModel::findByEnabled(1);
			
			if($objResult === null)
			{
				return;
			}
			
			while ($objResult->next()) 
			{
				// ignore incorrect settings
				if($objResult->localId > 1 && $objResult->cloudId > 1)
				{
					$this->arrMount[] = $objResult->current();					
				}				
			}
		}
		
		elseif($strAction == 'stop')
		{
			return;
		}
		
		// no mounts found so stop
		elseif(empty($this->arrMount))
		{
			return;
		}
		
		// listen to create and update actions
		elseif($strAction == 'create' || $strAction == 'update')
		{
			// try to fetch model
			if(is_string($mixedNodeOrPath))
			{
				$mixedNodeOrPath = \CloudNodeModel::findOneByPath($mixedNodeOrPath);
				
				if($mixedNodeOrPath === null || $mixedNodeOrPath->id == '')
				{
					return;
				}
			}
			
			// run through every mount
			foreach ($this->arrMount as $objMount) 
			{
				if(!isset($this->arrCloudChildIds[$objMount->id]))
				{
					$this->arrCloudChildIds[$objMount->id] = $this->Database->getChildRecords($objMount->cloudId, 'tl_cloud_node');					
				}				
				
				if(in_array($mixedNodeOrPath->id, $this->arrCloudChildIds[$objMount->id]))
				{
					$this->handleCloud2LocalNode($objMount, $mixedNodeOrPath);					
				}				
			}		
		}
	}

	
	/**
	 * sync a mounted folder
	 * 
	 * @param mixed int or CloudCountModel 
	 * @param bool force syncing and ignore sync interval
	 */
	public function sync($mixedMount, $blnForce=false)
	{
		
		if(is_numeric($mixedMount))
		{
			$mixedMount = \CloudMountModel::findOneById(intval($mixedMount));		
		}		 
		
		if($mixedMount === null || $mixedMount->enabled == '')
		{
			return false;
		}
		
		// ignore incorrect settings
		if($mixedMount->localId < 1 && $mixedMount->cloudId < 1)
		{
			return false;
		}
		
		if(!$blnForce && ((time() - $mixedMount->syncTstamp) < $GLOBALS['TL_CONFIG']['cloudapiSyncInterval']))
		{
			return false;
		}
		
		// TODO: implement other implement other sync methods
		$this->syncCloud2Local($mixedMount);

		if(!$this->blnHasMore)
		{
			$mixedMount->syncTstamp = time();
			$mixedMount->save();			
		}
		
		return $this->blnHasMore;
	}
	
	
	/**
	 * make sure that every folder of a path exists
	 * 
	 * @param string path
	 * @param string root folder where to stop checking
	 * @return array of path => id
	 */
	protected function createFolderPath($strPath, $strRoot='')
	{
		// create parent folders
		$arrFolders = array();
		//$strFolderWalk = ;
		
		for($strFolderWalk = dirname($strPath); $strFolderWalk != $strRoot; $strFolderWalk = dirname($strPath))
		{
			if($strFolderWalk == '' || $strFolderWalk == '.' || $strFolderWalk == '\\' || $strFolderWalk == '/')
			{
				break;
			}
			
			$arrFolders[] = $strFolderWalk;
		}
				
		if(empty($arrFolders))
		{
			return array();
		}
		
		$this->import('Files');
			
		for($i = count($arrFolders)-1; $i >= 0; $i--)
		{
			$strFolder = $arrFolders[$i];
			$blnNewFolder = false;
			
			if(!is_dir(TL_ROOT . '/' . $strFolder))
			{
				$blnNewFolder = true;
				$this->Files->mkdir($strFolder);
			}
			
			$objFolder = new \Folder($strFolder);
			
			$objModel = \FilesModel::findOneByPath($strFolder);

			// Create the entry if it does not yet exist
			if ($objModel !== null)
			{
				$this->arrIds[$strParent] = $objModel->id;
				$this->arrFound[] = $objModel->id;
				
				if($blnNewFolder)
				{
					$objModel->hash = $objFolder->hash;
					$objModel->save();
				}
				
				continue;
			}
			
			$this->arrIds[$strFolder] = $objModel->id;			
			$strParent = dirname($strFolder);
			
			if(isset($this->arrIds[$strParent]))
			{
				$intPid = $this->arrIds[$strParent];
			}
			else 
			{
				$objParent = \FilesModel::findOneByPath($this->arrIds[$strParent]);
				
				if($objParent === null)
				{
					continue;
				}
				
				$intPid = $objParent->pid;
				$this->arrFound[] = $objParent->id;
				$this->arrIds[$strFolder] = $objParent->id;
			}
			
			$objModel = new \FilesModel();
			$objModel->pid    = $intpid;
			$objModel->tstamp = time();
			$objModel->name   = basename($strFolder);
			$objModel->type   = 'folder';
			$objModel->path   = $strFolder;
			$objModel->hash   = $objFolder->hash;
			$objModel->found  = 1;
			$objModel->save();
			
			$this->arrFound[] = $objModel->id;
			$this->arrIds[$strFolder] = $objModel->id;
			$this->callSyncListener('create', $objModel, $GLOBALS['TL_LANG']['cloudapi']['syncLocalFolderC']);
		}
		
		$this->updateFolderHash($strRoot);
	}
	

	/**
	 * create a local folder or file
	 * 
	 * @param CloudMountModel
	 * @param CLoudNodeModel
	 * @param array found
	 * 
	 */
	protected function createLocalNode($objMount, $objNode)
	{
		// get rood nodes		
		$objCloudRoot = \CloudNodeModel::findOneById($objMount->cloudId);		
		$objLocalRoot = \FilesModel::findOneById($objMount->localId);
		
		// create new path
		$strNewPath = $objLocalRoot->path . '/' . substr($objNode->path, strlen($objCloudRoot->path)+1);
		
		if($objNode->type == 'folder')
		{
			$this->createFolderPath($strNewPath, $objLocalRoot->path, $this->arrIds);		
		}
		
		// check the limits
		elseif($this->intDownloaded < $GLOBALS['TL_CONFIG']['cloudapiSyncDownloadLimit'] && $this->intDownloadedTime < $GLOBALS['TL_CONFIG']['cloudapiSyncDownloadTime'])
		{
			if(!$objNode->isCached)
			{
				$this->intDownloaded++;
			}
			
			$intStart = time();
			$strContent = $objNode->downloadFile();
			$this->intDownloadedTime = time() - $intStart;
			
			$objFile = new \File(dirname($strNewPath) . '/' . utf8_romanize(basename($strNewPath)));
			$objFile->write($strContent);
			$objFile->close();
			
			
			$strParent = dirname($strNewPath);
			if(!isset($arrPid[$strParent]))
			{
				// create parent folders
				$objParent = \FilesModel::findOneByPath($strParent);
				
				if($objParent === null)
				{
					$this->createFolderPath(dirname($strNewPath), $objLocalRoot->path);				
				}
				else
				{
					$this->arrIds[$strParent] = $objParent->id;
					$this->arrFound[] = $objParent->id;
				}
			}
			
			
			// create local file
			$objModel = \FilesModel::findOneByPath($strNewPath);
			$blnCreate = false;
			
			if($objModel === null)
			{
				$objModel = new \FilesModel();
				$blnCreate = true;	
			}
			
			$strParent = dirname($strFolder);
			$objModel->pid       = isset($this->arrIds[$strParent]) ? $this->arrIds[$strParent] : $objLocalRoot->id;
			$objModel->tstamp    = time();
			$objModel->name      = basename($strNewPath);
			$objModel->type      = 'file';
			$objModel->path      = $strNewPath;
			$objModel->extension = $objFile->extension;
			$objModel->hash      = $objFile->hash;
			$objModel->found     = 1;
			$objModel->save();
			
			$this->callSyncListener($blnCreate ? 'create' : 'update' , $objFile, $GLOBALS['TL_LANG']['cloudapi']['syncLocalFile' . ($blnCreate ? 'C' : 'U')]);
			
			// update cloud node
			$this->arrFound[] = $objModel->id;
			
			$objNode->fid = $objModel->id;
			$objNode->mountVersion = $objNode->version;
			$objNode->save();
		}

		// not everything synced
		else
		{
			$this->blnHasMore = true;
		}
	}


	/**
	 * handle cloud2llocal syncing for a single node
	 * 
	 * @param CloudMountModel
	 * @param CloudNodeModel
	 * @param array found ids
	 */
	protected function handleCloud2LocalNode($objMount, $objNode)
	{
		$arrOptions = unserialize($objMount->options);
		
		// no create permission
		if(!in_array('create', $arrOptions) && !in_array('update', $arrOptions))
		{
			return;
		}
		
		// there is already a linked file
		if($objNode->fid > 0)
		{
			$this->arrFound[] = $objNode->fid;
			
			// nothing changed
			// check permission
			// we can not really update folders so continue
			if($objNode->version == $objNode->mountVersion || !in_array('update', $arrOptions) || $objNode->type == 'folder')
			{
				return;
			}
		}
		
		$this->createLocalNode($objMount, $objNode);
	}


	/**
	 * sync diricetion cloud to local
	 * 
	 * @param CloudMountModel 
	 */
	protected function syncCloud2Local($objMount)
	{
		$arrCloudChildIds = $this->Database->getChildRecords($objMount->cloudId, 'tl_cloud_node');
		$arrOptions = unserialize($objMount->options);
		
		// clean database
		$this->Database->query("UPDATE tl_cloud_node SET mountVersion='', fid=NULL WHERE fid != '' AND (SELECT count(id) FROM tl_files WHERE id=fid) = 0");
		
		// prepare delete action
		if(in_array('delete', $arrOptions))
		{
			$arrLocalChildIds = $this->Database->getChildRecords($objMount->localId, 'tl_files');
			
			if(!empty($arrLocalChildIds))
			{
				$this->Database->query('UPDATE tl_files SET found=0 WHERE id IN (' . implode(',', $arrLocalChildIds) . ')');	
			}
		}
		
		// no cloud files found
		if($arrCloudChildIds === null || empty($arrCloudChildIds))
		{
			return;
		}
		
		// get every cloud node
		$objResult = $this->Database->query('SELECT * FROM tl_cloud_node WHERE id IN (' . implode(',', $arrCloudChildIds) . ')');
		
		if($objResult === null)
		{
			return;
		}
		
		$objCollection = new CloudNodeModelCollection($objResult, 'tl_cloud_node');
		
		while ($objCollection->next())
		{
			$this->handleCloud2LocalNode($objMount, $objCollection->current(), $this->arrFound);
		}
		
		// delete every file which was not found
		if(in_array('delete', $arrOptions))
		{
			$arrDelete = array_diff($arrLocalChildIds, $this->arrFound);
			
			if(!empty($arrDelete))
			{
				$objResult = $this->Database->query('SELECT * FROM tl_files WHERE id IN (' . implode(',', $arrDelete) . ') ORDER BY type, path DESC');
				$arrPaths = $objResult->fetchEach('path');
				$arrTypes = $objResult->fetchEach('type');
				
				$this->import('Files');
				
				foreach($arrPaths as $intIndex => $strPath)
				{
					if($arrTypes[$intIndex] == 'folder' && is_dir(TL_ROOT . '/' . $strPath))
					{
						$this->Files->rmdir($strPath);						
						$this->callSyncListener('delete', $strPath, $GLOBALS['TL_LANG']['cloudapi']['syncLocalFileD']);
					}
					elseif($arrTypes[$intIndex] == 'file' && file_exists(TL_ROOT . '/' . $strPath))
					{
						$this->Files->delete($strPath);						
						$this->callSyncListener('delete', $strPath, $GLOBALS['TL_LANG']['cloudapi']['syncLocalFolderD']);			
					}
				}
				
				if(!empty($this->arrFound))
				{
					$this->Database->query('UPDATE tl_files SET found=1 WHERE id IN (' . implode(',', $this->arrFound) . ')');					
				}
				
				if(!empty($arrLocalChildIds))
				{
					$objResult = $this->Database->query('DELETE FROM tl_files WHERE found=0 AND id IN (' . implode(',', $arrLocalChildIds) . ')');
					
					if($objResult->affectedRows > 0)
					{
						//$this->callSyncListener('delete', '', $GLOBALS['TL_LANG']['cloudapi']['syncLocalDeleted']);						
					}
				}
			}
		}
		
		// clean database
		$this->Database->query("UPDATE tl_cloud_node SET mountVersion='', fid=NULL WHERE fid != '' AND (SELECT count(id) FROM tl_files WHERE id=fid) = 0");
		
		$this->updateFolderHash($objMount->localId);			
	}


	/**
	 * update the hash of a folder
	 * 
	 * @parent mixed string or FilesModel
	 */
	protected function updateFolderHash($strFolder)
	{
		if(is_numeric($strFolder))
		{
			$objModel = \FilesModel::findById($strFolder);
			$strFolder = $objModel->path;
		}
		elseif(is_string($strFolder))
		{
			$objModel = \FilesModel::findByPath($strFolder);
		}
		else
		{
			$objModel = $strFolder;
			$strFolder = $objModel->path;
		}
		
		if(!is_dir(TL_ROOT . '/' . $strFolder))
		{
			return;
		}
		
		if($objModel !== null)
		{
			$objFolder = new \Folder($strFolder);
			$objModel->hash = $objFolder->hash;
			$objModel->save();
			
			$this->callSyncListener('hash', $objModel, $GLOBALS['TL_LANG']['cloudapi']['syncLocalHash']);
		}
	}
	
}
