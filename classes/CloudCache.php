<?php

namespace Netzmacht\Cloud\Api;
use File;

/**
 * CloudCache handles caching for the cloud api. It is a static class
 * 
 * @package CloudApi
 * @author David Molineus <mail@netzmacht.de>
 * @copyright Copyright 2012 David Molineus netzmacht creative
 * @license GNU/LGPL
 */
class CloudCache
{
	/**
	 * path to cache dir
	 * 
	 * @var string
	 */
	const CACHE_DIR = 'system/cache/cloud-api/';
	
	/**
	 * cache file paths
	 * 
	 * @var array
	 */
	protected static $arrFilePaths = array();
	
	
	/**
	 * cache content
	 * 
	 * @param string $strKey
	 * @param string $strContent
	 * @param int cache level (1 for meta file, 2 for file)
	 * @return bool
	 */
	public static function cache($strKey, $strContent, $intLevel = 2)
	{
		if(!self::hasCacheLevel($intLevel)) {
			return false;
		}
		
		$strFileName = self::getPath($strKey);
		$objFile = new File($strFileName);
		$objFile->write($strContent);
		
		return true;
	}
	
	
	/**
	 * delete cached content
	 * 
	 * @param string $strKey
	 * @return bool
	 */
	public static function delete($strKey)
	{
		if(!self::isCached($strKey)) {
			return false;			
		}
		
		//$objFile = new File(self::getPath($strKey));
		//$objFile->delete();
		
		return true;
	}
	
	
	/**
	 * get cached content
	 * 
	 * @param string $strKey
	 * @return mixed
	 */
	public static function get($strKey)
	{
		if(!self::isCached($strKey)) {
			return false;
		}
		
		$objFile = new File(self::getPath($strKey));
		
		return $objFile->getContent();
	}
	
	
	/**
	 * generate cache file name
	 * 
	 * @param string $strKey
	 * @return string file path
	 */
	public static function getPath($strKey)
	{
		if(!isset(self::$arrFilePaths[$strKey])) {			
			self::$arrFilePaths[$strKey] = self::CACHE_DIR . $strKey;
		}
		
		return self::$arrFilePaths[$strKey];
	}

	/**
	 * check cache level
	 */
	public static function hasCacheLevel($intLevel=2)
	{
		return ($GLOBALS['cloudApi']['cacheLevel'] >= $intLevel);		
	}
	
			
	/**
	 * check is cache file with key exits
	 * 
	 * @return bool
	 * @param string $strKey
	 */
	public static function isCached($strKey)
	{
		return file_exists(TL_ROOT . '/' . self::getPath($strKey));		
	}
	
	/**
	 * delete all cached files
	 */
	public function purgeCache()
	{
		$objFolder = new \Folder(self::CACHE_DIR);
		$objFolder->purge();		
	}
}
