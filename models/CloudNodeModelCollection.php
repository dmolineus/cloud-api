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
	 * 
	 * 
	 */
	public function getModelClass()
	{
		if(static::$objApi !== null)
		{
			return static::$objApi->modelClass;
		}
		
		$objApi = CloudApiManager::getApi($this->objResult->cloudapi);
		
		if($objApi !== null)
		{
			return $objApi->modelClass;
		}
		
		return parent::getModelClassFromTable($this->strTable);
	}
	
	
	/**
	 * Fetch the next result row and create the model
	 * 
	 * @return boolean True if there was another row
	 */
	protected function fetchNext()
	{
		if ($this->objResult->next() == false)
		{
			return false;
		}

		$strClass = $this->getModelClass();
		//var_dump(CloudApiManager::getApi($this->objResult->cloudapi));
		//\CloudNodeModel::setApi(CloudApiManager::getApi($this->objResult->cloudapi));
		$this->arrModels[$this->intIndex + 1] = new $strClass($this->objResult);

		return true;
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