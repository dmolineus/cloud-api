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
use Backend;

/**
 * Stores methods to handle the ajax request for the cloud file tree
 */
class AjaxRequest extends Backend
{
	
	/**
	 * calls by contao hook executePreActions
	 * 
	 * @param string 'toggleCloudFiletree' or 'loadCloudFiletree' are matched
	 * @return void 
	 */
	public function executePreActions($strAction)
	{
		// toggle a node of the file tree	
		if($strAction == 'toggleCloudFiletree')
		{
			$this->import('Session');
			
			$this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', \Input::post('id'));
			$this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', \Input::post('id'));

			if (\Input::get('act') == 'editAll')
			{
				$this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $this->strAjaxKey);
				$this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', \Input::post('name'));
			}

			$nodes = $this->Session->get($this->strAjaxKey);
			$nodes[$this->strAjaxId] = intval(\Input::post('state'));
			$this->Session->set($this->strAjaxKey, $nodes);
			exit;
			
		}
		
		// pre ation for loading part of the cloud file tree
		elseif($strAction == 'loadCloudFiletree')
		{
			$this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', \Input::post('id'));
			$this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', \Input::post('id'));

			if (\Input::get('act') == 'editAll')
			{
				$this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $this->strAjaxKey);
				$this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', \Input::post('name'));
			}

			$nodes = $this->Session->get($this->strAjaxKey);
			$nodes[$this->strAjaxId] = intval(\Input::post('state'));
			$this->Session->set($this->strAjaxKey, $nodes);
		}
	}
	
	
	/**
	 * calls by contao hook executePostActions
	 * 
	 * @param string 'loadCloudFiletree' are matched
	 * @return void 
	 */
	public function executePostActions($strAction)
	{
		// generate part of the cloud file selector
		if($strAction == 'loadCloudFiletree')
		{
			$arrData['strTable'] = $dc->table;
			$arrData['id'] = $this->strAjaxName ?: $dc->id;
			$arrData['name'] = \Input::post('name');
			$strFolder = \Input::post('folder');

			$objWidget = new $GLOBALS['BE_FFL']['cloudFileSelector']($arrData, $dc);
			echo $objWidget->generateAjax($strFolder, \Input::post('field'), intval(\Input::post('level')));
			exit;
		}		
	}
}
