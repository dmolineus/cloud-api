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
 * It is nessesary to load localconfig.php for the cloud apis to access enabling
 * state. Load it at this place so not every cloud api has to do it
 */
require TL_ROOT . '/system/config/localconfig.php';
 
/**
 * register assets
 */
if(TL_MODE == 'BE')
{ 
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/cloud-api/assets/AjaxRequest.js';
}

/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['accesToken'] = 'Netzmacht\Cloud\Api\RequestAccessToken';
$GLOBALS['BE_FFL']['cloudFileTree'] = 'Netzmacht\Cloud\Api\CloudFileTree';
$GLOBALS['BE_FFL']['cloudFileSelector'] = 'Netzmacht\Cloud\Api\CloudFileSelector';
$GLOBALS['BE_FFL']['cloudApiSelect'] = 'Netzmacht\Cloud\Api\CloudApiSelectMenu';


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
	'affected' => array('system/cache/cloud-api')
);
