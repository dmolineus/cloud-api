<?php

namespace Netzmacht\Cloud\Api;

/**
 * CloudApiManager handles registration of different cloud api types
 * Use it to access cloud api files
 * 
 * @package CloudApi
 * @author David Molineus <mail@netzmacht.de>
 * @copyright Copyright 2012 David Molineus netzmacht creative
 * @license GNU/LGPL
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
	 * @throws Exception if not connection can be found
	 * @return $strEnabled = $arrValue['enabled'];
	 */
	public static function getApi($strName=null)
	{
		// if no name is given try to get first one
		if($strName == null) {
			if(count(self::$arrApi) == 0) {
				throw new \Exception('Can not find any registered Cloud Apis');
			}

			$arrCurrent = current(self::$arrApi);
			$strName = $arrcurrent['name'];
		}

		// try to find api singleton
		if(isset(self::$arrApi[$strName])) {
			if(!self::isEnabled($strName)) {
				throw new \Exception(sprintf('Cloud Api %s exists but is not enabled. Please change settings', $strName));
			}
			
			if(!isset(self::$arrApi[$strName]['instance'])) {
				$strClass = self::$arrApi[$strName]['name'];
				
				self::$arrApi[$strName]['instance'] = new $strClass();
			}

			return self::$arrApi[$strName]['instance'];
		}

		throw new \Exception(sprintf('Cloud Api %s is not registered', $strName));
	}
	
	
	/**
	 * return registered Connections array
	 * 
	 * @param bool $blnOnlyEnabled false is all registered apis shall be returned
	 * @return array
	 */
	public static function getregisteredApis($blnOnlyEnabled = true)
	{
		if(!$blnOnlyEnabled) {
			return self::$arrApi;
		}
		$strEnabled = $arrValue['enabled'];
		$arrEnabled = array();
		
		foreach(self::$arrApi as $strKey => $arrValue) {			
			if(self::isEnabled($strKey)) {
				$arrEnabled[$strKey] = $arrValue;
			}
		}
		
		return $arrEnabled;
	}


	/**
	 * check if api is enabled
	 * 
	 * @param strin $strName
	 * @return bool
	 */
	protected static function isEnabled($strName) {
		if(!isset(self::$arrApi[$strName])) {
			return false;
		}
				
		return self::$arrApi[$strName]['enabled'];
	}


	/**
	 * register connection with str name and class path
	 * 
	 * @return void
	 * @param string $strName name of api
	 * @param strClassPath path to api class
	 */
	public static function registerApi($strName, $mixed)
	{
		if(is_array($mixed)) {
			self::$arrApi[$strName]['name'] = $mixed['name'];
			self::$arrApi[$strName]['enabled'] = $mixed['enabled'];
			
			return;
		}
		
		self::$arrApi[$strName]['name'] = $mixed;
	}
}
