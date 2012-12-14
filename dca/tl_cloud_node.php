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
$GLOBALS['TL_DCA']['tl_cloud_node'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'CloudNode',
		'onload_callback' => array
		(
			//array('tl_cloud_node', 'checkPermission'),
			//array('tl_cloud_node', 'addBreadcrumb'),
			
		),		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				//'path' => 'index',
				'cloudapi' => 'index',
				//'path_cloudapi' => array('unique', array('path', 'cloudapi')),
				'extension' => 'index'
			)
		),
	),

	// List
	'list' => array
	(
		'global_operations' => array
		(
			'sync' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['sync'],
				'href'                => 'act=sync',
				'class'               => 'header_sync',
				'button_callback'     => array('tl_files', 'syncFiles')
			),
			'toggleNodes' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleAll'],
				'href'                => 'tg=all',
				'class'               => 'header_toggle'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'button_callback'     => array('tl_files', 'editFile')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'button_callback'     => array('tl_files', 'copyFile')
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'button_callback'     => array('tl_files', 'cutFile')
			),
			'source' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['source'],
				'href'                => 'act=source',
				'icon'                => 'editor.gif',
				'button_callback'     => array('tl_files', 'editSource')
			),
			'protect' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['protect'],
				'href'                => 'act=protect',
				'icon'                => 'protect.gif',
				'button_callback'     => array('tl_files', 'protectFolder')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_files', 'deleteFile')
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'name,meta'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'cloudapi' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'type' => array
		(
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'path' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'extension' => array
		(
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'hash' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'found' => array
		(
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_files']['name'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'decodeEntities'=>true),
			'save_callback' => array
			(
				array('tl_files', 'checkFilename')
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'meta' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_files']['meta'],
			'inputType'               => 'metaWizard',
			'eval'                    => array('allowHtml'=>true),
			'sql'                     => "blob NULL"
		),
		'modified' => array
		(
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'downloadUrl' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'downloadUrlExpires' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'filesize' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'hasThumbnail' => array
		(
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		
		'thumbnailVersion' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		
		'cachedVersion' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		
		'version' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		
		'mountVersion' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		
		'fid' => array
		(
			'foreignKey'              => 'tl_files.path',
			'relation'                => array('type'=>'hasOne', 'load'=>'eager'), 
			'sql'                     => "int(10) unsigned NULL"
		),	
	)
);