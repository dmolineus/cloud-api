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
 * Abstract class for defining the API for CloudApi classes
 * 
 */
abstract class CloudApi extends System implements syncListenable
{
	
	/**
	 * api config
	 * 
	 * @var array
	 */
	protected $arrConfig;
	
	/**
	 * store all created nodes
	 * 
	 * @var array
	 */
	protected $arrNodes = array();
	
	
	/**
	 * sync listeners
	 * 
	 * @var array
	 */
	protected $arrSyncListeners = array(); 
	
	
	/**
	 * constructor will load database config
	 * 
	 * @return void
	 */
	public function __construct($arrRow)
	{			
		if($arrRow === null)
		{
			throw new \Exception(sprintf('Cloud Service "%s" config is not found. Please install the Cloud service.', $this->name));
		}
		
		$this->arrConfig = $arrRow;
				
		// initiate cloud nodes model for limiting statements to current cloud service 
		\CloudNodeModel::setApi($this);
		CloudNodeModelCollection::setApi($this);
		
		$this->import('Database');
		$this->loadLanguageFile('cloudapi');
	}
	
	
	/**
	 * getter implements following keys
	 * 
	 * @param string name
	 * 	- int id
	 *  - string name
	 * @return mixed
	 */
	public function __get($strKey)
	{
		switch($strKey)
		{
			case 'id':
			case 'class':
			case 'enabled':
			case 'title':
			{
				return $this->arrConfig[$strKey];
				break;
			}
			
			case 'modelClass':
				return 'Netzmacht\Cloud\Api\CloudNodeModel';
				break;
			
			case 'name':
			{
				return get_class($this);
				break;
			}
			
			default:
				return parent::__get($strKey);
		}
	}
	

	/**
	 * authenticate cloud api
	 * 
	 * @throws Exception if no valid token has found
	 * @return bool
	 */
	abstract public function authenticate();
	
	
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
	 * get account info
	 * 
	 * @return array
	 */	
	abstract public function getAccountInfo();
	
	
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
	 * reset sync state so that the whole cloud service has to sync again
	 */
	abstract public function resetSyncState();
	
	/**
	 * Send a file to the browser so the "save as …" dialogue opens
	 * 
	 * @param Netzmacht\Cloud\Api\CloudNode
	 */
	public function sendFileToBrowser($objNode)
	{
		// Make sure there are no attempts to hack the file system
		if ($objNode === null || $objNode->downloadUrl == null)
		{
			header('HTTP/1.1 404 Not Found');
			die('Invalid file name');
		}

		// Check whether the file exists
		if ($objNode->downloadUrl == null)
		{
			header('HTTP/1.1 404 Not Found');
			die('File not found');
		}				
		
		$arrAllowedTypes = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

		if (!in_array($objNode->extension, $arrAllowedTypes))
		{
			header('HTTP/1.1 403 Forbidden');
			die(sprintf('File type "%s" is not allowed', $objNode->extension));
		}				

		// Make sure no output buffer is active
		// @see http://ch2.php.net/manual/en/function.fpassthru.php#74080
		while (@ob_end_clean());

		// Prevent session locking (see #2804)
		session_write_close();
		
		// redirect to download url		
		$this->redirect($objNode->downloadUrl);

		// HOOK: post download callback
		/* do we need this hook here and how does that affects contao sendFileToBrowser?
		if (isset($GLOBALS['TL_HOOKS']['postDownload']) && is_array($GLOBALS['TL_HOOKS']['postDownload']))
		{
			foreach ($GLOBALS['TL_HOOKS']['postDownload'] as $callback)
			{
				static::importStatic($callback[0])->$callback[1]($strFile);
			}
		}
		*/	 

		// Stop the script
		exit;
	}


	/**
	 * update sync state in the cloud api table
	 * 
	 * @param bool true
	 * @param string sync cursor
	 * @param bool true if tstamp should be saved by finishing
	 * @return void
	 */
	public function setSyncState($blnActive, $strCursor=null,  $blnStoreTstamp=true)
	{
		$arrParams = array
		(
			'tstamp' => time(),
			'syncInProgress' => ($blnActive) ? '1' : '0',			
		);
		
		if($strCursor !== null)
		{
			$arrParams['deltaCursor'] = $strCursor;
		}
		
		if($blnActive === false && $blnStoreTstamp)
		{
			$arrParams['syncTstamp'] = time();
		}
		
		$objStatement= $this->Database->prepare('UPDATE tl_cloud_api %s WHERE name=?');
		$objStatement->set($arrParams);
		$objStatement->execute($this->name);
		
		$this->callSyncListener($blnActive ? 'start' : 'stop');
	}
	
	
	/**
	 * implements syncing of file system and database
	 * We use the delta sync method. If the cloud api does not 
	 * support this you have to override this method
	 *
	 * @param bool force syncing no matter when last sync was 
	 * @return void
	 */
	public function sync($blnForce = false)
	{	
		// only sync after 10 minutes and make sure that not other clients are also syncing the database
		if(!$blnForce && (((time() - $this->arrConfig['syncTstamp']) < $GLOBALS['TL_CONFIG']['cloudapiSyncInterval']) || $this->arrConfig['syncInProgress'] == '1'))
		{
			return;
		}
		
		$arrMounted = null;
		if($this->arrConfig['mountedFolders'] != '')
		{
			$arrMounted = unserialize($this->arrConfig['mountedFolders']);
		}
		
		$this->setSyncState(true);		
		$strCursor = $this->execSync($this->arrConfig['deltaCursor'], $arrMounted);		
		$this->setSyncState(false, $strCursor);
	}
	
	
	/**
	 * execute syncing
	 * 
	 * @param string cursor
	 * @return string new cursor
	 */	
	abstract protected function execSync($strCursor, $arrMounted=null);
	
}
