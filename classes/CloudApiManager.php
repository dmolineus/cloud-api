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
	protected function getInstance()
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
	 * @param string name of api
	 * @throws Exception if api can not be found
	 * @return CloudApi
	 */
	public static function getApi($strName)
	{	
		if(isset(static::$arrApi[$strName]))
		{
			return static::$arrApi[$strName];
		}
		
		if(!isset(static::$arrConfig[$strName])) 
		{
			$objInstance = static::getInstance();
			$objRow = $objInstance->Database->query('SELECT * FROM tl_cloudapi WHERE name = "' . $this->name .'"');
			
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
	public static function getApis($blnOnlyEnabled = true)
	{
		$strWhere = '';
		
		if($blnOnlyEnabled)
		{
			$strWhere = ' WHERE enabled=1';
		}
		
		$objInstance = static::getInstance();
		$objResult = $objInstance->Database->query('SELECT * FROM tl_cloudapi' . $strWhere);
		
		while($objResult->next())
		{
			static::$arrConfig[$objResult->name] = $objResult->row();
		}
		
		return static::$arrApi;
	}


	/**
	 * check if api is enabled
	 * 
	 * @param string
	 * @return bool
	 */
	protected static function isEnabled($strName) {
		if(!isset(static::$arrConfig[$strName]['enabled'])) 
		{
			static::getApi($strName);
		}
				
		return static::$arrConfig[$strName]['enabled'];
	}


	/**
	 * register connection with str name and class path
	 * 
	 * @return void
	 * @param string name of api
	 * @param strClassPath path to api class
	 */
	public static function installApi($strName, $strClass, $arrProvidedModes)
	{
		$objInstance = static::getInstance();
		$objStmt = $objInstance->Database->prepare('INSERT INTO tl_cloudapi %s');
		
		$objStmt->set(array
		(
			'name' => $strName,
			'class' => $strClass,
			'modes' => $arrProvidedModes
		));
		
		$objStmt->execute();		
	}
	
}
