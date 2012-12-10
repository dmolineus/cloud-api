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
	 * Load the database object
	 * 
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);					 
		if ($this->cloudApi == null && $this->cloudApiField != '')	
		{
			$this->cloudApi = $this->activeRecord->{$this->cloudApiField};
		}
	}
    
	
    /**
     * generate widget
	 * 
	 * @return string
     */
    public function generate()
    {
    	try 
    	{
    		$objApi = CloudApiManager::getApi($this->cloudApi);	
    	}        
		catch(\Exception $e) 
		{
			return sprintf('<div class"tl_error">%s</div>', $e->getMessage());
		}
        
        try 
        {                      
            $objApi->authenticate();
					
			// load account info and display connected info       
	        $arrAccountInfo = $objApi->getAccountInfo();
        }
		
		// could not authenticate so provide access link
        catch(\Exception $e) 
        {            
            return sprintf(
                '<div class="tl_info" style="margin-bottom: 7px;"><a href="system/modules/cloud-api/token.php?api=%s" target="_blank">%s</a></div>', 
                $this->cloudApi,
                $GLOBALS['TL_LANG'][$this->strTable]['accessTokenLink']
            );    
        }

        return sprintf(
            '<div class="tl_confirm" style="margin-bottom: 7px;"><input name="%s" type="hidden" value="%s"><b>%s</b> %s (%s)</div>',
            $this->strField,
            htmlspecialchars($this->activeRecord->{$this->strField}),
            $GLOBALS['TL_LANG'][$this->strTable]['accessTokenConnected'], 
            $arrAccountInfo['display_name'],
            $arrAccountInfo['email']
        );     
    }
}
