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
 * ... ['global_button']['button_rules'] = array('isAdmin', 'generate');
 * 
 * By default it try to match against a id. If no id is set it uses the class attribute. This way
 * is nessesarry because the button_callback does not know the name of the button
 * ... ['global_button']['button_rules']['id'] = 'mybutton1';
 * ... ['global_button']['button_rules']['class'] = 'mybutton1';
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
		
		$this->import('BackendUser', 'User');
		
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
				if(!isset($arrConfig['button_rules']))
				{
					continue;
				}
				
				$strMatch = (isset($arrConfig['id']) ? 'id' : 'class');
				
				$this->arrGlobalButtons[$strButton] = array
				(
					'match' => ($strMatch == 'class' ? 'icon' : $strMatch),
					'value' => $arrConfig[$strMatch],
				);
			}
		}
		
		// read all button configurations
		if(isset($GLOBALS['TL_DCA'][$this->strTable]['list']['operations']))
		{
			foreach ($GLOBALS['TL_DCA'][$this->strTable]['list']['operations'] as $strButton => $arrConfig) 
			{
				if(!isset($arrConfig['button_rules']))
				{
					continue;
				}
				
				$strMatch = (isset($arrConfig['id']) ? 'id' : 'icon');
				
				$this->arrButtons[$strButton] = array
				(
					'match' => $strMatch,
					'value' => $arrConfig[$strMatch],
				);
			}
		}
		
		// get global label callback
		if(isset($GLOBALS['TL_DCA'][$this->strTable]['list']['label']['label_rules']))
		{
			$this->arrLabelCallback = $GLOBALS['TL_DCA'][$this->strTable]['list']['label']['label_rules'];
		}
	}


	/**
	 * generic check permissioin callback
	 * 
	 * @param DataContainer 
	 */
	public function checkPermission($objDc)
	{
		$arrRules = $GLOBALS['TL_DCA'][$this->strTable]['config']['permission_rules'];
		
		foreach ($arrRules as $strRule) 
		{
			$arrAttributes = array();
			$this->parseRule($strRule, $arrAttributes, null, null, null, null, null, null, null, 'permission');
			$strError = sprintf('Not enough permissions for action "%" on item with ID "%s"', \Input::get('act'), \Input::get('id'));
			
			if(!$this->{$strRule}($objDc, $arrAttributes, $strError))
			{
				$this->log($strError, $this->strTable . ' checkPermission', TL_ERROR);
				$this->redirect('contao/main.php?act=error');
				return;
			}			
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
		
		$arrRules = $GLOBALS['TL_DCA'][$this->strTable]['list'][($arrRow === null) ? 'global_operations' : 'operations'][$strButton]['button_rules'];
		
		$this->strGenerated = '';
		
		foreach ($arrRules as $strRule) 
		{
			$arrAttributes = array();
			$this->parseRule($strRule, $arrAttributes, $strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes, $arrRow);
			
			if(!$this->$strRule($strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes, $arrRow, $arrAttributes))
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
		return $this->generateButton(null, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes);
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
		
		foreach ($this->arrLabelCallback as $strRule) 
		{
			$arrAttributes = array();
			
			$this->parseRule($strRule, $arrAttributes, null, null, $strLabel, null, null, null, $arrRow, 'label');			
			$this->{$strRule}($arrRow, $strLabel, $objDc, $arrValues, $arrAttributes);
		}
		
		return $arrValues;		
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
	protected function buttonRuleGenerate(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null, $arrAttributes=null)
	{
		// global button
		if($arrRow === null)
		{
			if(!isset($arrAttributes['plain']) || $arrAttributes['id'] != '1')
			{
				$strHref = $this->addToUrl($strHref);	
			}
			
			$this->strGenerated .= sprintf
			(
				'<a href="%s" class="%s" title="%s" %s>%s</a>',
				$strHref, $strIcon, $strTitle,  $strAttributes, $strLabel
			);
		}
		
		// local button
		else 
		{
			if(!isset($arrAttributes['table']) || $arrAttributes['id'] != '0')
			{
				$strHref .= '&table=' . $this->strTable;			
			}
			
			if(!isset($arrAttributes['id']) || $arrAttributes['id'] != '0')
			{
				$strHref .= '&id=' . $arrRow['id'] ;			
			}
			
			if(!isset($arrAttributes['plain']) || $arrAttributes['id'] != '1')
			{
				$strHref = $this->addToUrl($strHref);	
			}
			
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
	protected function buttonRuleGenerateReferer(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null, $arrAttributes=null)
	{
		$this->strGenerated .= sprintf
		(
			'<a href="%s" class="%s" title="%s" %s>%s</a>',
			$this->getReferer(true), $strIcon, $strTitle,  $strAttributes, $strLabel
		);
		
		return true;
	}
	
	
	/**
	 * rule for checking if user hass access to something 
	 *
	 * @param string the button name 
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon class
	 * @param string added attributes
	 * @param array option data row of operation buttons
	 * @param arrary supported attribues
	 * 		- bool isAdmin, set to false if not gaining access if user admin
	 * 		- string modules check if user has access to module
	 * 		- string alexf Allowed excluded fields
	 */
	protected function buttonRuleHasAccess(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null, $arrAttributes=null)
	{		
		if(isset($arrAttributes['isAdmin']) && $arrAttributes['isAdmin']  && $this->User->isAdmin)
		{
				return true;			
		}
				
		return $this->genericHasAccess($arrAttributes);
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
	protected function buttonRuleIsAdmin(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null, $arrAttributes=null)
	{
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
	protected function buttonRuleLineBreak(&$strButton, &$strHref, &$strLabel, &$strTitle, &$strIcon, &$strAttributes, $arrRow=null, $arrAttributes=null)
	{
		$this->strGenerated = '<br style="line-height: 18px;">' . $this->strGenerated;
		return true;	
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
	protected function findButton($href, $label, $title, $icon, $attributes, $arrRow=null)
	{
		$arrSearch = ($arrRow === null) ? 'arrGlobalButtons' : 'arrButtons';
		
		foreach ($this->{$arrSearch} as $strButton => $arrOption) 
		{			
			if(${$arrOption['match']} == $arrOption['value'])
			{
				return $strButton;
			} 			
		}
	}
	
	
	/**
	 * generic has access rule
	 * 
	 * @param attributes supports modules and alexfs
	 * @return bool
	 */
	protected function genericHasAccess(&$arrAttributes)
	{
		$blnHasAccess = true; 
		
		if(isset($arrAttributes['modules']))
		{
			$blnHasAccess = $this->User->hasAccess($arrAttributes['modules'], 'modules');			
		}
		
		if($blnHasAccess && isset($arrAttributes['alexf']))
		{
			$blnHasAccess = $this->User->hasAccess($arrAttributes['alexf'], 'alexf');
		}
		
		return $blnHasAccess;
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
	protected function labelRuleYesNo(&$arrRow, &$strLabel, &$objDc, &$arrValues, &$arrAttributes)
	{
		$strYesNow = $arrRow[$arrAttributes['field']] == '1' ? 'yes' : 'no';
		
		$arrValues[$arrAttributes['index']] = $GLOBALS['TL_LANG']['MSC'][$strYesNow];	
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
	protected function labelRuleParseDate(&$arrRow, &$strLabel, $objDc, &$arrValues, &$arrAttributes)
	{
		if(isset($arrAttributes['field']))
		{
			$arrValues[$arrAttributes['index']] = $arrRow[$arrAttributes['field']];
		}
		
		$arrValues[$arrAttributes['index']] = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $arrValues[$arrAttributes['index']]);
	}
	
	
	/**
	 * parse a string which contains the rule with attributes
	 * 
	 * @param string the rule
	 * @param array attributes
	 * @param string button
	 * @param string href
	 * @param string label
	 * @param string title
	 * @param string icon
	 * @param string attributes
	 * @param array current row
	 * @param string rule prefix
	 * 
	 * Syntax of a rule: do not use spaces
	 * 
	 * 'button_rules' = array
	 * (
	 * 		'isAdmin,' 							// simple rule
	 * 		'hasAccess:isAdmin', 				// set attribute isAdmin=true
	 * 		'hasAccess:modules=files', 			// set attribute modules='files'
	 * 		'hasAccess:isAdmin:modules=files', 	// set attributes isAdmin=true and modules='files'
	 * 		'hasAccess:modules=[files,news]', 	// set attribute modules=array('files', 'news') 
	 * 		'hasAccess:isAdmin=false', 			// convert to boolean attributes isAdmin=false
	 * 		'hasAccess:isAdmin=1', 				// convert to int attributes isAdmin=1
	 * 		'tile:value=$strLabel', 			// access php variables, given as arguments, no array key access posible
	 * );
	 * 
	 */
	protected function parseRule(&$strRule, &$arrAttributes, $strButton, $strHref, $strLabel, $strTitle, $strIcon, $strAttributes, $arrRow=null, $strPrefix='button')
	{
		$arrRule = explode(':', $strRule);
		$strRule = $strPrefix . 'Rule' . ucfirst(array_shift($arrRule));
		
		foreach($arrRule as $strAttribute)
		{
			$arrSplit = explode('=', $strAttribute);
			
			if(!isset($arrSplit[1]))
			{
				$arrAttributes[$arrSplit[0]] = true;				
			}
			elseif(is_numeric($arrSplit[1]))
			{
				$arrAttributes[$arrSplit[0]] = intval($arrSplit[1]);
			}
			elseif($arrSplit[1] == 'false' || $arrSplit[1] == 'true')
			{
				$arrAttributes[$arrSplit[0]] = ($arrSplit[1] == 'true') ? true : false;
			}
			elseif(substr($arrSplit[1], 0, 1) == '[')
			{
				$arrAttributes[$arrSplit[0]] = explode(',', substr($arrSplit[1], 1, -1));
			}
			elseif(substr($arrSplit[1], 0, 1) == '$')
			{
				$arrAttributes[$arrSplit[0]] = ${substr($arrSplit[1], 1)};
			}
			else
			{
				$arrAttributes[$arrSplit[0]] = $arrSplit[1];
			}
		}
	}
	
	
	/**
	 * doing generic permission rule handling
	 * 
	 * checking for access to act param
	 * and support error messages
	 * 
	 * @param DataContainer
	 * @param array attributes, supports act, error and params
	 * @param string error message
	 */
	protected function permissionRuleGeneric($objDc, &$arrAttributes, &$strError)
	{
		$blnPermission = true;
		
		if(isset($arrAttributes['act']))
		{
			if(!is_array($arrAttributes['act']))
			{
				$arrAttributes['act'] = array($arrAttributes['act']);
			}
			
			if(!in_array(\Input::get('act'), $arrAttributes['act']))
			{
				$blnPermission = false;
			}			
		}
		
		if($blnPermission)
		{
			return true;
		}
		
		if(isset($arrAttributes['error']))
		{
			if(isset($arrAttributes['params']))
			{
				if(!is_array($arrAttributes['params']))
				{
					$arrAttributes['params'] = array($arrAttributes['params']);
				}
				
				$arrParams = array($arrAttributes['error']);
				
				foreach ($arrAttributes['params']  as $strParam) 
				{
					$arrParams[] = \Input::get($strParam);					
				}
				
				$strError = call_user_func_array('sprintf', $arrParams);				
			}
		}
		
		return false;
	}
	
	
	/**
	 * check if user has access depending on the act
	 * 
	 * @param DataContainer
	 * @param array attributes, supports act,error,params
	 * @param string error message
	 * @return bool
	 */
	protected function permissionRuleHasAccess($objDc, &$arrAttributes, &$strError)
	{		
		if($this->permissionRuleGeneric($objDc, $arrAttributes, $strError))
		{
			return $this->genericHasAccess($arrAttributes);			
		}
		
		return true;		
	}
	
	
	/**
	 * check if user is admin depending on the act
	 * 
	 * @param DataContainer
	 * @param array attributes, supports act,error,params
	 * @param string error message
	 * @return bool
	 */
	protected function permissionRuleIsAdmin($objDc, &$arrAttributes, &$strError)
	{
		if($this->permissionRuleGeneric($objDc, $arrAttributes, $strError))
		{
			return $this->User->isAdmin;			
		}
		
		return true;		
	}
	
	
	/**
	 * use this role for disabling access
	 * 
	 * @param DataContainer
	 * @param array attributes
	 * @param string error message
	 * @return bool
	 */
	protected function permissionRuleForbidden($objDc, &$arrAttributes, &$strError)
	{
		return false;		
	}
	
}
