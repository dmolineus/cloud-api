<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Core
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Netzmacht\Cloud\Api;
use SelectMenu;

/**
 * Class SelectMenu
 *
 * Provide methods to handle select menus.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://contao.org>
 * @package    Core
 */
class CloudApiSelectMenu extends SelectMenu
{
    
    /**
     * override options with registered cloud settings
     */
    public function __construct($arrAttributes=null)
    {
        parent::__construct($arrAttributes);
        
        // override options
        $this->arrOptions = array();
        
        $arrApis = CloudApiManager::getregisteredApis();                
        
        foreach ($arrApis as $strKey => $arrValue) 
        {
            $this->arrOptions[] = array(
                'value' => $strKey,
                'label' => isset($GLOBALS['TL_LANG']['MOD']['cloudapi_' . $strKey][0]) 
                    ? $GLOBALS['TL_LANG']['MOD']['cloudapi_' . $strKey][0]
                    : $strKey                    
            );                        
        }        
    }

}
