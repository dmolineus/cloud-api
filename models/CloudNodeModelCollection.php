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
use Model\Collection;


/**
 * extend model collection
 */
class CloudNodeModelCollection extends Collection
{
	/**
	 * cloud api name
	 * @var string
	 */
	protected static $objApi = null;
	
	
	/**
	 * we modify class name so that we can provide different model classes for each cloud api
	 * 
	 */
	public static function getModelClassFromTable($strTable)
	{
		if(static::$objApi !== null)
		{
			return static::$objApi->modelClass;
		}
		
		return parent::getModelClassFromTable($strTable);
	}
	
	
	/**
	 * limit api
	 * 
	 * @param int api pid
	 */
	public static function setApi($objApi)
	{
		static::$objApi = $objApi;		
	}
	
}