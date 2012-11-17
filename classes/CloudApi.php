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
	
}
