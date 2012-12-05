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

 
/**
 * CloudNodeDataContainer provides callbacks for the DC_Memory DCA driver 
 */
class CloudNodeDataContainer extends Backend
{
	
	/**
	 * 
	 */
	public function loadData(\DataContainer $dc)
	{
		//$dc->setData('field', 'value');
		//$dc->setDataArray(array('field' => 'value', 'field2' => 'value'));
	}
	
	
	/**
	 * 
	 */
	public function checkPermission(\DataContainer $dc)
	{
		
	}
	
	
	/**
	 * 
	 */
	public function addBreadcrumb(\DataContainer $dc)
	{
		
	}
	
	
	/**
	 * 
	 */
	public function show(\DataContainer $dc)
	{
		
	}	
	
	
	/**
	 * 
	 */
	public function showAll(\DataContainer $dc)
	{
		$objHelper = new DC_Memory_Helpers($dc);		
		$objHelper->setCurrentLimits();
		
		$objTotal = $this->Database->execute('SELECT COUNT(id) AS total FROM tl_cloudapi_node');
		
		$objData = $this->Database->execute('SELECT * FROM tl_cloudapi_node' . $objHelper->getLimitForSQL());
		$arrDataArray = array();
		
		while($objData->next())
		{
			if($objData->type == 'file')
			{
				$strCatagory = dirname($objData->path);
			}
			else 
			{
				$strCatagory = $objData->path;
			}
			 
			$arrDataArray[$strCatagory]['label'] = $objData->name;
			$arrDataArray[$strCatagory]['data'][] = array
			(
				'id'			=> $objData->id,
				'class'			=> $objData->type,
				'label'			=> $objData->name,
				'buttons'	=> array
				(
					'edit' => array
					(
						'href'		=> 'contao/main.php?do=user&amp;act=edit&amp;id=' . $objData->id,
						'title'		=> 'Edit user ID ' . $objData->id,
						'icon'		=> 'system/themes/default/images/edit.gif',
						'icon_w'	=> '12',
						'icon_h'	=> '16',
						'alt'		=> 'Edit user'				
					),
					'copy' => array
					(
						'href'		=> 'contao/main.php?do=user&amp;act=copy&amp;id= ' . $objData->id,
						'title'		=> 'Duplicate user ID ' . $objData->id,
						'icon'		=> 'system/themes/default/images/copy.gif',
						'icon_w'	=> '14',
						'icon_h'	=> '16',
						'alt'		=> 'Duplicate user'				
					)
				)
			);
				
		} // end while
		
		$strLimitHtml = $objHelper->generateLimitMenuString($objTotal->total, 20);
		
		$strPanel = $objHelper->generatePanel($strLimitHtml);
		$strGlobalOperations = $objHelper->generateGlobalOperationsString();
		$strListView = $objHelper->generateListViewString($arrDataArray);
		return $strPanel . $strGlobalOperations . $strListView;
	}
	
	
	/**
	 * 
	 */
	public function delete(\DataContainer $dc)
	{
		
	}	
	
	
	/**
	 * 
	 */
	public function undo(\DataContainer $dc)
	{
		
	}		
}
 
