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
 * default cloudapi config
 */
$GLOBALS['TL_CONFIG']['cloudapiFileManagerIntegration'] = true;
$GLOBALS['TL_CONFIG']['cloudapiSyncDownloadTime'] = 15;
$GLOBALS['TL_CONFIG']['cloudapiSyncDownloadLimit'] = 5;
$GLOBALS['TL_CONFIG']['cloudapiSyncInterval'] = 600;
$GLOBALS['TL_CONFIG']['cloudapiNavigationPosition'] = 1;
$GLOBALS['TL_CONFIG']['cloudapiFileManagerManageMounts'] = false;

 
/**
 * backend module
 */
array_insert($GLOBALS['BE_MOD']['system'], $GLOBALS['TL_CONFIG']['cloudapiNavigationPosition'], array
(
	'cloudapi' =>array 
	(
		'tables' 		=> array('tl_cloud_api', 'tl_cloud_node', 'tl_cloud_mount'),
		'icon'       	=> 'system/modules/cloud-api/assets/drive_web.gif',
		'stylesheet' 	=> 'system/modules/cloud-api/assets/style.css',
		'install' 		=> array('Netzmacht\Cloud\Api\Module\CloudApi', 'generateInstallApi'),
		'mount' 		=> array('Netzmacht\Cloud\Api\Module\CloudApi', 'generateMountSync'),
		'sync' 			=> array('Netzmacht\Cloud\Api\Module\CloudApi', 'generateCloudSync'),
		'overview'		=> array('Netzmacht\Cloud\Api\Module\CloudApi', 'generateSyncOverview'),
	)
));


/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['accessToken'] = 'Netzmacht\Cloud\Api\Widget\RequestAccessToken';
$GLOBALS['BE_FFL']['cloudFileTree'] = 'Netzmacht\Cloud\Api\Widget\CloudFileTree';
$GLOBALS['BE_FFL']['cloudFileSelector'] = 'Netzmacht\Cloud\Widget\Api\CloudFileSelector';
$GLOBALS['BE_FFL']['cloudApiSelect'] = 'Netzmacht\Cloud\Api\Widget\CloudApiSelectMenu';


/**
 * Register Hooks
 */
$GLOBALS['TL_HOOKS']['executePreActions'][] = array('Netzmacht\Cloud\Api\AjaxRequest', 'executePreActions');
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Netzmacht\Cloud\Api\AjaxRequest', 'executePostActions');


/**
 * register purge jobs of maintenance 
 */
$GLOBALS['TL_PURGE']['folders']['cloud-api'] = array (
	'callback' => array('Netzmacht\Cloud\Api\CloudCache', 'purgeCache'),
	'affected' => array()
);


/**
 * register assets
 */
if(TL_MODE == 'BE')
{ 
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/cloud-api/assets/AjaxRequest.js';
}


/**
 * font awesome icons
 */
$GLOBALS['ICON_REPLACER']['navigation']['styleIcons'][] = array('cloud', 'cloudapi');
$GLOBALS['ICON_REPLACER']['context']['imageIcons'][] = array('refresh', 'sync.gif');
$GLOBALS['ICON_REPLACER']['buttons']['styleIcons'][] = array('retweet', 'header_mount');
$GLOBALS['ICON_REPLACER']['context']['imageIcons'][] = array('folder-open', 'mount.png');
