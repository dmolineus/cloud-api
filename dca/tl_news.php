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

// only used for testing purposes

$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] .= ';{cloudapi_legend},cloudApi,cloudApiSingleSRC';

$GLOBALS['TL_DCA']['tl_news']['fields']['cloudApi'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['MSC']['cloudapi_apiselect'],
	'inputType'			=> 'cloudApiSelect',
	'eval'				=> array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'				=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_news']['fields']['cloudApiSingleSRC'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['MSC']['cloudapi_cloudfiletree'],
	'exclude'			=> true,
	'inputType'			=> 'cloudFileTree',	
	'eval'				=> array('fieldType'=>'radio', 'filesOnly'=>false, 'mandatory'=>false, 'class=clr'),
	'sql'				=> "varchar(255) NOT NULL default ''"
);