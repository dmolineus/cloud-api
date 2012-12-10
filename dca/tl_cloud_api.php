<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   cloud-dropbox 
 * @author    David Molineus <http://www.netzmacht.de>
 * @license   GNU/LGPL 
 * @copyright Copyright 2012 David Molineus netzmacht creative 
 *  
 **/
 
$GLOBALS['TL_DCA']['tl_cloud_api'] = array
(
	'config' => array
	(
		'dataContainer' => 'Table',
		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'name' => 'unique',
			)
		),
		
		'closed' => true,
		
		'onload_callback' => array
		(
			array('tl_cloud_api', 'choosePalette'),
		)
	),
	
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('name'),
			'flag'                    => 1,
		),
		
		'label' => array
		(
			'fields' => array('name', 'enabled', 'syncTstamp'),
			'showColumns' => true,
			'label_callback' => array('tl_cloud_api', 'getLabel'),
		),
		
		'global_operations' => array
		(
			'install' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cloud_api']['installService'],
				'href'                => 'act=install',
				'class'               => 'header_new',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'button_callback' => array('tl_cloud_api', 'generateInstallButton'),
			),
			
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
		),
		
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cloud_api']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			
			'sync' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cloud_api']['sync'],
				'href'                => 'act=sync&table=tl_cloud_node',
				'icon'                => 'sync.gif',
			),

			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cloud_api']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cloud_api']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				
			),
		)
	),
	
	'palettes' => array
	(
		'__selector__' => array
		(
			'enabled',
		),
		'default' => '{connection_legend}, enabled;{folder_legend:hide},mountedFolders;',	
	),
	
	'subpalettes' => array
	(
	),
	
	'fields' => array
	(
		'id' => array
		(
			'sql' => 'int(10) unsigned NOT NULL auto_increment',
		),
		
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		
		'name' => array
		(
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['name'],
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'enabled' => array
		(			
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'],
			'inputType'	=> 'checkbox',
			'eval' => array('submitOnChange'=>true),
			'sql' => "char(1) NOT NULL default ''",
		),
		
		'oAuthClass' => array
		(
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'],
			'inputType' => 'select',
			'options' => array('Curl', 'PEAR', 'PHP'),	
			'eval' => array('mandatory'=>true, 'nospace'=>'true', 'tl_class'=>'w50'),
			'sql' => "varchar(10) NOT NULL default ''",
		),
		
		'accessToken' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'],
			'inputType' => 'accessToken',
			'eval' => array('nospace'=>'true', 'cloudApiField' => 'name', 'tl_class' => 'w50', 'doNotShow' => true),		
			'sql' => "blob NULL",
		),
		
		'mountedFolders' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'],
			'inputType' => 'listWizard',
			'eval' => array('nospace'=>'true', 'tl_class' => 'clr'),
			'sql' => "blob NULL",
		),
		
		'useCustomApp' => array
		(
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'],
			'inputType' => 'checkbox',
			'eval' => array('submitOnChange'=>true),
			'sql' => "char(1) NOT NULL default ''",
		),
		
		'appKey' => array
		(
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'],
			'inputType'	=> 'text',
			'eval' => array('mandatory'=>true, 'tl_class'=>'w50'),
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'appSecret' => array
		(
			'label'	=> &$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'],
			'inputType'	=> 'text',
			'eval' => array('mandatory'=>true, 'tl_class'=>'w50'),
			'sql' => "varchar(255) NOT NULL default ''",
		),
		
		'root' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_cloud_api']['root'],
			'inputType'				=> 'select',
			'options'				 => array('dropbox', 'sandbox'),
			'eval'					=> array('mandatory'=>true, 'tl_class'=>'w50')
		),
		
		'syncTstamp' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'],
			'sql' => "int(10) unsigned NOT NULL default '0'",
			'eval' => array('rgxp' => 'datim'),
		),
		
		'syncInProgress' => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'],
			'sql' => "char(1) NOT NULL default '0'",
		),
		
		'deltaCursor' => array
		(
			'eval' => array('doNotShow' => true),
			'sql' => "blob NULL",
		),
	),	
);


/**
 * data container class
 * 
 * @author David Molineus <http://netzmacht.de>
 */
class tl_cloud_api extends Backend
{
	
	/**
	 * change values for the labels
	 * 
	 * @param array row
	 * @param string
	 * @param datacontainer
	 * @param values
	 */
	public function getLabel($row, $field, $dc, $values)
	{
		if($row['enabled'] == '1')
		{
			$values[1] = $GLOBALS['TL_LANG']['MSC']['yes'];
		}
		else
		{
			$values[1] = $GLOBALS['TL_LANG']['MSC']['no'];
		}
		
		$values[2] = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $values[2]);
		 
		return $values;
	}
	
	
	/**
	 * choose palette supports different palettes depending on cloud name
	 * subpalettes are stores in customsubpalettes['cloudname'].
	 * 
	 * @return void  
	 */
	public function choosePalette()
	{
		$intId = \Input::get('id');
				
		$this->import('Database');
		
		$objStmt = $this->Database->prepare('SELECT * FROM tl_cloud_api WHERE id=?');
		$objResult = $objStmt->execute($intId);
		
		if($objResult->numRows == 0) 
		{
			return;
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objResult->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_cloud_api']['palettes'][$objResult->name];
		}
		
		if(isset($GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name]))
		{
			$GLOBALS['TL_DCA']['tl_cloud_api']['subpalettes'] = $GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name];
			$GLOBALS['TL_DCA']['tl_cloud_api']['palettes']['__selector__'] = array_keys
			(
				$GLOBALS['TL_DCA']['tl_cloud_api']['customSubPalettes'][$objResult->name]
			);
		}
	}
	
	
	/**
	 * hide install button if every cloud api is installed
	 * 
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param array
	 */
	public function generateInstallButton($href, $label, $title, $icon, $attributes)
	{
		$arrApis = Netzmacht\Cloud\Api\CloudApiManager::getApis(0);
		
		if(count($arrApis) > 0) {
			return sprintf('<a href="%s" class="%s" title="%s">%s</a>',$this->addToUrl($href), $icon, $title, $label);
		}
	}
}
