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
	'Netzmacht\Cloud\Api\CloudCache' => 'system/modules/cloud-api/classes/CloudCache.php',
	'Netzmacht\Cloud\Api\CloudApi' => 'system/modules/cloud-api/classes/CloudApi.php',
	'Netzmacht\Cloud\Api\CloudMountManager' => 'system/modules/cloud-api/classes/CloudMountManager.php',
	
	// Models
	'CloudNodeModel' => 'system/modules/cloud-api/models/CloudNodeModel.php',
	'CloudMountModel' => 'system/modules/cloud-api/models/CloudMountModel.php',
	'Netzmacht\Cloud\Api\CloudNodeModelCollection' => 'system/modules/cloud-api/models/CloudNodeModelCollection.php',
	
	// modules
	'Netzmacht\Cloud\Api\ModuleCloudApi' => 'system/modules/cloud-api/modules/ModuleCloudApi.php',
	
	// widgets
	'Netzmacht\Cloud\Api\CloudApiSelectMenu' => 'system/modules/cloud-api/widgets/CloudApiSelectMenu.php',	
	'Netzmacht\Cloud\Api\CloudFileTree' => 'system/modules/cloud-api/widgets/CloudFileTree.php',
	'Netzmacht\Cloud\Api\CloudFileSelector' => 'system/modules/cloud-api/widgets/CloudFileSelector.php',
	'Netzmacht\Cloud\Api\RequestAccessToken' => 'system/modules/cloud-api/widgets/RequestAccessToken.php',
	
	// drivers
	'DC_CloudNode' => 'system/modules/cloud-api/drivers/DC_CloudNode.php',
	'DC_CloudMountedFolder' => 'system/modules/cloud-api/drivers/DC_CloudMountedFolder.php',
	
	// data containers 
	'Netzmacht\Utils\DataContainer' => 'system/modules/cloud-api/classes/DataContainer.php',
	'Netzmacht\Cloud\Api\DataContainer\Files' => 'system/modules/cloud-api/DataContainer/Files.php',
	'Netzmacht\Cloud\Api\DataContainer\CloudApi' => 'system/modules/cloud-api/DataContainer/CloudApi.php',
	'Netzmacht\Cloud\Api\DataContainer\CloudMount' => 'system/modules/cloud-api/DataContainer/CloudMount.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFile('be_cloudapi_install', 'system/modules/cloud-api/templates');
TemplateLoader::addFile('be_cloudapi_sync', 'system/modules/cloud-api/templates');
TemplateLoader::addFile('be_cloudapi_mount', 'system/modules/cloud-api/templates');
