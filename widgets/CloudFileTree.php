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
 * Run in a custom namespace, so the class can be replaced
 */
namespace Netzmacht\Cloud\Api;
use FileTree;


/**
 * Class FileTree
 *
 * Provide methods to handle input field "page tree".
 * @copyright	Leo Feyer 2005-2012
 * @author	 Leo Feyer <http://contao.org>
 * @package	Core
 */
class CloudFileTree extends FileTree
{
	/**
	 * reference to cloud api object
	 * 
	 * @var Netzmacht\Cloud\Api\CloudApi
	 */
	protected $objCloudApi;


	/**
	 * Load the database object
	 * 
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);					 
		if ($this->cloudApi == null && $this->cloudApiField != '')	
		{
			$this->cloudApi = $this->activeRecord->{$this->cloudApiField};
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		
		try {
			$this->objCloudApi = CloudApiManager::getApi($this->cloudApi);
		}
		catch(\Exception $e)
		{
			return; 
		}
		
		$strValues = '';
		$arrValues = array();

		if (!empty($this->varValue)) // Can be an array
		{			
			$strValues = implode(',', (array)$this->varValue);
			$arrFindValues = (array)$this->varValue;			
						
			$allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));
				
			foreach ($arrFindValues as $intId)
			{
				try 
				{
					$objNode = $this->objCloudApi->getNode(intval($intId));
				}
				
				// something went wrong. file does not exists anymore or connection failed
				catch(\Exception $e) 
				{				
					continue;
				}


				// Show files and folders
				if (!$this->blnIsGallery && !$this->blnIsDownloads)
				{
					if ($objNode->type == 'folder')
					{
						$arrValues[$intId] = $this->generateImage('folderC.gif') . ' ' . $objNode->path;
					}
					else
					{
						$arrValues[$intId] = $this->generateImage($objNode->icon) . ' ' . $objNode->path;
					}
				}

				// Show a sortable list of files only
				else
				{
					if ($objNode->type == 'folder')
					{
						$objSubNodes = $objNode->getChildren();

						if ($objSubNodes == null || count($objSubNodes) == 0)
						{
							continue;
						}

						foreach ($objSubNodes as $strSubPath => $objSubNode)
						{
							// Skip subfolders
							if ($objSubNode->type == 'folder')
							{
								continue;
							}
							
							// cloudApi do not show image dimensions at the moment. maybe the feature will be added later
							$strInfo = $strSubPath . ' <span class="tl_gray">(' . $this->getReadableSize($objSubNode->size) /*. ($objSubNode->isGdImage ? ', ' . $objSubNode->width . 'x' . $objSubNode->height . ' px' : '') */ . ')</span>';

							if ($this->blnIsGallery)
							{
								// Only show images
								if ($objSubNode->isGdImage)
								{
									$arrValues[$strSubPath] = $this->generateImage(\Image::get($objSubNode->getThumbnail(), 80, 60, 'center_center'), '', 'class="gimage" title="' . specialchars($strInfo) . '"');
								}
							}
							else
							{
								// Only show allowed download types
								if (in_array($objSubNode->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', $objSubNode->basename))
								{
									$arrValues[$strSubPath] = $this->generateImage($objSubNode->icon) . ' ' . $strInfo;
								}
							}
						}
					}
					else
					{						
						if ($this->blnIsGallery)
						{
							// Only show images
							if ($objNode->isGdImage)
							{								
								$arrValues[$intId] = $this->generateImage(\Image::get($objNode->getThumbnail(), 80, 60, 'center_center'), '', 'class="gimage"');
							}
						}
						else
						{
							// Only show allowed download types
							if (in_array($objNode->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', $objNode->basename))
							{
								$arrValues[$intId] = $this->generateImage($objNode->icon) . ' ' . $objNode->path;
							}
						}
					}
				}
			}

			// Apply a custom sort order
			if ($this->strOrderField != '' && $this->{$this->strOrderField} != '')
			{
				$arrNew = array();
				$arrOrder = explode(',', $this->{$this->strOrderField});

				foreach ($arrOrder as $i)
				{
					if (isset($arrValues[$i]))
					{
						$arrNew[$i] = $arrValues[$i];
						unset($arrValues[$i]);
					}
				}

				if (!empty($arrValues))
				{
					foreach ($arrValues as $k=>$v)
					{
						$arrNew[$k] = $v;
					}
				}

				$arrValues = $arrNew;
				unset($arrNew);
			}
		}

		// Load the fonts for the drag hint (see #4838)
		$GLOBALS['TL_CONFIG']['loadGoogleFonts'] = true;
		//$strValues = htmlspecialchars(serialize($arrValues));
		$return = '<input type="hidden" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" value="'.$strValues.'">' . (($this->strOrderField != '') ? '
	<input type="hidden" name="'.$this->strOrderName.'" id="ctrl_'.$this->strOrderId.'" value="'.$this->{$this->strOrderField}.'">' : '') . '
	<div class="selector_container" id="target_'.$this->strId.'">' . (($this->strOrderField != '' && count($arrValues)) ? '
	<p id="hint_'.$this->strId.'" class="sort_hint">' . $GLOBALS['TL_LANG']['MSC']['dragItemsHint'] . '</p>' : '') . '
	<ul id="sort_'.$this->strId.'" class="'.trim((($this->strOrderField != '') ? 'sortable ' : '').($this->blnIsGallery ? 'sgallery' : '')).'">';

		foreach ($arrValues as $k=>$v)
		{
			$return .= '<li data-id="'.$k.'">'.$v.'</li>';
		}

		$return .= '</ul>
	<p><a href="system/modules/cloud-api/file.php?do='.\Input::get('do').'&amp;table='.$this->strTable.'&amp;field='.$this->strField.'&amp;act=show&amp;api='.$this->cloudApi.'&amp;id='.\Input::get('id').'&amp;value='.$strValues.'&amp;rt='.REQUEST_TOKEN.'" class="tl_submit" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\''.specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MOD']['files'][0] . ' (' . $this->cloudApi .')' )).'\',\'url\':this.href,\'id\':\''.$this->strId.'\'});return false">'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</a></p>' . (($this->strOrderField != '') ? '
	<script>Backend.makeMultiSrcSortable("sort_'.$this->strId.'", "ctrl_'.$this->strOrderId.'");window.addEvent("sm_hide",function(){$("hint_'.$this->strId.'").destroy();$("sort_'.$this->strId.'").removeClass("sortable")})</script>' : '') . '
	</div>';

		return $return;
	}


	/**
	 * Return an array if the "multiple" attribute is set
	 * @param mixed
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		// Store the order value
		if ($this->strOrderField != '')
		{
			$this->Database->prepare("UPDATE {$this->strTable} SET {$this->strOrderField}=? WHERE id=?")
						   ->execute(\Input::post($this->strOrderName), \Input::get('id'));
		}

		// Return the value as usual
		if (strpos($varInput, ',') === false)
		{
			return $this->blnIsMultiple ? array($varInput) : $varInput;
		}
		else
		{
			$arrValue = array_filter(explode(',', $varInput));
			return $this->blnIsMultiple ? $arrValue : $arrValue[0];
		}
	}
}
