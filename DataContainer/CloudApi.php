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
	 * @var bool
	 */
	protected $blnReset = false;
	
	/**
	 * @var Netzmacht\Cloud\Api\DataContainer\CloudApi
	 */
	protected static $objInstance = null;
	
	
	/**
	 * define a singleton because the class is used for datacontainer callbacks and meta palettes callbacks as well
	 * 
	 * @return Netzmacht\Cloud\Api\DataContainer\CloudApi
	 */
	public static function getInstance()
	{
		if(static::$objInstance === null)
		{
			static::$objInstance = new static();
		}
		
		return static::$objInstance;		
	}
	
	
	/**
	 * choose palette supports different palettes depending on cloud name
	 * 
	 * @return void  
	 */
	public function choosePalette()
	{
		try
		{
			$objApi = CloudApiManager::getApi(\Input::get('id'));			
		}
		catch(\Exception $e)
		{
			return;
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objApi->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objApi->name];
		}
	}
	
	
	/**
	 * choose subpaletttes using the palettes__callback because we have to do it before meta palettes renders the palettes
	 * subpalettes are stores in cloudapi_metasubselectpalettes['cloudname'].
	 * 
	 * @return void
	 */
	public function chooseSubpalettes()
	{
		try
		{
			$objApi = CloudApiManager::getApi(\Input::get('id'));			
		}
		catch(\Exception $e)
		{
			return;
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['cloudapi_metasubselectpalettes'][$objApi->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['metasubpalettes'] = $GLOBALS['TL_DCA']['tl_cloud_api']['cloudapi_metasubselectpalettes'][$objApi->name];
		}
	}
	
	
	/**
	 * lets delete all corresponding nodes and mounts
	 * we did not set up ptable, ctable relations because
	 * we want to display ALL mounts on one page => dont know any way how to do it with setting ptable 
	 * and we use pid in tl_cloud_node as parent node id like tl_files does
	 * 
	 * @param $objDc
	 */
	public function deleteNodesAndMounts($objDc)
	{
		$this->import('Database');
		
		$this->Database->prepare('DELETE FROM tl_cloud_node WHERE cloudapi=?')->execute($objDc->id);
		$this->Database->prepare('DELETE FROM tl_cloud_mount WHERE pid=?')->execute($objDc->id);		
	}
	
	
	/**
	 * check if mounted folders has changed so we need to reset the sync state
	 *  
	 */
	public function resetOnUpdate($mixedValue, $objDc)
	{
		$this->blnReset = ($mixedValue != $objDc->activeRecord->mountedFolders);		
		return $mixedValue;
	}
	
	
	/**
	 * reset sync state if blnReset was set to true
	 * 
	 */
	public function updateSyncState($objDc)
	{
		// Return if there is no active record (override all)
		if (!$objDc->activeRecord)
		{
			return;
		}
		
		if($this->blnReset)
		{
			$arrSet = array
			(
				'syncTstamp' => 0,
				'syncInProgress' => '',
				'deltaCursor' => null,
			);
			
			$this->import('Database');
			$this->Database->prepare('UPDATE tl_cloud_api %s WHERE id=?')->set($arrSet)->execute($objDc->id);
		}
	}


	/**
	 * hide reset button if sync state is already reset
	 * 
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	protected function buttonRuleCanReset(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, &$arrAttributes, $arrRow=null)
	{
		return ($arrRow['deltaCursor'] != null || $arrRow['syncTstamp'] != 0 || $arrRow['syncInProgress'] != '');
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
	protected function buttonRuleInstallApi(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, &$arrAttributes, $arrRow=null)
	{
		$arrApis = CloudApiManager::getApis(0);
		
		return (count($arrApis) > 0);
	}
	
}
