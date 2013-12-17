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

$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][]	= 'cloudapiFileManagerIntegration';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'].= ';{cloudapi_legend},cloudapiSyncInterval,cloudapiSyncDownloadTime,'
														. 'cloudapiSyncDownloadLimit,cloudapiFileManagerIntegration';
	
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['cloudapiFileManagerIntegration'] =  'cloudapiFileManagerManageMounts';

$GLOBALS['TL_DCA']['tl_settings']['fields']['cloudapiSyncInterval'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncInterval'],
	'inputType'		=> 'select',
	'options'		=> array('600', '1800', '3600', '7200', '86400', '604800'),
	'reference'		=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions'],
	'eval'			=> array('rgxp' => 'digit', 'tl_class' => 'w50'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['cloudapiSyncDownloadTime'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadTime'],
	'inputType'		=> 'text',
	'eval'			=> array('rgxp' => 'digit', 'maxlength' => 3, 'tl_class' => 'w50'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['cloudapiSyncDownloadLimit'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadLimit'],
	'inputType'		=> 'text',
	'eval'			=> array('rgxp' => 'digit', 'maxlength' => 3, 'tl_class' => 'w50'),
);


$GLOBALS['TL_DCA']['tl_settings']['fields']['cloudapiFileManagerIntegration'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerIntegration'],
	'inputType'		=> 'checkbox',
	'eval'			=> array('isBoolean' => true, 'tl_class' => 'clr w50', 'submitOnChange' => true),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['cloudapiFileManagerManageMounts'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerManageMounts'],
	'inputType'		=> 'checkbox',
	'eval'			=> array('isBoolean' => true, 'tl_class' => 'w50'),
);
