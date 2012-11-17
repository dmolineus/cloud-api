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
abstract class CloudApi extends System
{

	/**
	 * authenticate cloud api
	 * 
	 * @throws Exception if no valid token has found
	 * @return bool
	 */
	abstract public function authenticate();
	
	
	/**
	 * get account info
	 * 
	 * @return array
	 */	
	abstract public function getAccountInfo();
	
	
	/**
	 * get name of the cloud service
	 * 
	 * @return string
	 */
	abstract public function getName();
	
	
	/**
	 * get cloud node (file or folder)
	 * 
	 * @param string $strPath
	 * @return void
	 */	
	abstract public function getNode($strPath);	
	
	
	/**
	 * search for nodes
	 * 
	 * @return array
	 * @param string search query
	 * @param string start point
	 */
	abstract public function searchNodes($strQuery, $strPath='');
	
	
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
}
