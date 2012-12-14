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
use Netzmacht\Cloud\Api\CloudApiManager;
use Netzmacht\Utils\DataContainer;


/**
 * extends the utils data container
 */
class CloudApi extends DataContainer
{
	
	/**
	 * choose palette supports different palettes depending on cloud name
	 * subpalettes are stores in customsubpalettes['cloudname'].
	 * 
	 * @return void  
	 */
	public function choosePalette()
	{
		$intId = \Input::get('id');
				
		$this->import('Database');
		
		$objStmt = $this->Database->prepare('SELECT * FROM tl_cloud_api WHERE id=?');
		$objResult = $objStmt->execute($intId);
		
		if($objResult->numRows == 0) 
		{
			return;
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objResult->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objResult->name];
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['subpalettes'] = $GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name];
			$GLOBALS['TL_DCA']['tl_cloud_api']['palettes']['__selector__'] = array_keys
			(
				$GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name]
			);
		}
	}


	/**
	 * hide install button if every cloud api is installed
	 * 
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	protected function buttonRuleInstallApi($strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes)
	{
		$arrApis = CloudApiManager::getApis(0);
		
		return (count($arrApis) > 0);
	}
	
}
