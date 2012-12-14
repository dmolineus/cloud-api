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


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Netzmacht\Cloud\Api;
use SelectMenu;

/**
 * Class SelectMenu
 *
 * Provide methods to handle select menus.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://contao.org>
 * @package    Core
 */
class CloudApiSelectMenu extends SelectMenu
{
	
	/**
	 * override options with registered cloud settings
	 * 
	 * @param array
	 * @return void
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);
		
		// override options
		//$this->arrOptions = array();
		
		if($this->includeBlankOption)
		{
			$this->arrOptions[] = array('value'=>'', 'label'=>'-');
		}
		
		$intMode = ($this->cloudApiMode === null ? 2 : $this->cloudApiMode);

		$arrApis = CloudApiManager::getApis($intMode);

		foreach ($arrApis as $strKey => $arrValue) 
		{
			$this->arrOptions[$strKey] = $arrValue['title'] = array(
				'value' => $strKey,
				'label' => isset($GLOBALS['TL_LANG']['MOD']['cloudapi_' . $strKey][0]) 
					? $GLOBALS['TL_LANG']['MOD']['cloudapi_' . $strKey][0]
					: $arrValue['title']      
			);
		}
	}
}
