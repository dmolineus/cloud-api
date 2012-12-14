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
class CloudMountManager extends System
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
	protected $arrPids = array();
	
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
	public function syncCloudApiListener($strAction, $mixedNodeOrPath, $strMessage=null, $objApi=null)
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
				$this->arrMount[] = $objResult->current();				
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
	 * @param int id of mount 
	 */
	public function syncMountedFolders($intId)
	{
		$objResult = \CloudMountModel::findOneById($intId);		 
		
		if($objResult === null || $objResult->enabled == '')
		{
			return false;
		}
		
		// TODO: implement other implement other sync methods
		$this->syncCloud2Local($objResult);

		if(!$this->blnHasMore)
		{
			$objResult->syncTstamp = time();
			$objResult->save();			
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
		$strFolderWalk = dirname($strPath);
		
		for($strFolderWalk = $strPath; $strFolderWalk != $strRoot; $strFolderWalk = dirname($strPath))
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
				
		for($i = count($arrFolders)-1; $i >= 0; $i--)
		//foreach($arrFolders as $strFolder)
		{
			$strFolder = $arrFolders[$i];
			$objFolder = new \Folder($strFolder);
			$objModel = \FilesModel::findByPath($strFolder);

			// Create the entry if it does not yet exist
			if ($objModel !== null)
			{
				continue;
			}
			
			$this->arrPids[$strFolder] = $objModel->id;			
			$strParent = dirname($strFolder);
			
			if(isset($this->arrPids[$strParent]))
			{
				$intPid = $this->arrPids[$strParent];
			}
			else 
			{
				$objParent = \FilesModel::findOneByPath($this->arrPids[$strParent]);
				
				if($objParent === null)
				{
					continue;
				}
				
				$intPid = $objParent->pid;
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
			$this->createFolderPath($strNewPath, $objLocalRoot->path, $this->arrPids);		
		}
		
		// check the limits
		elseif($this->intDownloaded < $GLOBALS['TL_CONFIG']['cloudapiSyncDownloadLimit'] && $this->intDownloadedTime < $GLOBALS['TL_CONFIG']['cloudapiSyncDownloadTime'])
		{
			if(!CloudCache::isCached($objNode->cacheKey))
			{
				$this->intDownloaded++;
			}
			
			$intStart = time();
			$strContent = $objNode->downloadFile();
			$this->intDownloadedTime = time() - $intStart;
			
			$objFile = new \File($strNewPath);
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
					$this->arrPids[$strParent] = $objParent->id;
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
			$objModel->pid       = isset($this->arrPids[$strParent]) ? $this->arrPids[$strParent] : $objLocalRoot->id;
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
				$objResult = $this->Database->query('SELECT * FROM tl_files WHERE id IN (' . implode(',', $arrDelete) . ')');
				$arrPaths = $objResult->fetchEach('path');
				
				foreach($arrPaths as $strPath)
				{
					if(is_dir(TL_ROOT . '/'))
					{
						$objFile = new \File($strPath);
						$objFile->delete();			
						
						$this->callSyncListener('delete', $objFile, $GLOBALS['TL_LANG']['cloudapi']['syncLocalFolderD']);			
					}
				}
				
				if(!empty($this->arrFound))
				{
					$this->Database->query('UPDATE tl_files SET found=1 WHERE id IN (' . implode(',', $this->arrFound) . ')');					
				}
				
				if(!empty($arrLocalChildIds))
				{
					$this->Database->query('DELETE FROM tl_files WHERE found=0 AND id IN (' . implode(',', $arrLocalChildIds) . ')');
					$this->callSyncListener('delete', '', $GLOBALS['TL_LANG']['cloudapi']['syncLocalDeleted']);
				}
			}
		}
		
		// clean database
		$this->Database->query("UPDATE tl_cloud_node SET mountVersion='', fid=NULL WHERE fid != '' AND (SELECT count(id) FROM tl_files WHERE id=fid) = 0");
		
		$objModel = \FilesModel::findOneById($objMount->localId);
		
		if($objModel !== null)
		{
			$this->updateFolderHash($objModel);			
		}
	}

	/**
	 * update the hash of a folder
	 * 
	 * @parent mixed string or FilesModel
	 */
	protected function updateFolderHash($strFolder)
	{
		if(is_string($strFolder))
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
