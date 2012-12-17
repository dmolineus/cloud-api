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
 
$GLOBALS['TL_LANG']['tl_settings']['cloudapi_legend'] 						= 'Cloud API';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncInterval'][0] 				= 'Sync interval';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncInterval'][1]				= 'Please decide which sync interval is used';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['600'] 	= '10 minutes';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['1800'] 	= '30 minutes';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['3600'] 	= '1 hour';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['7200'] 	= '2 hours';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['86400'] 	= '1 day';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['604800'] = '1 week';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadTime'][0] 			= 'Limit download time';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadTime'][1] 			= 'Duration of downloads in seconds. During the mount syncing the duration will be checked'
																			. ' to avoid php execution limits. There will be a redirect an the sync goes on.';
																	
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadLimit'][0] 			= 'Number of downloaded files';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadLimit'][1] 			= 'Please decide how many files can be downloaded to avoid php execution limit';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerIntegration'][0] 	= 'Integrates into the file system';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerIntegration'][1] 	= 'You can use a file system view where you can see which folders are mounted.'
																			. ' Besides the search overview will used as well.';

$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerManageMounts'][0] 	= 'Manage mount points in the file manager';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerManageMounts'][1] 	= 'You can enable/disable the manage mount points button in the file system view.';