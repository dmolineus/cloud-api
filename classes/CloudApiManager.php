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
		if(isset(static::$arrApi[$strName]))
		{
			return static::$arrApi[$strName];
		}
		
		if(!isset(static::$arrConfig[$strName]['enabled'])) 
		{
			$objInstance = static::getInstance();
			$objInstance->import('Database');
			$objRow = $objInstance->Database->query('SELECT * FROM tl_cloud_api WHERE ' . ($strField == 'id' ? 'id' : 'name') . ' = "' . $strName .'"');
			
			if($objRow !== null)
			{
				static::$arrConfig[$strName] = $objRow->row();
			}			
		}

		// try to find api singleton
		if(isset(static::$arrConfig[$strName])) {
			if(!static::isEnabled($strName)) {
				throw new \Exception(sprintf('Cloud Service %s is not enabled.', $strName));
			}
			
			$strClass = static::$arrConfig[$strName]['class'];
			static::$arrApi[$strName] = new $strClass(static::$arrConfig[$strName]);

			return static::$arrApi[$strName];
		}

		throw new \Exception(sprintf('Cloud Api %s is not installed', $strName));
	}
	
	
	/**
	 * return registered Connections array
	 * 
	 * @param bool false is all registered apis shall be returned
	 * @return array
	 */
	public static function getApis($blnOnlyInstalled=true, $blnOnlyEnabled = true)
	{
		$strWhere = '';
		
		if($blnOnlyEnabled)
		{
			$strWhere = ' WHERE enabled=1';
		}
		
		$objInstance = static::getInstance();
		$objInstance->import('Database');
		
		$objResult = $objInstance->Database->query('SELECT * FROM tl_cloud_api' . $strWhere);
		$arrReturn = array();
		
		while($objResult->next())
		{
			static::$arrConfig[$objResult->name] = $objResult->row();
			static::$arrConfig[$objResult->name]['installed'] = true;
			$arrReturn[$objResult->name] = static::$arrConfig[$objResult->name];
		}
		
		return $blnOnlyInstalled ? $arrReturn : static::$arrConfig;
	}


	/**
	 * check if api is enabled
	 * 
	 * @param string
	 * @return bool
	 */
	public static function isEnabled($strName) {
		if(!isset(static::$arrConfig[$strName]['enabled'])) 
		{
			return false;
		}
				
		return static::$arrConfig[$strName]['enabled'];
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
			'class' => static::$arrConfig[$strName]['class'],
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
