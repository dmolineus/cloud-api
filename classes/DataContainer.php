<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   netzmacht-utils
 * @author    David Molineus <http://www.netzmacht.de>
 * @license   GNU/LGPL 
 * @copyright Copyright 2012 David Molineus netzmacht creative 
 *  
 **/
 
namespace Netzmacht\Utils;
use Backend;


/**
 * General DataContainer which provides usefull helpers for the daily stuff
 * using DataContainers
 * 
 * You only have to extend it and set $strTable. Then you can use it for generating
 * your buttons by assign different rules. It's all configurable in the DCA!. Just use
 * a generic callback for creating the button. It's nessessary to add the generate rule
 * so you can decide if something should happen after that
 * 
 * ... ['global_button']['button_callback'] => array('TlFiles', 'generateGlobalButton'),
 * ... ['global_button']['utilsButtonRules'] = array('isAdmin', 'generate');
 * 
 * By default it try to match the class attribute but you can also use every other option to
 * match. It is nessesarry to use this way because the button_callback does not know the name of the button
 * 
 * ... ['global_button']['id'] = 'myCustomButton';
 * ... ['global_button']['utilsButtonMatch'] = 'id';
 */
class DataContainer extends Backend
{
	
	/**
	 * @var array global buttons 
	 */
	protected $arrGlobalButtons = array();
	
	/**
	 * @var arry buttons buttons 
	 */
	protected $arrButtons = array();
	
	/**
	 * @var array label callback
	 */
	protected $arrLabelCallback = array();
	
	
	/**
	 * @var string generated output
	 */
	protected $strGenerated = '';
	
	
	/**
	 * @var string table
	 */
	protected $strTable;
	
	
	/**
	 * constructor fetches all configurations
	 */
	public function __construct()
	{
		parent::__construct();
		
		if($this->strTable === null)
		{
			$strTable = get_class($this);
			$strTable = substr($strTable, strrpos($strTable, '\\')+1);
			$strTable = 'tl' . preg_replace('/([A-Z])/', '_\0', $strTable);
			$this->strTable = strtolower($strTable);	
		}
		
		if(!isset($GLOBALS['TL_DCA'][$this->strTable]))
		{
			return;
		}
		
		// read all button configurations
		if(isset($GLOBALS['TL_DCA'][$this->strTable]['list']['global_operations']))
		{
			foreach ($GLOBALS['TL_DCA'][$this->strTable]['list']['global_operations'] as $strButton => $arrConfig) 
			{
				if(!isset($arrConfig['utilsButtonRules']))
				{
					continue;
				}
				
				if(!isset($arrConfig['utilsButtonOptions']))
				{
					$arrConfig['utilsButtonOptions'] = 'class';					
				}
				
				$this->arrGlobalButtons[$strButton] = array
				(
					'match' => $arrConfig['utilsButtonOptions'],
					'value' => $arrConfig[$arrConfig['utilsButtonOptions']],
				);
			}
		}
		
		// read all button configurations
		if(isset($GLOBALS['TL_DCA'][$this->strTable]['list']['operations']))
		{
			foreach ($GLOBALS['TL_DCA'][$this->strTable]['list']['operations'] as $strButton => $arrConfig) 
			{
				if(!isset($arrConfig['utilsButtonRules']))
				{
					continue;
				}
				
				if(!isset($arrConfig['utilsButtonOptions']))
				{
					$arrConfig['utilsButtonOptions'] = 'class';					
				}
				
				$this->arrButtons[$strButton] = array
				(
					'match' => $arrConfig['utilsButtonOptions'],
					'value' => $arrConfig[$arrConfig['utilsButtonOptions']],
				);
			}
		}
		
		// get global label callback
		if(isset($GLOBALS['TL_DCA'][$this->strTable]['list']['label']['utilsLabelRules']))
		{
			$this->arrLabelCallback = $GLOBALS['TL_DCA'][$this->strTable]['list']['label']['utilsLabelRules'];
		}
	}
	
	
	/**
	 * generic generate button callback
	 *
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	public function generateButton($arrRow, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes)
	{
		$strButton = $this->findButton($strHref, $strLabel, $strTitle, $strIcon, $strAttributes, $arrRow);

		if($strButton === null)
		{
			return '';			
		}
		
		$arrRules = $GLOBALS['TL_DCA'][$this->strTable]['list']['operations'][$strButton]['utilsButtonRules'];
		
		$this->strGenerated = '';
		
		foreach ($arrRules as $strRule) 
		{
			$strRule = 'buttonRule' . ucfirst($strRule);
			
			if(!$this->$strRule($strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes, $arrRow))
			{
				return '';				
			}
		}
		
		return $this->strGenerated;
	}
	
	
	/**
	 * generic create global button callback
	 *
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	public function generateGlobalButton($strHref, $strLabel, $strTitle, $strIcon, $strAttributes)
	{
		$strButton = $this->findGlobalButton($strHref, $strLabel, $strTitle, $strIcon, $strAttributes);

		if($strButton === null)
		{
			return '';			
		}
		

		$arrRules = $GLOBALS['TL_DCA'][$this->strTable]['list']['global_operations'][$strButton]['utilsButtonRules'];
		
		$this->strGenerated = '';
		
		foreach ($arrRules as $strRule) 
		{
			$strRule = 'buttonRule' . ucfirst($strRule);
			
			if(!$this->$strRule($strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes))
			{
				return '';				
			}
		}
		
		return $this->strGenerated;
	}
	
	
	/**
	 * generic generateLabel callback
	 * 
	 * @param array current row
	 * @param string label
	 * @param DataContainer
	 * @param array values
	 */
	public function generateLabel($arrRow, $strLabel, $objDc, $arrValues)
	{
		if(empty($this->arrLabelCallback))
		{
			return;
		}
		
		foreach ($this->arrLabelCallback as $arrConfig) 
		{
			$strRule = 'labelRule' . ucfirst($arrConfig[0]);
			
			$this->{$strRule}($arrRow, $strLabel, $objDc, $arrValues, isset($arrConfig[1]) ? $arrConfig[1] : null, isset($arrConfig[2]) ? $arrConfig[2] : null);
		}
		
		return $arrValues;		
	}
	
	
	/**
	 * find button by trying to match it against the configuration
	 * 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	protected function findGlobalButton($href, $label, $title, $icon, $attributes)
	{
		foreach ($this->arrGlobalButtons as $strButton => $arrOption) 
		{
			if($arrOption['match'] == 'class')
			{
				$arrOption['match'] = 'icon'; 
			}
			
			if(${$arrOption['match']} == $arrOption['value'])
			{
				return $strButton;
			} 			
		}
	}
	
	
	/**
	 * find button by trying to match it against the configuration
	 * 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 */
	protected function findButton($href, $label, $title, $icon, $attributes, $arrRow)
	{
		foreach ($this->arrButtons as $strButton => $arrOption) 
		{			
			if(${$arrOption['match']} == $arrOption['value'])
			{
				return $strButton;
			} 			
		}
	}
	
	
	/**
	 * rule for generating the button
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 */
	protected function buttonRuleAddToUrl(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null)
	{
		$strHref = $this->addToUrl($strHref);
		return true;
	}
	
	
	/**
	 * rule for generating the button
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 */
	protected function buttonRuleGenerate(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null)
	{
		// global button
		if($arrRow === null)
		{
			$this->strGenerated .= sprintf
			(
				'<a href="%s" class="%s" title="%s" %s>%s</a>',
				$strHref, $strIcon, $strTitle,  $strAttributes, $strLabel
			);			
		}
		
		// local button
		else 
		{
			$this->strGenerated .= sprintf
			(
				'<a href="%s" title="%s" %s>%s</a> ',
				$strHref, $strTitle,  $strAttributes, $this->generateImage($strIcon, $strLabel)
			);			
		}
		
		return true;
	}
	
	
	/**
	 * rule for generating a referer button
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 */
	protected function buttonRuleGenerateReferer(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null)
	{
		$this->strGenerated .= sprintf
		(
			'<a href="%s" class="%s" title="%s" %s>%s</a>',
			$this->getReferer(true), $strIcon, $strTitle,  $strAttributes, $strLabel
		);
		
		return true;
	}
	
	
	/**
	 * rule checks if user is admin
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 */
	protected function buttonRuleIsAdmin(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null)
	{
		$this->import('BackendUser', 'User');
		
		return $this->User->isAdmin;		
	}
	
	
	/**
	 * rule checks adds line break before output
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 */
	protected function buttonRuleLineBreak(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null)
	{
		$this->strGenerated = '<br style="line-height: 18px;">' . $this->strGenerated;
		return true;	
	}

	
	/**
	 * set value to yes or no depending on value
	 * 
	 * @param array current row
	 * @param string label
	 * @param DataContainer
	 * @param array reference to values
	 * @param int value of index
	 * @param string field name
	 */
	protected function labelRuleYesNo($arrRow, $strLabel, $objDc, &$arrValues, $intIndex, $strField)
	{
		$strYesNow = $arrRow[$strField] == '1' ? 'yes' : 'no';
		
		$arrValues[$intIndex] = $GLOBALS['TL_LANG']['MSC'][$strYesNow];	
	}
	
	
	/**
	 * parse timestamp into date
	 * 
	 * @param array current row
	 * @param string label
	 * @param DataContainer
	 * @param array reference to values
	 * @param int value of index
	 * @param string field name
	 */
	protected function labelRuleParseDate($arrRow, $strLabel, $objDc, &$arrValues, $intIndex, $strField=null)
	{
		if($strField !== null)
		{
			$arrValues[$intIndex] = $arrRow[$strField];
		}
		
		$arrValues[$intIndex] = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $arrValues[$intIndex]);
	}
	
}
