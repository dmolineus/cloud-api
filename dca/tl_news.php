<?php

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