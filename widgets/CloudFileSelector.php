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


/**
 * Run in a custom namespace, so the class can be replacedchanges
 */
namespace Netzmacht\Cloud\Api;
use FileSelector;


/**
 * CloudFileSelector extends the FileSelector of Contao
 * and adjusts it to work with the cloud file structure 
 * changes
 * Provide methods to handle input field "cloud file tree".
 * 
 * @copyright	Leo Feyer 2005-2012
 * @author		Leo Feyer <http://contao.org>
 * @author		David Molineus <http://www.netzmacht.de>
 * @package		cloud-api
 */
class CloudFileSelector extends FileSelector
{
	
	/**
	 * reference to cloud api 
	 *
	 * @var protected
	 */
	protected $objCloudApi = null;
	
	/**
	 * allowed extensions
	 * 
	 * @var array
	 */
	protected $arrExtensions;


	/**
	 * Load Cloud Api
	 * 
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);
		
		$this->cloudApi = \Input::get('api');	
				

				
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->import('BackendUser', 'User');
		
		try {			
			$this->objCloudApi = CloudApiManager::getApi($this->cloudApi);			
			$this->objCloudApi->authenticate();
		}
		catch(\Exception $e)
		{
			$this->addErrorMessage(sprintf('Could not load CloudApi "%s"', $this->cloudApi));
			return;		 
		}

		// Store the keyword
		if (\Input::post('FORM_SUBMIT') == 'item_selector')
		{
			$this->Session->set('file_selector_search', \Input::post('keyword'));
			$this->reload();
		}

		// Extension filter
		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['extensions'] != '')
		{
			$this->arrExtensions = trimsplit(',', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['extensions']);			
		}

		// Sort descending
		if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['flag']) && ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['flag'] % 2) == 0)
		{
			$this->strSortFlag = 'DESC';
		}

		$tree = '';
		$this->getPathNodes();
		$for = $this->Session->get('file_selector_search');
		$arrIds = array();

		// Search for a specific file
		if ($for != '')
		{			
			$arrNodes = $this->objCloudApi->searchNodes($for);	

			if (!empty($arrNodes))
			{
				// Respect existing limitations
				if (!$this->User->isAdmin)
				{	
					$arrRootNodes = array();
					
					foreach ($arrNodes as $objNode) 
					{
						$strFilemounts = $this->objCloudApi->getName() . 'Filemounts';						

						if (count(array_intersect($this->User->{$strFilemounts}, $this->getParentNodes((array)$objNode->path))) > 0)
						{
							$arrRootNodes[] = $objNode->path;
						}
					}

					$arrNodes = $arrRootNodes;
				}
			}


			// Build the tree
			foreach ($arrNodes as $id => $value)
			{
				$tree .= $this->renderFiletree($value, -20, false, true);
			}
		}
		else
		{

			// Show a custom path (see #4926)
			if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['path'] != '')
			{
				$objFolder = $this->objCloudApi->getNode($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['path']);

				if ($objFolder !== null)
				{
					$tree .= $this->renderFiletree($objFolder->path, -20);
				}
			}

			// Show all files to admins
			elseif ($this->User->isAdmin)
			{
				// fetch entrys in root directory
				$objRoot = $this->objCloudApi->getNode('/');
				$objNodes = $objRoot->getChildren();			

				foreach($objNodes as $objNode)				
				{
					$tree .= $this->renderFiletree($objNode->path, -20);
				}
			}

			// Show mounted files to regular users
			else
			{
				// cloudApi: we have file paths fo use the eliminateNestedPaths instead of the nested paged on
				$strFilemounts = $this->objCloudApi->getName() . 'Filemounts';			
				foreach ($this->eliminateNestedPaths($this->User->{$strFilemounts}) as $node)
				{
					$tree .= $this->renderFiletree($node, -20);
				}
			}
		}

		// Select all checkboxes
		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'checkbox')
		{
			$strReset = "\n" . '	<li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="check_all_' . $this->strId . '" class="tl_change_selected">' . $GLOBALS['TL_LANG']['MSC']['selectAll'] . '</label> <input type="checkbox" id="check_all_' . $this->strId . '" class="tl_tree_checkbox" value="" onclick="Backend.toggleCheckboxGroup(this,\'' . $this->strName . '\')"></div><div style="clear:both"></div></li>';
		}
		// Reset radio button selection
		else
		{
			$strReset = "\n" . '	<li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="reset_' . $this->strId . '" class="tl_change_selected">' . $GLOBALS['TL_LANG']['MSC']['resetSelected'] . '</label> <input type="radio" name="' . $this->strName . '" id="reset_' . $this->strName . '" class="tl_tree_radio" value="" onfocus="Backend.getScrollOffset()"></div><div style="clear:both"></div></li>';
		}

		// Return the tree
		return '<ul class="tl_listing tree_view'.(($this->strClass != '') ? ' ' . $this->strClass : '').'" id="'.$this->strId.'">
	<li class="tl_folder_top"><div class="tl_left">'.$this->generateImage((($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['icon'] != '') ? $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['icon'] : 'filemounts.gif')).' '.($GLOBALS['TL_CONFIG']['websiteTitle'] ?: 'Contao Open Source CMS').'</div> <div class="tl_right">&nbsp;</div><div style="clear:both"></div></li><li class="parent" id="'.$this->strId.'_parent"><ul>'.$tree.$strReset.'
	</ul></li></ul>';
	}


	/**
	 * Generate a particular subpart of the file tree and return it as HTML string
	 * @param integer
	 * @param string
	 * @param integer
	 * @return string
	 */
	public function generateAjax($id, $strField, $level)
	{
		if (!\Environment::get('isAjaxRequest'))
		{
			return '';
		}

		try {			
			$this->objCloudApi = CloudApiManager::getApi($this->cloudApi);			
			$this->objCloudApi->authenticate();
		}
		catch(\Exception $e)
		{
			$this->addErrorMessage(sprintf('Could not load CloudApi "%s"', $this->cloudApi));
			return;		 
		}


		$this->strField = $strField;
		$this->loadDataContainer($this->strTable);
		
		try {
			$objNode = $this->objCloudApi->getNode($id);	
		}
		catch(\Exception $e)
		{
			return '';			
		}
		
		$this->varValue = $objNode->path;
		
		// Extension filter
		if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['extensions'] != '')
		{
			$this->arrExtensions = trimsplit(',', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['extensions']);			
		}

		// Sort descending
		if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['flag']) && ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['flag'] % 2) == 0)
		{
			$this->strSortFlag = 'DESC';
		}		

		$this->getPathNodes();

		// Load the requested nodes
		$tree = '';
		$level = $level * 20;
		
		
		$arrNodes = $objNode->getChildren();		

		foreach($arrNodes as $strChild => $objChild)
		{
			$tree .= $this->renderFiletree($strChild, $level);
		}

		return $tree;
	}


	/**
	 * Recursively render the filetree
	 * @param integer
	 * @param integer
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	protected function renderFiletree($id, $intMargin, $protectedPage=false, $blnNoRecursion=false)
	{
		static $session;
		$session = $this->Session->getData();				

		
		$flag = substr($this->strField, 0, 2);
		$node = 'tree_' . $this->strTable . '_' . $this->strField;
		$xtnode = 'tree_' . $this->strTable . '_' . $this->strName;

		// Get the session data and toggle the nodes
		if (\Input::get($flag.'tg'))
		{
			$session[$node][\Input::get($flag.'tg')] = (isset($session[$node][\Input::get($flag.'tg')]) && $session[$node][\Input::get($flag.'tg')] == 1) ? 0 : 1;
			$this->Session->setData($session);
			$this->redirect(preg_replace('/(&(amp;)?|\?)'.$flag.'tg=[^& ]*/i', '', \Environment::get('request')));
		}
		
		try 
		{
			$objNode = $this->objCloudApi->getNode($id);	
		}
		catch(\Exception $e) 
		{
			return '';
		}		
	
		$blnFilesOnly = ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['files'] || $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['filesOnly']);		
		$arrResult = $this->applyFilter($objNode, $blnFilesOnly);

		// Return if there is no result
		if(empty($arrResult)) 
		{
			return '';
		}

		$return = '';
		$intSpacing = 20;

		// Check whether there are child records
		if (!$blnNoRecursion)
		{
			$arrNodes = $objNode->getChildren(); 			
		}

		$return .= "\n	" . '<li class="'.(($objNode->type == 'folder') ? 'tl_folder' : 'tl_file').'" onmouseover="Theme.hoverDiv(this, 1)" onmouseout="Theme.hoverDiv(this, 0)"><div class="tl_left" style="padding-left:'.($intMargin + $intSpacing).'px">';

		$tid = md5($id);
		$folderAttribute = 'style="margin-left:20px"';
		$session[$node][$id] = isset($session[$node][$tid]) ? $session[$node][$tid] : 0;
		$level = ($intMargin / $intSpacing + 1);
		$blnIsOpen = ($session[$node][$tid] == 1 || in_array($id, $this->arrNodes));

		if (!empty($arrNodes))
		{
			$folderAttribute = '';
			$img = $blnIsOpen ? 'folMinus.gif' : 'folPlus.gif';
			$alt = $blnIsOpen ? $GLOBALS['TL_LANG']['MSC']['collapseNode'] : $GLOBALS['TL_LANG']['MSC']['expandNode'];
			$return .= '<a href="'.$this->addToUrl($flag.'tg='.$tid).'" title="'.specialchars($alt).'" onclick="Backend.getScrollOffset();return AjaxRequest.toggleCloudFiletree(this,\''.$xtnode.'_'.$tid.'\',\''.$objNode->path.'\',\''.$this->strField.'\',\''.$this->strName.'\','.$level.')">'.$this->generateImage($img, '', 'style="margin-right:2px"').'</a>';
		}

		// Get the icon
		if ($objNode->type == 'folder')
		{
			$file = null;
			$image = !empty($objNodes) ? 'folderC.gif' : 'folderO.gif';
		}
		else
		{						
			$image = $objNode->icon;
		}

		$thumbnail = '';

		// Generate the thumbnail
		if ($objNode->type == 'file')
		{
			if ($objNode->isGdImage && $objNode->hasThumbnail)
			{
				$thumbnail = ' <span class="tl_gray">('.$this->getReadableSize($objNode->filesize). /*', '.$file->width.'x'.$file->height.' px*/ ')</span>';

				if ($GLOBALS['TL_CONFIG']['thumbnails'] /*&& $file->height <= $GLOBALS['TL_CONFIG']['gdMaxImgHeight'] && $file->width <= $GLOBALS['TL_CONFIG']['gdMaxImgWidth'] */)
				{
					$_height = 50;
					$thumbnail .= '<br><img src="' . TL_FILES_URL . \Image::get($objNode->getThumbnail(), $_width, $_height) . '" alt="" style="margin-bottom:2px">';
				}
			}
			else
			{
				$thumbnail = ' <span class="tl_gray">('.$this->getReadableSize($objNode->filesize).')</span>';
			}
		}

		// Add the file name
		$return .= $this->generateImage($image, '', $folderAttribute).' <label title="'.specialchars($objNode->path).'" for="'.$this->strName.'_'.$id.'">'.(($objNode->type == 'folder') ? '<strong>' : '').$objNode->name.(($objNode->type == 'folder') ? '</strong>' : '').'</label>'.$thumbnail.'</div> <div class="tl_right">';

		// Add a checkbox or radio button
		if ($objNode->type == 'file' || !$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['filesOnly'])
		{
			$value = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['paths'] ? $objNode->path : $id;

			switch ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'])
			{
				case 'checkbox':
					$return .= '<input type="checkbox" name="'.$this->strName.'[]" id="'.$this->strName.'_'.$id.'" class="tl_tree_checkbox" value="'.specialchars($value).'" onfocus="Backend.getScrollOffset()"'.$this->optionChecked($value, $this->varValue).'>';
					break;

				default:
				case 'radio':
					$return .= '<input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_'.$id.'" class="tl_tree_radio" value="'.specialchars($value).'" onfocus="Backend.getScrollOffset()"'.$this->optionChecked($value, $this->varValue).'>';
					break;
			}
		}

		$return .= '</div><div style="clear:both"></div></li>';

		// Begin a new submenu
		if (!empty($arrNodes) && ($blnIsOpen || $this->Session->get('file_selector_search') != ''))
		{
			$return .= '<li class="parent" id="'.$node.'_'.$tid.'"><ul class="level_'.$level.'">';

			foreach ($arrNodes as $strChild => $objChild)
			{
				// cloudApi: we do not have an protected option
				$return .= $this->renderFiletree($strChild, ($intMargin + $intSpacing) /*, $objNode->protected*/);
			}

			$return .= '</ul></li>';
		}

		return $return;
	}
	
	/**
	 * apply sorting, filesOnly and extension filter to result set
	 * 
	 * @return array
	 * @param mixed array or object
	 * @param bool set filesonly flag
	 */
	protected function applyFilter($mixed, $blnFilesOnly=null)
	{
		if(!is_array($mixed)) 
		{
			$mixed = (array) $mixed;			
		}
		
		$arrResult = array();
		
		foreach ($mixed as $strKey => $objValue) 
		{
			// filesOnly filter
			if($blnFilesOnly && $objValue->type == 'folder')
			{
				continue;
			}
			
			// extension filter
			if($objValue->type == 'file' && !in_array($objValue->extension, $this->arrExtensions))
			{
				continue;				
			}			
			
			$arrResult[$strKey] = $objValue;
		}

		// sorting filter
		if($this->strSortFlag == 'DESC') 
		{
			return array_flip($arrResult);
		}		
		
		return $arrResult;		
	}


	/**
	 * get parent nodes of a path
	 * 
	 * @return array
	 * @param array
	 */
	protected function getParentNodes($arrNodes)
	{
		$arrResult = array();
				
		foreach ($arrNodes as $mixed)
		{			
			$arrPids = array();
			
			if(is_object($mixed)) {
				$strDirName = $mixed->path;
			}
			else {
				$strDirName = $mixed;
			}							

			for($strDirName = dirname($strDirName); $strDirName != '/' && $strDirName != '' && $strDirName != '.'; $strDirName = dirname($strDirName))
			{
				$arrPids[$strDirName] = $strDirName;
				
			}
			
			$arrResult = array_merge($arrResult, $arrPids);
		}
		
		return $arrResult;
	}


	/**
	 * Get the IDs of all parent folders of the selected files, so they are expanded automatically
	 */
	protected function getPathNodes()
	{
		if (!$this->varValue)
		{
			return;
		}

		if (!is_array($this->varValue))
		{
			$this->varValue = array($this->varValue);
		}
		
		// create seperate method so we can use it on different places to 
		// get cloud parent nodes
		$arrNodes = $this->getParentNodes($this->varValue);
		$this->arrNodes = array_merge($this->arrNodes, $arrNodes);
		
	}
	
}

