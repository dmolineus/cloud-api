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
  * extends the settings palettes
  * 
  * cloudapi_hook is not an field. used for inserting the parts of each cloud api
  * by str_replace('cloudapi_hook', 'new,cloudapi_hook', $old) 
  */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{cloudapi_legend},cloudapi_hook';
