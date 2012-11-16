<?php

namespace Netzmacht\Cloud\Api;
use Backend;

/**
 * 
 */
class AjaxRequest extends Backend
{
    /**
     * 
     */
    public function executePreActions($strAction)
    {        
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
     * 
     */
    public function executePostActions($strAction)
    {
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
