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
 
$GLOBALS['TL_DCA']['tl_cloudapi'] = array
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
		),
		
		'class' => array
		(
		),
		
		'name' => array
		(
		),
		
		'mode' => array
		(
		),
		
		'modes' => array
		(
		),
		
		'tstamp' => array
		(
		),
		
		'enabled' => array
		(
		),
		
		'useCustomApp' => array
		(
		),
		
		'appKey' => array
		(
		),
		
		'appSecret' => array
		(
		),
		
		'oAuthClass' => array
		(
		),
		
		'appKey' => array
		(
		),
		
		'syncTstamp' => array
		(
		),
		
		'syncInProgress' => array
		(
		),
		
		'deltaCursor' => array
		(
		),
	),	
);
