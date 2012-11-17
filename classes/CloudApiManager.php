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
class CloudApiManager
{
	/**
	 * registerd apis
	 * 
	 * @var array
	 */
	protected static $arrApi = array();


	/**
	 * get cloud api singleton
	 * 
	 * @throws Exception if api can not be found
	 * @return CloudApi
	 */
	public static function getApi($strName=null)
	{
		// if no name is given try to get first one
		if($strName == null) {
			if(count(static::$arrApi) == 0) {
				throw new \Exception('Can not find any registered Cloud Apis');
			}

			$arrCurrent = current(static::$arrApi);
			$strName = $arrcurrent['name'];
		}

		// try to find api singleton
		if(isset(static::$arrApi[$strName])) {
			if(!static::isEnabled($strName)) {
				throw new \Exception(sprintf('Cloud Api %s exists but is not enabled. Please change settings', $strName));
			}
			
			if(!isset(static::$arrApi[$strName]['instance'])) {
				$strClass = static::$arrApi[$strName]['name'];
				
				static::$arrApi[$strName]['instance'] = new $strClass();
			}

			return static::$arrApi[$strName]['instance'];
		}

		throw new \Exception(sprintf('Cloud Api %s is not registered', $strName));
	}
	
	
	/**
	 * return registered Connections array
	 * 
	 * @param bool false is all registered apis shall be returned
	 * @return array
	 */
	public static function getregisteredApis($blnOnlyEnabled = true)
	{
		if(!$blnOnlyEnabled) {
			return static::$arrApi;
		}
		
		$arrEnabled = array();
		
		foreach(static::$arrApi as $strKey => $arrValue) {			
			if(static::isEnabled($strKey)) {
				$arrEnabled[$strKey] = $arrValue;
			}
		}
		
		return $arrEnabled;
	}


	/**
	 * check if api is enabled
	 * 
	 * @param string
	 * @return bool
	 */
	protected static function isEnabled($strName) {
		if(!isset(static::$arrApi[$strName]['enabled'])) {
			return false;
		}
				
		return static::$arrApi[$strName]['enabled'];
	}


	/**
	 * register connection with str name and class path
	 * 
	 * @return void
	 * @param string name of api
	 * @param strClassPath path to api class
	 */
	public static function registerApi($strName, $mixed)
	{
		if(is_array($mixed)) {
			static::$arrApi[$strName]['name'] = $mixed['name'];
			static::$arrApi[$strName]['enabled'] = $mixed['enabled'];
			
			return;
		}
		
		static::$arrApi[$strName]['name'] = $mixed;
	}
	
}
