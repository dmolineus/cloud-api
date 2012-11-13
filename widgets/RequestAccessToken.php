<?php

namespace Netzmacht\Cloud\Api;
use Widget;

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
     * 
     */
    public function generate()
    {       
        $objApi = CloudApiManager::getApi($this->cloudApi);
        
        try {                      
            $objApi->authenticate();
        }
        catch(\Exception $e) {            
            return sprintf(
                '<div class="tl_info"><a href="system/modules/cloud-api/accessToken.php?api=%s" target="_blank">%s</a>', 
                $this->cloudApi,
                $GLOBALS['TL_LANG']['tl_settings']['cloudapi_accessTokenLink']
            );    
        }
       
        $arrAccountInfo = $objApi->getAccountInfo();
        
        return sprintf(
            '<div class="tl_confirm"><input name="%sAccessToken" type="hidden" value="%s"><b>%s</b> %s (%s)</div>',
            $this->cloudApi,
            htmlspecialchars($GLOBALS['TL_CONFIG'][$this->cloudApi . 'AccessToken']),
            $GLOBALS['TL_LANG']['tl_settings']['cloudapi_connected'], 
            $arrAccountInfo['display_name'],
            $arrAccountInfo['email']
        );     
    }
}
