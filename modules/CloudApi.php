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
 
namespace Netzmacht\Cloud\Api\Module;
use Netzmacht\Cloud\Api;
use BackendModule;
use BackendTemplate;


/**
 * cloud api module for handling cloud api specific views
 */
class CloudApi extends BackendModule
{
	
	/**
	 * 
	 */
	protected $arrMessages = array();
	
	/**
	 * constructor load language file
	 */
	public function __construct($objDc=null)
	{
		parent::__construct($objDc);
		
		$this->loadLanguageFile('cloudapi');
		$this->import('BackendUser', 'User');
	}
	
	
	/**
	 * Parse the template
	 * @return string
	 */
	public function generate()
	{
		if($this->Template === null)
		{
			$this->Template = new BackendTemplate($this->strTemplate);
		}
		
		$this->compile();

		return $this->Template->parse();
	}
	
	
	/**
	 * generate the install api view
	 * 
	 * @return string
	 */
	public function generateInstallApi()
	{
		if(!$this->User->isAdmin)
		{
			$this->log('User "%s" tried to access ModuleCloudApi generateCloudSync()', $this->User->id, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
			return '';
		}
		
		$this->Template = new BackendTemplate('be_cloudapi_install');
		
		$strApi = \Input::post('cloudapi');
		
		if($strApi != '')
		{
			Api\CloudApiManager::installApi($strApi);
			$this->redirect('contao/main.php?do=cloudapi');
			return;
		}
		
		$arrAttributes = array
		(
			'cloudApiMode' => 0,
			'name' => 'cloudapi',
			'label' => $GLOBALS['TL_LANG']['tl_cloud_api']['label'],
		);
		
		$objSelectMenu = new Api\Widget\ApiSelectMenu($arrAttributes);
		
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_cloud_api']['headline'];
		$this->Template->explain = $GLOBALS['TL_LANG']['tl_cloud_api']['explain'];
		$this->Template->selectMenu = $objSelectMenu;
		$this->Template->submit = $GLOBALS['TL_LANG']['tl_cloud_api']['install'];
		
		return $this->generate();
	}
	
	
	/**
	 * generate the view for cloud api syncing
	 * 
	 * @return string
	 */
	public function generateCloudSync()
	{		
		try 
		{
			$intId = \Input::get('id');
			$objCloudApi = Api\CloudApiManager::getApi($intId, 'id');			
		}
		catch(\Exception $e)
		{
			$this->log('Could not initiate CloudAPI for "' . $strTable . '"', 'DC_CloudNode __construct()', TL_ERROR);
			trigger_error('Could not initiate CloudAPI', E_USER_ERROR);			
		}
		
		// use mount manager as listener to changes will be directly passed
		$objMountManager = new Api\CloudMountManager();
		$objMountManager->registerSyncListener($this, 'syncListener');
		
		$objCloudApi->registerSyncListener($this, 'syncListener');
		$objCloudApi->registerSyncListener($objMountManager, 'syncListener');
		$objCloudApi->sync();
				
		$this->Template = new BackendTemplate('be_cloudapi_sync');
		$this->Template->headline = sprintf($GLOBALS['TL_LANG']['cloudapi']['cloudSyncHeadline'], $objCloudApi->title);
		$this->Template->messages = $this->arrMessages;
		$this->Template->href = $this->getReferer(true);
		$this->Template->hrefLabel = $GLOBALS['TL_LANG']['MSC']['continue'];
		
		return $this->generate();		
	}
	
	
	/**
	 * generate the mount view
	 * 
	 * @return string
	 */
	public function generateMountSync()
	{		
		$intId = \Input::get('id'); 
		$intStep = \Input::get('step', 0);
		
		$this->import('Session');
		
		$this->arrMessages = $this->Session->get('syncMessages');
		
		if($intStep == 0)
		{
			$this->arrMessages = array();
		}
		
		$objManager = new Api\CloudMountManager();
		$objManager->registerSyncListener($this, 'syncListener');
		
		// has more
		if($objManager->sync($intId))
		{
			$this->Session->set('syncMessages', $this->arrMessages);
			$this->redirect($this->addToUrl('step=' . ($intStep+1)));
			return;
		}
				
		$this->Template = new BackendTemplate('be_cloudapi_sync');
		$this->Template->headline = sprintf($GLOBALS['TL_LANG']['cloudapi']['mountSyncHeadline'], $intId);
		$this->Template->messages = $this->arrMessages;
		$this->Template->href = $this->getReferer(true);
		$this->Template->hrefLabel = $GLOBALS['TL_LANG']['MSC']['continue'];
		
		return $this->generate();
	}
	
	
	/**
	 * generate the sync view
	 * 
	 * @return string
	 */
	public function generateSyncOverview()
	{	
		// set session data
		$this->import('Session');
		$session = $this->Session->get('referer');
		
		if(\Environment::get('requestUri') != $session['current'])
		{
			$session['last'] = $session['current'];			
		}
		$session['current'] = \Environment::get('requestUri');
		$this->Session->set('referer', $session);
		
		$arrGroups = array();
		
		// get Contao file manager
		$this->import('BackendUser', 'User');
		
		if($this->User->isAdmin)
		{
			$arrGroup = array();
			$arrGroup['headline'] = $GLOBALS['TL_LANG']['cloudapi']['syncLocalFiles'][0];
			$arrGroup['description'] = $GLOBALS['TL_LANG']['cloudapi']['syncLocalFiles'][1];
			
			$arrGroup['data'] = array
			(	
				array
				(
					'title' 		=> $GLOBALS['TL_LANG']['cloudapi']['syncLocalFiles'][2],
					'description'	=> $GLOBALS['TL_LANG']['cloudapi']['syncLocalFiles'][3],
					'sync' 			=> isset($GLOBALS['TL_CONFIG']['cloudapiFileManagerIntegration']) ? $this->generateLastSyncLabel($GLOBALS['TL_CONFIG']['filemangerSynced']) : '',
					'href'			=> 'contao/main.php?do=files&act=sync&rt=' . REQUEST_TOKEN,
				)
			);
			
			$arrGroups[] = $arrGroup;
		}
		
		// get all enabled cloud apis
		$arrApis = Api\CloudApiManager::getApis();
		
		if($arrApis !== null)
		{
			$arrGroup = array();

			$arrGroup['headline'] = $GLOBALS['TL_LANG']['cloudapi']['syncCloudApis'][0];
			$arrGroup['description'] = $GLOBALS['TL_LANG']['cloudapi']['syncCloudApis'][1];
			$arrGroup['data'] = array();
			
			foreach ($arrApis as $arrData) 
			{
				$blnSync = (time() - $arrData['syncTstamp']) > $GLOBALS['TL_CONFIG']['cloudapiSyncInterval'];
				
				$arrGroup['data'][] = array
				(
					'title' 		=> $arrData['title'],
					'description'	=> sprintf($GLOBALS['TL_LANG']['cloudapi']['syncCloudApis'][2], $arrData['title']),
					'sync' 			=> $this->generateLastSyncLabel($arrData['syncTstamp']),
					'href'			=> $blnSync ? 'contao/main.php?do=cloudapi&key=sync&id=' . $arrData['id'] . '&rt=' . REQUEST_TOKEN : null,
				);
			}
			
			$arrGroups[] = $arrGroup;
		}
		
		// get all cloud mounts
		$objMount = \CloudMountModel::findByEnabled('1');
		
		if($objMount !== null)
		{
			$arrGroup = array();
			$arrGroup['headline'] = $GLOBALS['TL_LANG']['cloudapi']['syncMounts'][0];
			$arrGroup['description'] = $GLOBALS['TL_LANG']['cloudapi']['syncMounts'][1];
			$arrGroup['data'] = array();
			
			$this->import('Database');
			
			while ($objMount->next()) 
			{
				$arrOptions = unserialize($objMount->options);
				
				$blnSync = (time() - $objMount->syncTstamp) > $GLOBALS['TL_CONFIG']['cloudapiSyncInterval'];

				$arrGroup['data'][] = array
				(
					'title' 		=> $objMount->name,
					'description'	=> $objMount->description,
					'sync' 			=> $this->generateLastSyncLabel($objMount->syncTstamp),
					'href'			=> $blnSync ? 'contao/main.php?do=cloudapi&key=mount&id=' . $objMount->id . '&rt=' . REQUEST_TOKEN : null,
				);				
			}
			
			$arrGroups[] = $arrGroup;
		}
		
		$this->Template = new BackendTemplate('be_cloudapi_overview');
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_cloud_api']['sync'][1];
		$this->Template->groups = $arrGroups;
		$this->Template->syncedLabel = $GLOBALS['TL_LANG']['cloudapi']['syncedLabel'];
		$this->Template->syncLabel = $GLOBALS['TL_LANG']['cloudapi']['syncLabel'];
		$this->Template->lastSyncLabel = $GLOBALS['TL_LANG']['cloudapi']['lastSyncLabel'];
				
		return $this->generate();
	}


	/**
	 * log sync messages for the sync listener
	 * 
	 * @param string message
	 * @param string path
	 * @param string type
	 * @param bool create system log
	 */
	public function syncListener($strAction, $mixedNodeOrPath, $strMessage=null, $objApi=null)
	{
		$strPath = is_string($mixedNodeOrPath) ? $mixedNodeOrPath : $mixedNodeOrPath->path;
		
		switch ($strAction) 
		{
			case 'create':
				$strAction = 'new';
				break;
				
			case 'update':
				$strAction = 'info';
				break;
				
			case 'delete':
			case 'reset':
				$strAction = 'error'; 			
				break;
			
			default:
				return;
				break;
		}

		$strClass = 'tl_' . $strAction;
		$this->arrMessages[] = sprintf('<p class="%s">%s</p>', $strClass, ($strPath === null ? $strMessage : sprintf($strMessage, $strPath)));
	}
	
	
	/**
	 * compile used for setting general template vars
	 * 
	 * @return void
	 */
	protected function compile()
	{
		$this->Template->href = $this->getReferer(true);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		$this->Template->message = \Message::generate();
	}
	
	
	/**
	 * generate last sync label
	 * 
	 * @param int timestamp
	 */
	protected function generateLastSyncLabel($intTimestamp)
	{
		if($intTimestamp == 0)
		{
			return $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['never'];
		}
		
		$intTimestamp = time() - $intTimestamp;
		
		// last 20 seconds
		if($intTimestamp < 20)
		{
			return $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['now'];
		}

		// last hour
		elseif($intTimestamp < 3600)
		{
			$intValue = ceil($intTimestamp / 60);
			$intvalue = ($intValue == 0) ? 1 : $intValue; 
			$strLabel = $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['minute' . (($intValue > 1)  ? 's' : '')];
		}

		// last 24 hours
		elseif($intTimestamp < 86400)
		{
			$intValue = floor($intTimestamp / 3600);
			$strLabel = $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['hour' . (($intValue > 1)  ? 's' : '')];
		}

		// last 30 days
		elseif($intTimestamp < 2592000)
		{
			$intValue = floor($intTimestamp / 86400);
			$strLabel = $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['day' . (($intValue > 1)  ? 's' : '')];
		}

		// last year
		elseif($intTimestamp < 31536000)
		{
			$intValue = floor($intTimestamp / 2592000);
			$strLabel = $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['month' . (($intValue > 1)  ? 's' : '')]; 
		}
		
		else
		{
			$intValue = floor($intTimestamp / 31536000);
			$strLabel = $GLOBALS['TL_LANG']['cloudapi']['timeLabel']['year' . (($intValue > 1)  ? 's' : '')]; 
		}
		
		return sprintf($strLabel, $intValue);	
	}

}
