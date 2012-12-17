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
	// interfaces
	'Netzmacht\Cloud\Api\syncListenable' => 'system/modules/cloud-api/syncListenable.php',
	
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
	'Netzmacht\Cloud\Api\Module\CloudApi' => 'system/modules/cloud-api/modules/CloudApi.php',
	
	// widgets
	'Netzmacht\Cloud\Api\Widget\ApiSelectMenu' => 'system/modules/cloud-api/widgets/ApiSelectMenu.php',	
	'Netzmacht\Cloud\Api\Widget\FileTree' => 'system/modules/cloud-api/widgets/FileTree.php',
	'Netzmacht\Cloud\Api\Widget\FileSelector' => 'system/modules/cloud-api/widgets/FileSelector.php',
	'Netzmacht\Cloud\Api\Widget\RequestAccessToken' => 'system/modules/cloud-api/widgets/RequestAccessToken.php',
	
	// drivers
	'DC_CloudMountedFolder' => 'system/modules/cloud-api/drivers/DC_CloudMountedFolder.php',
	
	// data containers 
	//'Netzmacht\Utils\DataContainer' => 'system/modules/cloud-api/classes/DataContainer.php',
	'Netzmacht\Cloud\Api\DataContainer\CloudApi' => 'system/modules/cloud-api/DataContainer/CloudApi.php',
	'Netzmacht\Cloud\Api\DataContainer\CloudMount' => 'system/modules/cloud-api/DataContainer/CloudMount.php',
	'Netzmacht\Cloud\Api\DataContainer\CloudNode' => 'system/modules/cloud-api/DataContainer/CloudNode.php',
	'Netzmacht\Cloud\Api\DataContainer\Files' => 'system/modules/cloud-api/DataContainer/Files.php',
	'Netzmacht\Cloud\Api\DataContainer\Settings' => 'system/modules/cloud-api/DataContainer/Settings.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_cloudapi_install' => 'system/modules/cloud-api/templates',
	'be_cloudapi_overview' => 'system/modules/cloud-api/templates',
	'be_cloudapi_sync' => 'system/modules/cloud-api/templates',
));
