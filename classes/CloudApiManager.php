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
 * CloudApiManager handles registration of different cloud api types
 * Use it to access cloud api files
 * 
 * Its a static class so do not create an instance of it
 */
class CloudApiManager extends System
{
	/**
	 * registerd apis
	 * 
	 * @var array
	 */
	protected static $arrApi = array();
	
	/**
	 * registerd apis
	 * 
	 * @var array
	 */
	protected static $arrConfig = array();
	
	/**
	 * listestener for CloudApi::sync() method
	 * 
	 * @var array
	 */
	protected static $arrSyncListener = array();
	
	
	/**
	 * 
	 */
	protected static $blnConfigImported = false;
	
	/**
	 * 
	 */
	protected static $objInstance = null;
	
	
	/**
	 * 
	 */
	protected function __construct()
	{
		parent::__construct();
	}
	
	
	/**
	 * get singleton used only internally because we need to use the $this->import() function for getting the database
	 * 
	 * @return CloudApiManager
	 */
	protected static function getInstance()
	{
		if(static::$objInstance === null)
		{
			static::$objInstance = new static();
		}
		
		return static::$objInstance;
	}


	/**
	 * get cloud api singleton
	 * 
	 * @param string name or id of api
	 * @param string field name or id
	 * @throws Exception if api can not be found
	 * @return CloudApi
	 */
	public static function getApi($strName)
	{	
		if($strField == 'name' && !isset(static::$arrConfig[$strName])) 
		{
			throw new \Exception(sprintf('Cloud Api %s is not registered', $strName));
		}
		
		if(isset(static::$arrApi[$strName]))
		{
			return static::$arrApi[$strName];
		}
		
		$objInstance = static::getInstance();
		$objInstance->import('Database');
		
		$objRow = $objInstance->Database->query('SELECT * FROM tl_cloud_api WHERE enabled=1 AND ' . (is_numeric($strName) ? 'id' : 'name') . ' = "' . $strName .'"');
			
		if($objRow->numRows > 0)
		{
			$strName = $objRow->name;
			$arrConfig = array_merge((array)static::$arrConfig[$strName], $objRow->row());			
		}
		else
		{
			throw new \Exception(sprintf('Cloud Api %s is not enabled or installed', $strName));
		}
			
		$strClass = static::$arrConfig[$strName]['class'];
		static::$arrApi[$strName] = new $strClass($arrConfig);

		return static::$arrApi[$strName];
	}
	
	
	/**
	 * return registered Connections array
	 * 
	 * @param int which apis to get: 0 uninstalled, 1 installed, 2 installed and enabled
	 * @return array
	 */
	public static function getApis($intState = 2)
	{	
		$strWhere = '';
		
		if($intState > 0)
		{
			$strWhere = ' WHERE enabled=' . ($intState == 1 ? 0 : 1);
		}
		
		$objInstance = static::getInstance();
		$objInstance->import('Database');
		
		$objResult = $objInstance->Database->query('SELECT * FROM tl_cloud_api' . $strWhere);
		$arrReturn = static::$arrConfig;
		
		while($objResult->next())
		{
			if($intState == 0)
			{
				unset($arrReturn[$objResult->name]);
			}
			else
			{
				$arrReturn[$objResult->name] = array_merge(static::$arrConfig[$objResult->name], $objResult->row());	
			}
		}
		
		return $arrReturn;
	}


	/**
	 * check if api is enabled
	 * 
	 * @param string
	 * @return bool
	 */
	public static function isEnabled($strName) 
	{
		if(!isset(static::$arrApi[$strName])) 
		{
			$this->getApi($strName);
			return false;
		}
				
		return static::$arrApi[$strName]->enabled;
	}
	
	
	/**
	 * check if api is installed
	 * 
	 * @param string
	 * @return bool
	 */
	public static function isInstaled($strName) {
		if(!isset(static::$arrConfig[$strName]['installed'])) 
		{
			return false;
		}
				
		return static::$arrConfig[$strName]['installed'];
	}
	
	
	/**
	 * install a registered api in the database
	 * 
	 */
	public static function installApi($strName)
	{
		if(!isset(static::$arrConfig[$strName]))
		{
			return false;
		}		
		
		$objInstance = static::getInstance();
		$objInstance->import('Database');
		
		$objStmt = $objInstance->Database->prepare('SELECT count(name) AS total FROM tl_cloud_api WHERE name=%s');
		$objCount = $objStmt->execute($strName);
		
		if($objCount->total > 0)
		{
			return;
		}
		
		$objStmt = $objInstance->Database->prepare('INSERT INTO tl_cloud_api %s');
		
		$objStmt->set(array
		(
			'name' => $strName,
			'title' => static::$arrConfig[$strName]['title'],
			'tstamp' => time(),
		));
		
		return $objStmt->execute();
	}


	/**
	 * register connection with str name and class path
	 * 
	 * @return void
	 * @param string name of api
	 */
	public static function registerApi($strName, $strClass, $strTitle=null)
	{
		// fetch apis from database
		if(!isset(static::$arrConfig[$strName])) 
		{
			static::$arrConfig[$strName] = array();		
		}
		
		static::$arrConfig[$strName]['class'] = $strClass;
		static::$arrConfig[$strName]['title'] = ($strTitle === null ? $strName : $strTitle);
	}
	
	
	/**
	 * register a sync listener
	 * 
	 * @param mixed variable which is callable by call_user_func
	 * @param string namespace sync listener can be limit for a specific API by setting the name as namespace
	 */
	public static function registerSyncListener($mixedSource, $strMethod, $strNamespace='__global__', $blnCallStatic = false)
	{
		if(is_string($mixedSource) && !$blnCallStatic)
		{
			$mixedSource = new $mixedSource();
		}
		
		static::$arrSyncListener[$strNamespace][] = array($mixedSource, $strMethod);
	}
	
	
	/**
	 * call every registered sync listener
	 * 
	 * @param mixed string or CloudNodeModel current model or path
	 * @param string action can be info,update,create,delete,error
	 * @param string provided message
	 * @param CloudApi passed cloud api object
	 */
	public static function callSyncListener($strAction, $mixedNodeOrPath=null, $strMessage=null, $objApi=null)
	{
		if(!static::$blnConfigImported)
		{
			foreach ($GLOBALS['cloudapiSyncListener'] as $strNamespace => $arrListeners) 
			{
				foreach ($arrListeners as $arrListener) 
				{
					static::registerSyncListener($arrListener[0], $arrListener[1], $strNamespace);					
				}				
			}
			
			static::$blnConfigImported = true;
		}
		
		// global namespace
		if(isset(static::$arrSyncListener['__global__']) && !empty(static::$arrSyncListener['__global__']))
		{
			foreach(static::$arrSyncListener['__global__'] as $mixedListener)
			{
				call_user_func($mixedListener, $strAction, $mixedNodeOrPath, $strMessage, $objApi);
			}
		}

		if($objApi === null)
		{
			return;
		}
		
		// cloud service specific namespace
		if(isset(static::$arrSyncListener[$objApi->name]) && !empty(static::$arrSyncListener[$objApi->name]))
		{
			foreach(static::$arrSyncListener[$objApi->name] as $mixedListener)
			{
				call_user_func($mixedListener, $strAction, $mixedNodeOrPath, $strMessage, $objApi);
			}
		}
	}
}
