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
 * integrate cloud api in the system file manager by adding buttons and a customized data container for creating
 * the mounted folders
 */
if($GLOBALS['TL_CONFIG']['cloudapiFileManagerIntegration'])
{
	$GLOBALS['TL_DCA']['tl_files']['config']['dataContainer'] = 'CloudMountedFolder';
	
	$GLOBALS['TL_DCA']['tl_files']['list']['global_operations']['sync']['href'] = 'do=cloudapi&key=overview'; 
	$GLOBALS['TL_DCA']['tl_files']['list']['global_operations']['sync']['button_callback'] = array('Netzmacht\Cloud\Api\DataContainer\Files', 'generateGlobalButtonSync');
	$GLOBALS['TL_DCA']['tl_files']['list']['global_operations']['sync']['button_rules'] = array('hasAccess:module=cloudapi', 'generate');		
	
	if($GLOBALS['TL_CONFIG']['cloudapiFileManagerManageMounts'])
	{
		$GLOBALS['TL_DCA']['tl_files']['list']['global_operations']['cloudapiMount'] = array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_files']['cloudapiMount'],
			'href'                => 'do=cloudapi&table=tl_cloud_mount',
			'class'               => 'header_mount',
			'button_callback'     => array('Netzmacht\Cloud\Api\DataContainer\Files', 'generateGlobalButtonCloudapiMount'),
			'button_rules'	  => array('isAdmin', 'lineBreak', 'generate'),
		);
	}
	
	// stylesheet insert for icons
	$GLOBALS['TL_CSS'][] = 'system/modules/cloud-api/assets/style.css';
}
