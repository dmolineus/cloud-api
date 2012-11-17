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
 *  
 **/
 
namespace Netzmacht\Cloud\Api;
use File;
use Folder;

/**
 * CloudCache handles caching for the cloud api. 
 * Use this class as a static class
 * 
 * It can be created an object but only for Contao
 * TL_PURGE purposesIt is a static class
 * 
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
	 * @param string cache key
	 * @param string kontent to cache
	 * @return bool
	 */
	public static function cache($strKey, $strContent)
	{
		$strFileName = self::getPath($strKey);
		$objFile = new File($strFileName);
		$objFile->write($strContent);
		
		return true;
	}
	
	
	/**
	 * delete cached content
	 * 
	 * @param string cache key
	 * @return bool true on success
	 */
	public static function delete($strKey)
	{
		if(!self::isCached($strKey)) {
			return false;			
		}
		
		$objFile = new File(self::getPath($strKey));
		$objFile->delete();
		
		return true;
	}
	
	
	/**
	 * get cached content
	 * 
	 * @param string cache key
	 * @return mixed false if not found or content of cache
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
	 * @param string cache key
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
	 * this is called by TL_PURGE hook of contao
	 * 
	 * @return void
	 */
	public function purgeCache()
	{
		$objFolder = new Folder(self::CACHE_DIR);
		$objFolder->purge();		
	}
	
}
