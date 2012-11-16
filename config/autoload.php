<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Cloud-api
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Netzmacht',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Netzmacht\Cloud\Api\AjaxRequest'     => 'system/modules/cloud-api/classes/AjaxRequest.php',
	'Netzmacht\Cloud\Api\CloudApiManager' => 'system/modules/cloud-api/classes/CloudApiManager.php',
	'Netzmacht\Cloud\Api\CloudNode'       => 'system/modules/cloud-api/classes/CloudNode.php',
	'Netzmacht\Cloud\Api\CloudCache'      => 'system/modules/cloud-api/classes/CloudCache.php',
	'Netzmacht\Cloud\Api\CloudApi'        => 'system/modules/cloud-api/classes/CloudApi.php',
	
    // widgets
    'Netzmacht\Cloud\Api\RequestAccessToken' => 'system/modules/cloud-api/widgets/RequestAccessToken.php',
    'Netzmacht\Cloud\Api\CloudFileTree' => 'system/modules/cloud-api/widgets/CloudFileTree.php',
    'Netzmacht\Cloud\Api\CloudFileSelector' => 'system/modules/cloud-api/widgets/CloudFileSelector.php',
));
