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
 
// sync
$GLOBALS['TL_LANG']['tl_cloud_api']['sync'][0] = 'Sync';
$GLOBALS['TL_LANG']['tl_cloud_api']['sync'][1] = 'Sync cloud service and database';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncStart'] = 'Sync of cloud service "%s" started.';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncStop'] = 'Sync of cloud service "%s" finished.';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncReset'] = 'Reset forced. Deleted files of the cloud service were removed from the database as well.';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncFound'] = 'Found the original file "%s" at "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncRemoved'] = 'Removed the file or folder "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncFolderC'] = 'Created the folder "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncFolderF'] = 'Found the folder "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncFileC'] = 'Created the file "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncFileF'] = 'Found the file "%s"';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncHash'] = 'Updated the hash of the file "%s"';

// install
$GLOBALS['TL_LANG']['tl_cloud_api']['headline'] = 'Install cloud service';
$GLOBALS['TL_LANG']['tl_cloud_api']['label'] = 'Choose cloud service';
$GLOBALS['TL_LANG']['tl_cloud_api']['explain'] = 'Please choose the cloud service you want to install';
$GLOBALS['TL_LANG']['tl_cloud_api']['install'] = 'install';
$GLOBALS['TL_LANG']['tl_cloud_api']['installService'] = $GLOBALS['TL_LANG']['tl_cloud_api']['headline'];

// legends
$GLOBALS['TL_LANG']['tl_cloud_api']['connection_legend'] = 'General Settings';
$GLOBALS['TL_LANG']['tl_cloud_api']['folder_legend'] = 'Mount folders';
$GLOBALS['TL_LANG']['tl_cloud_api']['custom_legend'] = 'Chang cloud service settings';

// buttons
$GLOBALS['TL_LANG']['tl_cloud_api']['delete'] = array('deinstall %s', 'deinstall cloud service %s');
$GLOBALS['TL_LANG']['tl_cloud_api']['edit'] = array('edit %s', 'Edit settings of cloud service');

// fields
$GLOBALS['TL_LANG']['tl_cloud_api']['name'][0] = 'Cloud service';
$GLOBALS['TL_LANG']['tl_cloud_api']['name'][1] = 'Name of the cloud service';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessTokenLink'] = 'Request token. Pleas reload after that.';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessTokenConnected'] = 'connected with:';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'][0] = 'Access token';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'][1] = 'You have to agree the access at the page of the cloud service. Follow the link and the instructions. Then reload this page please.';
$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'][0] = 'Enalbe';
$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'][1] =  'Enable cloud service to use it.';
$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'][0] = 'Mount folder';
$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'][1] = 'Usually all folders are mounted and sync. You can limit the access by setting folders on in each row. example /folder';
$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'][0] = 'Use custom App settings';
$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'][1] = 'Depending on the clous service you can customize the settings. Please only change it if it\'s required.';
$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'][0] = 'App key';
$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'][1] = 'Please insert the key of the App.';
$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'][0] = 'App Secret';
$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'][1] = 'Please insert the secret of the App.';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'][0] = 'Last sync';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'][1] = 'Date of the last sync of the cloud service';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'][0] = 'Sync in progress';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'][1] = 'Sync is in progress at the moment';
$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'][0] = 'oAuth implementation';
$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'][1] = 'The cloud service provides different oAuth implementations. Please choose one your server support.';
$GLOBALS['TL_LANG']['tl_cloud_api']['root'][0] = 'Root directory';
$GLOBALS['TL_LANG']['tl_cloud_api']['root'][1] = 'The cloud service provides different root modes. Dropbox for instance, can work in a sandbox. But then you have to use custom app settings.';
