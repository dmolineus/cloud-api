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
 
$GLOBALS['TL_DCA']['tl_cloud_api'] = array
(
	'config' => array
	(
		'dataContainer' => 'Table',
		'doNotCopyRecords' => true,
		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'name' => 'unique',
			)
		),
		
		'closed' => true,
		
		'onload_callback' => array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'checkPermission'),
			array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'choosePalette'),
		),
		
		'ondelete_callback' => array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'deleteNodesAndMounts'),
		),
		
		'palettes_callback' => array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'chooseSubpalettes'),			
		),
		
		'permission_rules' => array('isAdmin:act=[delete,edit,editAll,select]:key=install'),
	),
	
	'list' => array
	(
		'sorting' => array
		(
			'mode'						=> 1,
			'fields'					=> array('title'),
			'flag'						=> 1,
		),
		
		'label' => array
		(
			'fields' 					=> array('title', 'syncTstamp'),
			'showColumns' 				=> true,
			'label_callback' 			=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateLabel'),
			'label_rules' 				=> array('parseDate:index=1'),			
		),
		
		'global_operations' => array
		(
			'install' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['install'],
				'href'					=> 'key=install',
				'class'					=> 'header_new',
				'attributes'			=> 'onclick="Backend.getScrollOffset()"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateGlobalButtonInstall'),
				'button_rules'			=> array('isAdmin', 'installApi', 'generate'),
			),
			
			'mount' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['mount'],
				'href'					=> 'table=tl_cloud_mount',
				'class'					=> 'header_mount',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateGlobalButtonMount'),
				'button_rules'			=> array('isAdmin', 'generate'),
			),
			
			'overview' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['overview'],
				'href'					=> 'key=overview',
				'class'					=> 'header_sync',
			),
			
			'all' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset()" accesskey="e"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateGlobalButtonAll'),
				'button_rules'			=> array('isAdmin', 'generate'),
			),
		),
		
		'operations' => array
		(
			'edit' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['edit'],
				'href'					=> 'act=edit',
				'icon'					=> 'edit.gif',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateButtonEdit'),
				'button_rules'			=> array('isAdmin', 'generate'),
			),
			
			'enable' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['enable'],
				'icon'					=> 'visible.gif',
				'attributes'          	=> 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateButtonEnable'),
				'button_rules'			=> array('isAdmin', 'toggleIcon:field=enabled', 'generate'),
			),
			
			'reset' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['reset'],
				'href'					=> 'key=reset',
				'icon'					=> 'system/modules/cloud-api/assets/reset.png',
				'attributes'			=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['cloudResetConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateButtonReset'),
				'button_rules'			=> array('isAdmin', 'generate'),
			),

			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudApi', 'generateButtonDelete'),
				'button_rules'			=> array('isAdmin', 'generate'),
			),
			
			'show' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['show'],
				'href'					=> 'act=show',
				'icon'					=> 'show.gif',
				
			),
		)
	),
	
	'metapalettes' => array
	(
		'_base_' => array
		(
			'connection' 				=> array('title', 'enabled'),
			'folder'					=> array(':hide,', 'mountedFolders')
			
		),
		
		'default extends _base_' 		=> array(), 	
	),
	
	'fields' => array
	(
		'id' => array
		(
			'sql' 						=> 'int(10) unsigned NOT NULL auto_increment',
		),
		
		'tstamp' => array
		(
			'sql' 						=> "int(10) unsigned NOT NULL default '0'",
		),
		
		'name' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['name'],
			'exclude' 					=> true,
			'sql' 						=> "varchar(255) NOT NULL default ''",
		),
		
		'title' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['title'],
			'inputType' 				=> 'text',
			'exclude' 					=> true,
			'eval' 						=> array('readonly' => true, 'disabled' => true, 'tl_class' => 'w50'),
			'sql' 						=> "varchar(255) NOT NULL default ''",
		),
		
		'enabled' => array
		(			
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'],
			'inputType'					=> 'checkbox',
			'exclude' 					=> true,
			'eval' 						=> array('submitOnChange'=>true, 'tl_class' => 'w50 m12', 'isBoolean' => true),
			'sql' 						=> "char(1) NOT NULL default ''",
		),
		
		'oAuthClass' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'],
			'inputType' 				=> 'select',
			'exclude' 					=> true,
			'options' 					=> array('Curl', 'PEAR', 'PHP'),	
			'eval' 						=> array('mandatory'=>true, 'nospace'=>'true', 'tl_class'=>'w50'),
			'sql' 						=> "varchar(10) NOT NULL default ''",
		),
		
		'accessToken' => array
		(
			'label' 					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'],
			'inputType' 				=> 'accessToken',
			'exclude' 					=> true,
			'eval'						=> array('nospace'=>'true', 'cloudApiField' => 'name', 'tl_class' => 'w50', 'doNotShow' => true),		
			'sql' 						=> "blob NULL",
		),
		
		'mountedFolders' => array
		(
			'label' 					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'],
			'inputType' 				=> 'listWizard',
			'exclude' 					=> true,
			'eval' 						=> array('nospace'=>'true', 'tl_class' => 'clr'),
			'sql' 						=> "blob NULL",
		),
		
		'useCustomApp' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'],
			'inputType' 				=> 'checkbox',
			'exclude' 					=> true,
			'eval' 						=> array('submitOnChange'=>true),
			'sql' 						=> "char(1) NOT NULL default ''",
		),
		
		'appKey' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'],
			'inputType'					=> 'text',
			'exclude' 					=> true,
			'eval' 						=> array('mandatory'=>true, 'tl_class'=>'w50', 'doNotShow' => true),
			'sql' 						=> "varchar(255) NOT NULL default ''",
		),
		
		'appSecret' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'],
			'inputType'					=> 'text',
			'exclude' 					=> true,
			'eval' 						=> array('mandatory'=>true, 'tl_class'=>'w50', 'doNotShow' => true),
			'sql' 						=> "varchar(255) NOT NULL default ''",
		),
		
		'root' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_api']['root'],
			'inputType'					=> 'select',
			'exclude' 					=> true,
			'options'					=> array('dropbox', 'sandbox'),
			'eval'						=> array('mandatory'=>true, 'tl_class'=>'w50')
		),
		
		'syncTstamp' => array
		(
			'label' 					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'],
			'sql' 						=> "int(10) unsigned NOT NULL default '0'",
			'eval' 						=> array('rgxp' => 'datim'),
		),
		
		'syncInProgress' => array
		(
			'label' 					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'],
			'sql' 						=> "char(1) NOT NULL default '0'",
		),
		
		'deltaCursor' => array
		(
			'eval' 						=> array('doNotShow' => true),
			'sql' 						=> "blob NULL",
		),
	),	
);
