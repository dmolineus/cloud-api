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
 **/

namespace Netzmacht\Cloud\Api;

/**
 * interface for classes which provides a sync listener
 * 
 * useful for logging, providing messages or for the mount syncing
 */
interface syncListenable
{

	/**
	 * call every registered sync listener
	 * 
	 * @param mixed string or CloudNodeModel current model or path
	 * @param string action can be create,update,create,delete,error,start,stop,info
	 * @param string provided message
	 * @param CloudApi passed cloud api object
	 */
	public function callSyncListener($strAction, $mixedNodeOrPath=null, $strMessage=null, $objApi=null);
	
		
	/**
	 * register a sync listener
	 * 
	 * @param mixed variable which is the first part of a callable by call_user_func
	 * @param string method name for call_user_func
	 * @param bool true if its a static call. otherwise $mixedSource will be handles as a class
	 */
	public function registerSyncListener($mixedSource, $strMethod, $blnCallStatic = false);
	
}
