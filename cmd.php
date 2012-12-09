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
use Backend;

/**
 * Initialize the system
 */
define('TL_MODE', 'BE');
require_once '/var/www/dev/system/initialize.php';


/**
 * AccesToken will redirect to cloud authorize page
 *
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://contao.org>
 * @package    Core
 */
class CloudCMD extends Backend
{

    /**
     * Initialize the controller
     *
     * 1. Import the user
     * 2. Call the parent constructor
     * 3. Authenticate the user
     * 4. Load the language files
     * DO NOT CHANGE THIS ORDER!
     */
    public function __construct()
    {
        $this->import('BackendUser', 'User');
        parent::__construct();

        $this->User->authenticate();

        $this->loadLanguageFile('default');
        $this->loadLanguageFile('modules');
    }


    /**
     * load cloud api and redirect to authorize page
     * 
     * @return void
     */
    public function run()
    {
        //$this->import('Input');
        
        //$objApi = CloudApiManager::getApi($this->Input->get('api'));
        //$strUrl = $objApi->getAuthorizeUrl();
        //$this->redirect($strUrl);
        
        //CloudApiManager::installApi('dropbox');
        
        $do = \Input::get('do');
		
		if($do == 'install')
		{
			CloudApiManager::installApi('dropbox');
			return; 
		}
		elseif ($do == 'mount') {
			echo serialize(array('/studium'));
			return;
		}
		elseif ($do == 'test') {
			return $this->test();
		}
		
        $objAPI = CloudApiManager::getApi('dropbox');
		try 
		{
			$objAPI->authenticate();
				
		}
		catch(\Exception $e)
		{
			//throw $e;
			$do = token;
			//die($e);			
		}
		
		
		
		switch ($do) 
		{
			case 'sync':
				$objAPI->sync();
				break;
			
			case 'token':
				$this->redirect('system/modules/cloud-api/token.php?api=dropbox');
				break;
		}
		
    }
	
	
	protected function test()
	{
		$arr = array('/studium/affenhals', '/tl/blob', '/hans/meyerstiftung');		
		$arrMounted = array('/studium');
		
		foreach ($arr as $key => $strPath) 
		{
			$blnMounted = false;
			
			if($arrMounted !== null)
			{
				foreach($arrMounted as $strFolder)
				{
					if(strncasecmp($strPath, $strFolder, strlen($strFolder)) === 0)
					{
						$blnMounted = true;
						break;
					}
				}
				
				if(!$blnMounted)
				{					
					continue;
				}
			}
			
			echo $strPath . '<br>';
		}
	}
}


/**
 * Instantiate the controller
 */
//$objCMD = new CloudCMD();
//$objCMD->run();

$objAPI = CloudApiManager::getApi('dropbox');
try 
{
	$objAPI->authenticate();
		
}
catch(\Exception $e)
{
	//throw $e;
	$do = token;
	//die($e);			
}


$objAPI->sync();
