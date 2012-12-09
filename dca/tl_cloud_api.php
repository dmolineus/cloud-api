<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   cloud-dropbox 
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
		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'name' => 'unique',
			)
		),
	),
	
	'list' => array
	(
	),
	
	'palettes' => array
	(
		'__selector' => array
		(
			'useCustomApp'
		),
		'default' => 'name, active;{connection_legend},useCustomApp',		
	),
	
	'subpalettes' => array
	(
		'useCustomApp' => 'appKey, appSecret',
	),
	
	'fields' => array
	(
		'id' => array
		(
			'sql' => 'int(10) unsigned NOT NULL auto_increment',
		),
		
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		
		'class' => array
		(
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'name' => array
		(
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'enabled' => array
		(
			'sql' => "varchar(1) NOT NULL default '0'",
		),
		
		'useCustomApp' => array
		(
			'sql' => "char(1) NOT NULL default '0'",
		),
		
		'appKey' => array
		(
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'appSecret' => array
		(
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'oAuthClass' => array
		(
			'sql' => "varchar(10) NOT NULL default ''",
		),
		
		'accessToken' => array
		(
			'sql' => "blob NULL",
		),
		
		'syncTstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		
		'syncInProgress' => array
		(
			'sql' => "char(1) NOT NULL default '0'",
		),
		
		'deltaCursor' => array
		(
			'sql' => "blob NULL",
		),
		
		'mountedFolders' => array
		(
			'sql' => "blob NULL",
		),
	),	
);
