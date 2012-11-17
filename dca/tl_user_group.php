<?php

/**
 * create dynamically the mount options for registrated
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace (
	'fop',
	'fop;{cloudapi_legend},cloudapi_hook',
	$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']
);
