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
 * Cloud API cloudapi_node
 */
$GLOBALS['TL_DCA']['tl_cloud_node'] = array
(
	// Config
	'config' => array
	(
		// will it work without a data container?
		'dataContainer'               => 'Table',		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				'cloudapi' => 'index',
				'extension' => 'index',
				//'path_cloudapi' => array('unique', array('path', 'cloudapi')),
			)
		),
		
		'onload_callback' => array
		(
			array('Netzmacht\Cloud\Api\DataContainer\CloudNode', 'checkPermission'),
		),
		
		'permission_rules' => array('forbidden'),
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
			'foreignKey'              => 'tl_cloud_api.title',
			'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
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
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		/* not supported at the moment
		'meta' => array
		(
			'sql'                     => "blob NULL"
		),*/
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
			'relation'                => array('type'=>'hasOne', 'load'=>'lazy'), 
			'sql'                     => "int(10) unsigned NULL"
		),	
	)
);