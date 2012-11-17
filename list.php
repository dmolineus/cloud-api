<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Core
 * @link	http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Netzmacht\Cloud\Api;
use Backend;

/**
 * Initialize the system
 */
define('TL_MODE', 'BE');
require_once '../../initialize.php';


/**
 * AccesToken will redirect to cloud authorize page
 *
 * @copyright	Leo Feyer 2005-2012
 * @author	 Leo Feyer <http://contao.org>
 * @package	Core
 */
class AccessToken extends Backend
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
		$this->import('Input');		
		
		$objApi = CloudApiManager::getApi('dropbox');
		$objApi->authenticate();
		$objNode = $objApi->getNode('0');
				
		
		echo sprintf('<h1>%s</h1>', $objNode->path);			
		echo '<ul>';
		
		//$arrChildren = $objNode->getChildren();
		
		//$arrChildren = $objApi->searchNodes('mind', '/Studium');
		
		foreach($arrChildren as $objChild) {
			$strThumb = '';
			
			if($objChild->hasThumbnail) {
				$strThumb = $objChild->getThumbnail();
				$strImg = @\Image::get($strThumb, 80, 60, 'center_center');
				
				if ($strImg != '') {
					$strThumb = sprintf('<br><img src="http://localhost/dev/%s" alt="%s">', $strImg, basename($objChild->path));
				}				 
			}
			
			echo sprintf('<li>%s (%s) %s</li>', $objChild->path, $objChild->filesize, $strThumb);
		}
		echo '</ul>';		
	}
}


/**
 * Instantiate the controller
 */
$objAccessToken = new AccessToken();
$objAccessToken->run();
