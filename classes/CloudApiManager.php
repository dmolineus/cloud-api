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
	 * 
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
	public static function getApi($strName, $strField='name')
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
		
		$objRow = $objInstance->Database->query('SELECT * FROM tl_cloud_api WHERE enabled=1 AND ' . ($strField == 'id' ? 'id' : 'name') . ' = "' . $strName .'"');
			
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
		
		if($intState == 2)
		{
			$strWhere = ' WHERE enabled=1';
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
	public static function registerApi($strName, $strClass)
	{
		// fetch apis from database
		if(!isset(static::$arrConfig[$strName])) 
		{
			static::$arrConfig[$strName] = array();		
		}
		
		static::$arrConfig[$strName]['class'] = $strClass;	
	}
}
