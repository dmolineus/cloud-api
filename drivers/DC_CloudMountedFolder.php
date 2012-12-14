<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Core
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */



/**
 * Class DC_Folder
 *
 * Provide methods to modify the file system.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://contao.org>
 * @package    Core
 */
class DC_CloudMountedFolder extends Contao\DC_Folder
{

	/**
	 * Render the file tree and return it as HTML string
	 * @param string
	 * @param integer
	 * @param boolean
	 * @param boolean
	 * @param array
	 * @return string
	 */
	protected function generateTree($path, $intMargin, $mount=false, $blnProtected=false, $arrClipboard=null, $arrMounts=array())
	{
		// load Mounts
		if(empty($arrMounts))
		{
			$this->import('Database');
			//$objResult = $this->Database->query('SELECT * FROM tl_cloud_mount WHERE enabled = 1');
			//$arrIds = $objResult->fetchEach('singleSRC');
			$objResult = $this->Database->query(
				'SELECT f.path AS fPath, n.path, m.id, c.title, m.name' 
				. ' FROM  `tl_cloud_mount` m'
				. ' LEFT JOIN  `tl_cloud_node` n ON n.id = m.cloudId'
				. ' LEFT JOIN  `tl_files` f ON f.id = m.localId'
				. ' LEFT JOIN  `tl_cloud_api` c ON c.id = n.cloudapi'
			);
			
			if($objResult !== null)
			{
				while($objResult->next())
				{
					$arrMounts[$objResult->fPath] = $objResult->row();
				}
			}
		}
		
		static $session;
		$session = $this->Session->getData();

		// Get the session data and toggle the nodes
		if (\Input::get('tg'))
		{
			$session['filetree'][\Input::get('tg')] = (isset($session['filetree'][\Input::get('tg')]) && $session['filetree'][\Input::get('tg')] == 1) ? 0 : 1;
			$this->Session->setData($session);
			$this->redirect(preg_replace('/(&(amp;)?|\?)tg=[^& ]*/i', '', \Environment::get('request')));
		}

		$return = '';
		$files = array();
		$folders = array();
		$intSpacing = 20;
		$level = ($intMargin / $intSpacing + 1);

		// Mount folder
		if ($mount)
		{
			$folders = array($path);
		}

		// Scan directory and sort the result
		else
		{
			foreach (scan($path) as $v)
			{
				if ($v == '.svn' || $v == '.DS_Store')
				{
					continue;
				}

				if (is_file($path . '/' . $v))
				{
					$files[] = $path . '/' . $v;
				}
				else
				{
					if ($v == '__new__')
					{
						$this->Files->rmdir(str_replace(TL_ROOT.'/', '', $path) . '/' . $v);
					}
					else
					{
						$folders[] = $path . '/' . $v;
					}
				}
			}

			natcasesort($folders);
			$folders = array_values($folders);

			natcasesort($files);
			$files = array_values($files);
		}

		// Folders
		for ($f=0; $f<count($folders); $f++)
		{
			$md5 = md5($folders[$f]);
			$content = scan($folders[$f]);
			$currentFolder = str_replace(TL_ROOT.'/', '', $folders[$f]);
			$session['filetree'][$md5] = is_numeric($session['filetree'][$md5]) ? $session['filetree'][$md5] : 0;
			$currentEncoded = $this->urlEncode($currentFolder);
			$countFiles = count($content);

			// Subtract files that will not be shown
			if (!empty($this->arrValidFileTypes))
			{
				foreach ($content as $file)
				{
					// Folders
					if (is_dir($folders[$f] .'/'. $file))
					{
						if ($file == '.svn')
						{
							--$countFiles;
						}
					}

					// Files
					elseif (!in_array(strtolower(substr($file, (strrpos($file, '.') + 1))), $this->arrValidFileTypes))
					{
						--$countFiles;
					}
				}
			}

			
			$return .= "\n  " . '<li class="tl_folder" onmouseover="Theme.hoverDiv(this,1)" onmouseout="Theme.hoverDiv(this,0)"><div class="tl_left" style="padding-left:'.($intMargin + (($countFiles < 1) ? 20 : 0)).'px">';

			// Add a toggle button if there are childs
			if ($countFiles > 0)
			{
				$img = ($session['filetree'][$md5] == 1) ? 'folMinus.gif' : 'folPlus.gif';
				$alt = ($session['filetree'][$md5] == 1) ? $GLOBALS['TL_LANG']['MSC']['collapseNode'] : $GLOBALS['TL_LANG']['MSC']['expandNode'];
				$return .= '<a href="'.$this->addToUrl('tg='.$md5).'" title="'.specialchars($alt).'" onclick="Backend.getScrollOffset(); return AjaxRequest.toggleFileManager(this, \'filetree_'.$md5.'\', \''.$currentFolder.'\', '.$level.')">'.$this->generateImage($img, '', 'style="margin-right:2px"').'</a>';
			}

			$protected = ($blnProtected === true || array_search('.htaccess', $content) !== false) ? true : false;
			$folderImg = ($session['filetree'][$md5] == 1 && $countFiles > 0) ? ($protected ? 'folderOP.gif' : 'folderO.gif') : ($protected ? 'folderCP.gif' : 'folderC.gif');

			// Add the current folder
			// Cloud API: change row
			$return .= $this->generateImage($folderImg, '').' <a href="' . $this->addToUrl('node='.$currentEncoded) . '" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'"><strong>'.specialchars(basename($currentFolder)).'</strong></a>';
			
			if(isset($arrMounts[$currentFolder]))
			{
				$return .= sprintf(' <span><a href="%s" title="%s">[%s: %s]</a></span>',				
				$this->addToUrl('do=cloudapi&table=tl_cloud_mount'),
				$arrMounts[$currentFolder]['name'],
				//$this->generateImage('system/modules/cloud-api/assets/mount.png', '', 'style="margin-right:2px"'),				
				$arrMounts[$currentFolder]['title'], $arrMounts[$currentFolder]['path']);	
			}

			$return .= '</div> <div class="tl_right">';

			// Paste buttons
			if ($arrClipboard !== false && \Input::get('act') != 'select')
			{
				$imagePasteInto = $this->generateImage('pasteinto.gif', $GLOBALS['TL_LANG'][$this->strTable]['pasteinto'][0]);
				$return .= (($arrClipboard['mode'] == 'cut' || $arrClipboard['mode'] == 'copy') && preg_match('/^' . preg_quote($arrClipboard['id'], '/') . '/i', $currentFolder)) ? $this->generateImage('pasteinto_.gif') : '<a href="'.$this->addToUrl('act='.$arrClipboard['mode'].'&amp;mode=2&amp;pid='.$currentEncoded.(!is_array($arrClipboard['id']) ? '&amp;id='.$arrClipboard['id'] : '')).'" title="'.specialchars($GLOBALS['TL_LANG'][$this->strTable]['pasteinto'][1]).'" onclick="Backend.getScrollOffset()">'.$imagePasteInto.'</a> ';
			}
			// Default buttons (do not display buttons for mounted folders)
			elseif (!$mount)
			{
				$return .= (\Input::get('act') == 'select') ? '<input type="checkbox" name="IDS[]" id="ids_'.md5($currentEncoded).'" class="tl_tree_checkbox" value="'.$currentEncoded.'">' : $this->generateButtons(array('id'=>$currentEncoded), $this->strTable);
			}

			$return .= '</div><div style="clear:both"></div></li>';

			// Call the next node
			if (!empty($content) && $session['filetree'][$md5] == 1)
			{
				$return .= '<li class="parent" id="filetree_'.$md5.'"><ul class="level_'.$level.'">';
				$return .= $this->generateTree($folders[$f], ($intMargin + $intSpacing), false, $protected, $arrClipboard, $arrMounts);
				$return .= '</ul></li>';
			}
		}

		// Process files
		for ($h=0; $h<count($files); $h++)
		{
			$thumbnail = '';
			$popupWidth = 600;
			$popupHeight = 260;
			$currentFile = str_replace(TL_ROOT.'/', '', $files[$h]);

			$objFile = new \File($currentFile);

			if (!empty($this->arrValidFileTypes) && !in_array($objFile->extension, $this->arrValidFileTypes))
			{
				continue;
			}

			$currentEncoded = $this->urlEncode($currentFile);
			$return .= "\n  " . '<li class="tl_file" onmouseover="Theme.hoverDiv(this,1)" onmouseout="Theme.hoverDiv(this,0)"><div class="tl_left" style="padding-left:'.($intMargin + $intSpacing).'px">';

			// Generate the thumbnail
			if ($objFile->isGdImage && $objFile->height > 0)
			{
				$popupWidth = ($objFile->width > 600) ? ($objFile->width + 61) : 661;
				$popupHeight = ($objFile->height + 245);
				$thumbnail .= ' <span class="tl_gray">('.$this->getReadableSize($objFile->filesize).', '.$objFile->width.'x'.$objFile->height.' px)</span>';

				if ($GLOBALS['TL_CONFIG']['thumbnails'] && $objFile->height <= $GLOBALS['TL_CONFIG']['gdMaxImgHeight'] && $objFile->width <= $GLOBALS['TL_CONFIG']['gdMaxImgWidth'])
				{
					$_height = ($objFile->height < 50) ? $objFile->height : 50;
					$_width = (($objFile->width * $_height / $objFile->height) > 400) ? 90 : '';

					$thumbnail .= '<br><img src="' . TL_FILES_URL . \Image::get($currentEncoded, $_width, $_height) . '" alt="" style="margin:0 0 2px -19px">';
				}
			}
			else
			{
				$thumbnail .= ' <span class="tl_gray">('.$this->getReadableSize($objFile->filesize).')</span>';
			}

			$strFileNameEncoded = utf8_convert_encoding(specialchars(basename($currentFile)), $GLOBALS['TL_CONFIG']['characterSet']);

			// No popup links for templates and in the popup file manager
			if ($this->strTable == 'tl_templates' || basename(\Environment::get('scriptName')) == 'files.php')
			{
				$return .= $this->generateImage($objFile->icon).' '.$strFileNameEncoded.$thumbnail.'</div> <div class="tl_right">';
			}
			else
			{
				$return .= '<a href="contao/popup.php?src='.base64_encode($currentEncoded).'" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['view']).'" onclick="Backend.openModalIframe({\'width\':'.$popupWidth.',\'title\':\''.str_replace("'", "\\'", $strFileNameEncoded).'\',\'url\':this.href,\'height\':'.$popupHeight.'});return false">' . $this->generateImage($objFile->icon, $objFile->mime).'</a> '.$strFileNameEncoded.$thumbnail.'</div> <div class="tl_right">';
			}

			// Buttons
			if ($arrClipboard !== false && \Input::get('act') != 'select')
			{
				$_buttons = '&nbsp;';
			}
			else
			{
				$_buttons = (\Input::get('act') == 'select') ? '<input type="checkbox" name="IDS[]" id="ids_'.md5($currentEncoded).'" class="tl_tree_checkbox" value="'.$currentEncoded.'">' : $this->generateButtons(array('id'=>$currentEncoded), $this->strTable);
			}

			$return .= $_buttons . '</div><div style="clear:both"></div></li>';
		}

		return $return;
	}


	/**
	 * store last sync time 
	 */
	public function sync()
	{
		$strReturn = parent::sync();
		
		$this->import('Config');		
		$this->Config->add("\$GLOBALS['TL_CONFIG']['filemangerSynced']", time());
		$this->Config->save();
		
		return $strReturn;
	}
}
