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
 
namespace Netzmacht\Cloud\Api;
use BackendModule;

/**
 * cloud api module for handling install process
 * 
 */
class ModuleCloudApi extends BackendModule
{
	
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_cloudapi_install';
	
	
	public function generate()
	{
		$this->headline = 'as';
		
		$strAct = \Input::get('act');
		
		switch ($strAct) 
		{
			case 'edit':
			case 'editAll':		
			case 'delete':
			case 'show':
			case 'sync':
				return $this->objDc->$strAct();
				break;
				
			case 'install':
				return parent::generate();
				break;
			
			default:
				return $this->objDc->showAll();		
				break;
		}				
	}
	
	
	/**
	 * compile will handle install request
	 * 
	 */
	protected function compile()
	{
		$strApi = \Input::post('cloudapi');
	
		if($strApi != '')
		{
			CloudApiManager::installApi($strApi);
			$this->redirect('contao/main.php?do=cloudapi');
			return;
		}
		
		$arrApis =  CloudApiManager::getApis(0);
		
		foreach($arrApis as $strKey => $arrValue)
		{
			if(isset($arrValue['enabled']))
			{
				unset($arrApis[$strKey]);				
			}
		}
		
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_cloud_api']['headline'];
		$this->Template->label = $GLOBALS['TL_LANG']['tl_cloud_api']['label'];
		$this->Template->explain = $GLOBALS['TL_LANG']['tl_cloud_api']['explain'];
		$this->Template->apis = $arrApis;
		$this->Template->submit = $GLOBALS['TL_LANG']['tl_cloud_api']['install'];
		
		$this->Template->href = $this->getReferer(true);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		$this->Template->message = \Message::generate();	
	}
}