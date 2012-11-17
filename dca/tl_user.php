<?php

/**
 * create dynamically the mount options for registrated
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