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
use Netzmacht\Utils\DataContainer;

/**
 * data container for cloud mount 
 */
class CloudMount extends DataContainer
{
	
	/**
	 * we have to create our own back button, because setting ptable does not work.
	 * So we make sure that the back button is the left one by simulating a closed table
	 */
	public function hideStandardCreateButton()
	{
		if(\Input::get('act') != 'create' && \Input::get('act') != 'copy')
		{
			$GLOBALS['TL_DCA']['tl_cloud_mount']['config']['closed'] = true;	
		}
	}
	
	
	/**
	 * generate list mounted folder
	 * 
	 * @param array current row
	 * @param string label
	 * @param DataContainer
	 * @param array values
	 */
	public function listMountedFolders($arrRow, $strLabel, $objDc, $arrValues)
	{
		return sprintf
		(
			'<p>%s - %s </p> <p style="color:#B3B3B3;">%s %s<br> %s %s</p>',
			$strLabel,
			$arrValues[3],
			$GLOBALS['TL_LANG']['tl_cloud_mount']['mountLocal'], $arrValues[1],
			$GLOBALS['TL_LANG']['tl_cloud_mount']['mountCloud'], $arrValues[2]
		);
	}
	
	
	/**
	 * 
	 */
	public function generateGoToButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes)
	{
		$objResult = \FilesModel::findOneById($arrRow['localId']);

		return sprintf
		(
			'<a href="%s" title="%s" %s onclick="Backend.getScrollOffset()">%s</a> ',
			'contao/main.php?'.$strHref.urlencode($objResult->path), specialchars($strTitle), $strAttributes, $this->generateImage($strIcon, $strLabel)
		);
	}
}