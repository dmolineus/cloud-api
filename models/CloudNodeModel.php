<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   cloud-dropbox 
 * @author    David Molineus <http://www.netzmacht.de>
 * @license   GNU/LGPL 
 * @copyright Copyright 2012 David Molineus netzmacht creative 
 *  
 **/


/**
 * extends files model for fetching cloud nodes entries
 */
class CloudNodeModel extends FilesModel
{
	
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_cloudapi_node';
	
	/**
	 * cloud api name
	 * @var string
	 */
	protected static $intPid = null;
	
	
	/**
	 * limit api
	 * 
	 * @param int api pid
	 */
	public static function setApi($intPid)
	{
		static::$intPid = intval($intPid);		
	}
	
	
	/**
	 * use pre find function to limit cloud models
	 * 
	 * @param Statmenet query statement
	 * @return Statement
	 */
	protected static function preFind(\Database\Statement $objStatement)
	{
		if(static::$intPid !== null)
		{
			$strQuery = $objStatement->query;
			$strSubQuery = ' cloudapi=' . static::$intPid;
			
			// inject where parte for api limiting
			if(($intPos = stripos($strQuery, 'where')) !== null)
			{
				$strSubQuery .= ' AND ';		
				$strQuery = preg_replace('/(WHERE\s*)([^\s]*\s)/i', "WHERE" . $strSubQuery . '\2', $strQuery);
			}
			else 
			{
				$strSubQuery = ' WHERE ' . $strSubQuery;				
				$strQuery = preg_replace('/FROM\s*([A-z0-9_\-])*/i', "\0" . $strSubQuery, $strQuery);
			}
			
			$objStatement->prepare($strQuery);			
		}
		
		return $objStatement;
	}
	
}
