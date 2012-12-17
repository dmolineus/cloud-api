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
 
// mount
$GLOBALS['TL_LANG']['tl_cloud_mount']['mountLocal'] 			= 'local:';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mountCloud'] 			= 'cloud:';

// legends
$GLOBALS['TL_LANG']['tl_cloud_mount']['general_legend']			= 'General settings';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mounts_legend']		 	= 'Define mount points';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_legend']			= 'Sync options';

// buttons
$GLOBALS['TL_LANG']['tl_cloud_mount']['create'] 				= array('Create mount point', 'Create a new mount point');
$GLOBALS['TL_LANG']['tl_cloud_mount']['delete'] 				= array('Delete mount point', 'Delete mount point with ID "%s"');
$GLOBALS['TL_LANG']['tl_cloud_mount']['edit']					= array('Edit mount point', 'Edit mount point with ID "%s"');
$GLOBALS['TL_LANG']['tl_cloud_mount']['copy'] 					= array('Copy mount point', 'Copy mount point with ID "%s"');
$GLOBALS['TL_LANG']['tl_cloud_mount']['goto'] 					= array('Go to file manager', 'Go to the linked file manager folder');
$GLOBALS['TL_LANG']['tl_cloud_mount']['enable'] 				= array('Activate/deactivate mount pount', 'Activate/deactivate mount pount');

// fields
$GLOBALS['TL_LANG']['tl_cloud_mount']['name'][0]				= 'Name';
$GLOBALS['TL_LANG']['tl_cloud_mount']['name'][1]				= 'Name is used as internal identifer';
$GLOBALS['TL_LANG']['tl_cloud_mount']['description'][0]			= 'Description';
$GLOBALS['TL_LANG']['tl_cloud_mount']['description'][1]			= 'You can use a description so that you users will know what the mount point is for.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['pid'][0]					= 'Cloud Service';
$GLOBALS['TL_LANG']['tl_cloud_mount']['pid'][1]					= 'Please choose a cloud service for the mount point';
$GLOBALS['TL_LANG']['tl_cloud_mount']['enabled'][0]				= 'Activate';
$GLOBALS['TL_LANG']['tl_cloud_mount']['enabled'][1]				= 'You can deactive the mount point. So it is not displayed on the sync overview.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['cloudId'][0]				= 'Cloud service folder';
$GLOBALS['TL_LANG']['tl_cloud_mount']['cloudId'][1]				= 'Please choose a folder which is mounted into the file system';
$GLOBALS['TL_LANG']['tl_cloud_mount']['localId'][0]				= 'File system folder';
$GLOBALS['TL_LANG']['tl_cloud_mount']['localId'][1]				= 'Please choose a folder where the cloud folder is mounted';

$GLOBALS['TL_LANG']['tl_cloud_mount']['mode'][0]				= 'Sync direction';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode'][1]				= 'Please decide which sync directions you want to use.';

$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['c2l']		= 'Cloud to local';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['l2c']		= 'Local to cloud';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['s2w']		= 'both directions';

$GLOBALS['TL_LANG']['tl_cloud_mount']['options'][0]				= 'options';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options'][1]				= 'Please choose which operations shall be done by syncing between the Cloud and the local filesystem';

$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['create']	= 'Create files and folders';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['update']	= 'Update existing files';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['delete']	= 'Delete files and folders';
