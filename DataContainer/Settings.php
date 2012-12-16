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

namespace Netzmacht\Cloud\Api\DataContainer;
use Backend;

/**
 * 
 */ 
class Settings extends Backend
{
	
	/**
	 * 
	 */
	public function getNavigationSystemModules()
	{
		$arrModules = array();
		$i = 0;
		foreach ($GLOBALS['BE_MOD']['system'] as $strName => $arrModule) 
		{
			if($strName == 'cloudapi')
			{
				continue;
			}
			
			$arrModules[$strName] = $GLOBALS['TL_LANG']['MOD'][$strName][0];
		}
		
		return $arrModules;
	}
}