<?php

namespace Netzmacht\Cloud\Api;
use System;

/**
 * define abstract CloudApi class
 */
abstract class CloudApi extends System
{

    /**
     * authenticate cloud api
     * 
     * @throws Exception if no valid token has found
     * @return bool
     */
    abstract public function authenticate();
    
    
    /**
     * get account info
     * 
     * @return array
     */    
    abstract public function getAccountInfo();
    
    
    /**
     * get cloud node (file or folder)
     * 
     * @param string $strPath
     * @return void
     */    
    abstract public function getNode($strPath);  
    
    
    /**
     * search for nodes
     * 
     * @return array
     * @param string search query
     * @param string start point
     */
    abstract public function searchNodes($strQuery, $strPath='');
    
}
