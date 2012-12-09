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
	'Netzmacht\Cloud\Api\AjaxRequest' => 'system/modules/cloud-api/classes/AjaxRequest.php',
	'Netzmacht\Cloud\Api\CloudApiManager' => 'system/modules/cloud-api/classes/CloudApiManager.php',
	'Netzmacht\Cloud\Api\CloudNode' => 'system/modules/cloud-api/classes/CloudNode.php',
	'Netzmacht\Cloud\Api\CloudCache' => 'system/modules/cloud-api/classes/CloudCache.php',
	'Netzmacht\Cloud\Api\CloudApi' => 'system/modules/cloud-api/classes/CloudApi.php',
	
	// Models
	'CloudapiNodeModel' => 'system/modules/cloud-api/models/CloudapiNodeModel.php',
	
	// widgets
	'Netzmacht\Cloud\Api\CloudApiSelectMenu' => 'system/modules/cloud-api/widgets/CloudApiSelectMenu.php',	
	'Netzmacht\Cloud\Api\CloudFileTree' => 'system/modules/cloud-api/widgets/CloudFileTree.php',
	'Netzmacht\Cloud\Api\CloudFileSelector' => 'system/modules/cloud-api/widgets/CloudFileSelector.php',
	'Netzmacht\Cloud\Api\RequestAccessToken' => 'system/modules/cloud-api/widgets/RequestAccessToken.php',	
));
