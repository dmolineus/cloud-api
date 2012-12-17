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
 * CloudAPI cloudapi_node
 */
$GLOBALS['TL_DCA']['tl_cloud_mount'] = array
(
	// Config
	'config' => array
	(
		'dataContainer' => 'Table',
		//'ptable' => 'tl_cloud_api',
		'switchToEdit' => true,
		//'label' => 'Pusteblume',
		'onload_callback' => array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'checkPermission'),
			array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'hideStandardCreateButton'),
		),
		'onsubmit_callback'	=> array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'clearTimestamp')
		),
		'permission_rules' => array('isAdmin'),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				'localId' => 'unique',
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'						=> 1,
			'fields'					=> array('pid', 'name'),
			'panelLayout'				=> 'filter,search,limit',
			'flag'						=> 1,
		),
		
		'label' => array
		(
		    'fields' => array('name', 'localId:tl_files.path', 'cloudId:tl_cloud_node.path', 'description'),
		    'label_callback' => array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'listMountedFolders'),
		),
		
		'global_operations' => array
		(
			'back' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['backBT'],
				'href'					=> '',
				'class'					=> 'header_back',
				'attributes'			=> 'onclick="Backend.getScrollOffset()" accesskey="b"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'generateGlobalButton'),
				'button_rules' 			=> array('referer', 'generate'),
			),
			
			'create' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['create'],
				'href'					=> 'act=create',
				'class'					=> 'header_new',
				'attributes'			=> 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
			
			'all' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
		),
		
		'operations' => array
		(
			'edit' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['edit'],
				'href'					=> 'act=edit',
				'icon'					=> 'edit.gif',
			),

			'goto' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['goto'],
				'href'               	=> 'do=files&node=',
				'icon'					=> 'system/modules/cloud-api/assets/mount.png',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'generateButton'),
				'button_rules'			=> array('fileManager', 'generate'),
			),
			
			'copy' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['copy'],
				'href'					=> 'act=copy',
				'icon'					=> 'copy.gif',
				'attributes'			=> 'onclick="Backend.getScrollOffset()"',
			),
			
			'enable' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['enable'],
				'icon'					=> 'visible.gif',
				'attributes'			=> 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'		=> array('Netzmacht\Cloud\Api\DataContainer\CloudMount', 'generateButton'),
				'button_rules'			=> array('toggleIcon:field=enabled', 'generate'),
			),
			
			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'					=> array('pid'),
		'default'						=> '{general_legend},name,description,enabled;{mounts_legend},pid;{options_legend},mode,options',
	),
	
	'metasubselectpalettes' => array
	(
		'pid' => array('!' => array('cloudId','localId')),
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'eval' 						=> array('unique' => true),
			'sql'						=> "int(10) unsigned NOT NULL auto_increment"
		),
		
		'pid' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['pid'],
			'foreignKey'				=> 'tl_cloud_api.title',
			'inputType'					=> 'select',
			'eval' 						=> array('submitOnChange' => true, 'includeBlankOption' => true, 'mandatory' => true),
			'sql'						=> "int(10) unsigned NULL",
			'relation'					=> array('type'=>'belongsTo', 'load'=>'lazy')
		),
		
		'tstamp' => array
		(
			'sql'						=> "int(10) unsigned NOT NULL default '0'"
		),
		
		'name' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['name'],
			'inputType'					=> 'text',
			'eval' 						=> array('mandatory'=>true, 'tl_class' => 'w50'),
			'sql'						=> "varchar(64) NOT NULL default ''",
			'search' 					=> true,
		),
		
		'description' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['description'],
			'inputType'					=> 'text',
			'eval' 						=> array('tl_class' => 'w50'),
			'search' 					=> true,
			'sql'						=> "varchar(255) NOT NULL default ''",
		),
		
		'enabled' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['enabled'],
			'inputType'					=> 'checkbox',
			'eval' 						=> array('tl_class' => 'w50'),
			'filter' 					=> true,
			'sql'						=> "char(1) NOT NULL default ''"
		),
		
		'localId' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['localId'],
			'inputType'					=> 'fileTree',
			'eval'						=> array('fieldType'=>'radio', 'files' => false, 'unique' => true, 'mandatory'=>true ),
			'search'					=> true,
			'sql'						=> "varchar(255) NOT NULL default ''",
		),
		
		'cloudId' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['cloudId'],
			'inputType'					=> 'cloudFileTree',
			'eval'						=> array('fieldType'=>'radio', 'files'=>false, 'cloudApiField' => 'pid', 'mandatory'=>true),
			'search' 					=> true,
			'sql'						=> "varchar(255) NOT NULL default ''",
		),
		
		'mode' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['mode'],
			'inputType' 				=> 'select',
			'options' 					=> array('c2l' /*, 'l2c', 's2w'*/),
			'reference'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values'],
			'eval' 						=> array('mandatory' => true),
			'sql'						=> "varchar(10) NOT NULL default ''",
		),
		
		'options' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['options'],
			'inputType' 				=> 'checkbox',
			'options' 					=> array('create', 'update', 'delete'),
			'default' 					=> array('create' => 'create', 'update' => 'update'),
			'reference'					=> &$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values'],
			'eval' 						=> array('multiple' => true),
			'sql'                     	=> "blob NULL",
		),
		
		'syncTstamp' => array
		(
			'sql'						=> "int(10) unsigned NOT NULL default '0'",
		),
	)
);
