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
 * insert the cloud api settings after the file operations for palettes extend and custom
 * 
 * cloudapi_hook is not an field. used for inserting the parts of each cloud api
 * by str_replace('cloudapi_hook', 'new,cloudapi_hook', $old) 
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace (
	'fop',
	'fop;{cloudapi_legend},cloudapi_hook',
	$GLOBALS['TL_DCA']['tl_user']['palettes']['extend']
);

$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace (
	'fop',
	'fop;{cloudapi_legend},cloudapi_hook',
	$GLOBALS['TL_DCA']['tl_user']['palettes']['custom']
);