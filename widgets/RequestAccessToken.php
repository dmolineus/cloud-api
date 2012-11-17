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

namespace Netzmacht\Cloud\Api;
use Widget;

/**
 * create an widget for loading the access token in the settings
 * 
 */
class RequestAccessToken extends Widget
{
    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Add a for attribute
     * @var boolean
     */
    protected $blnForAttribute = true;
    
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_widget';
    
	
    /**
     * Add specific attributes
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey)
        {
            case 'cloudApi':
                $this->arrAttributes['cloudApi'] = $varValue;
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }
    
	
    /**
     * generate widget
	 * 
	 * @return string
     */
    public function generate()
    {       
        $objApi = CloudApiManager::getApi($this->cloudApi);
        
        try 
        {                      
            $objApi->authenticate();
        }
		
		// could not authenticate so provide access link
        catch(\Exception $e) 
        {            
            return sprintf(
                '<div class="tl_info" style="margin-bottom: 7px;"><a href="system/modules/cloud-api/token.php?api=%s" target="_blank">%s</a></div>', 
                $this->cloudApi,
                $GLOBALS['TL_LANG']['tl_settings']['cloudapi_accessTokenLink']
            );    
        }
		
		// load account info and display connected info       
        $arrAccountInfo = $objApi->getAccountInfo();
        
        return sprintf(
            '<div class="tl_confirm" style="margin-bottom: 7px;"><input name="%sAccessToken" type="hidden" value="%s"><b>%s</b> %s (%s)</div>',
            $this->cloudApi,
            htmlspecialchars($GLOBALS['TL_CONFIG'][$this->cloudApi . 'AccessToken']),
            $GLOBALS['TL_LANG']['tl_settings']['cloudapi_connected'], 
            $arrAccountInfo['display_name'],
            $arrAccountInfo['email']
        );     
    }
}
