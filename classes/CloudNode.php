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

namespace Netzmacht\Cloud\Api;
use System;

/**
 * Abstract class for defining interface of cloud node and provide
 * common features
 */
abstract class CloudNode extends System
{
	
	/**
	 * children noded
	 * @var array
	 */
	protected $arrChildren;
	
	/**
	 * Pathinfo
	 * @var array
	 */
	protected $arrPathinfo = array();
		
	/**
	 * cloud api instance
	 * @var Netzmacht\Cloud\Api\CloudApi
	 */
	protected $objApi;
	
	/**
	 * path of current object
	 * @var
	 */
	protected $strPath;	


	/**
	 * initialize object and set reference to the cloud api
	 * 
	 * @return void
	 * @param string path
	 * @param string Netzmacht\Cloud\Api\CloudApi
	 */
	public function __construct($strPath, $objApi)
	{
		$this->strPath = $strPath;

		$this->objApi = $objApi;
	}
	
	
	/**
	 * provide access for attributes
	 * 
	 * support following keys:
	 *  - string basename
	 *  - string extension
	 *  - string icon
	 *  - bool isCached
	 *  - bool isGdImage
	 *  - bool isMetaCached
	 *  - string mime
	 *  - string name
	 * 
	 * following keys has to be created of the implementation of this class
	 *  - string cacheKey
	 *  - string cacheMetaKey
	 *  - string downloadUrl	 
	 *  - string type 'file' or 'folder'
	 *  - bool hasThumbnail
	 *  - string path	 
	 *  - int filesize
	 * 
	 * @param string name of attribute
	 * @return mixed
	 * 
	 */
	public function __get($strKey)
	{
		if(isset($this->arrCache[$strKey])) 
		{
			return $this->arrCache[$strKey];
		}
		
		switch ($strKey)
		{
			case 'extension':
				if (!isset($this->arrPathinfo[$strKey]))
				{
					$this->arrPathinfo = pathinfo($this->strPath);
				}
				$this->arrCache[$strKey] = $this->arrPathinfo['extension'];
				break;
			
			case 'icon':
				$arrMimeInfo = $this->getMimeInfo();
				$this->arrCache[$strKey] = $arrMimeInfo[1];			 
				break;
				
			case 'isCached':
				$this->arrCache[$strKey] = CloudCache::isCached($this->cacheKey);
				break;
				
			case 'isGdImage':
				$this->arrCache[$strKey] = in_array($this->extension, array('gif', 'jpg', 'jpeg', 'png'));
				break;
				
			case 'isMetaCached':
				$this->arrCache[$strKey] = CloudCache::isCached($this->cacheMetaKey);
				break;
				
			case 'mime':
				$arrMimeInfo = $this->getMimeInfo();
				$this->arrCache[$strKey] = $arrMimeInfo[0];
				break;
				
			case 'name':
			case 'basename':
				if (!isset($this->arrPathinfo[$strKey]))
				{
					$this->arrPathinfo = pathinfo($this->strPath);
				}
				$this->arrCache[$strKey] = $this->arrPathinfo['basename'];
				break;			 
		}
				
		if(!isset($this->arrCache[$strKey])) 
		{
			return parent::__get($strKey);
		}	
				
		return $this->arrCache[$strKey];
		
	}


	/**
	 * delete current node
	 * 
	 * @return bool
	 */
	abstract public function delete();
	
	
	/**
	 * Return the mime type and icon of the file based on its extension
	 * 
	 * @author Leo Feyer <http://contao.org>
	 * @link \Contao\File
	 * @return array An array with mime type and icon name
	 */
	protected function getMimeInfo()
	{
		$arrMimeTypes = array
		(
			// Application files
			'xl'	=> array('application/excel', 'iconOFFICE.gif'),
			'xls'	=> array('application/excel', 'iconOFFICE.gif'),
			'hqx'	=> array('application/mac-binhex40', 'iconPLAIN.gif'),
			'cpt'	=> array('application/mac-compactpro', 'iconPLAIN.gif'),
			'bin'	=> array('application/macbinary', 'iconPLAIN.gif'),
			'doc'	=> array('application/msword', 'iconOFFICE.gif'),
			'word'	=> array('application/msword', 'iconOFFICE.gif'),
			'cto'	=> array('application/octet-stream', 'iconCTO.gif'),
			'dms'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'lha'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'lzh'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'exe'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'class' => array('application/octet-stream', 'iconPLAIN.gif'),
			'so'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'sea'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'dll'	=> array('application/octet-stream', 'iconPLAIN.gif'),
			'oda'	=> array('application/oda', 'iconPLAIN.gif'),
			'pdf'	=> array('application/pdf', 'iconPDF.gif'),
			'ai'	=> array('application/postscript', 'iconPLAIN.gif'),
			'eps'	=> array('application/postscript', 'iconPLAIN.gif'),
			'ps'	=> array('application/postscript', 'iconPLAIN.gif'),
			'pps'	=> array('application/powerpoint', 'iconOFFICE.gif'),
			'ppt'	=> array('application/powerpoint', 'iconOFFICE.gif'),
			'smi'	=> array('application/smil', 'iconPLAIN.gif'),
			'smil'	=> array('application/smil', 'iconPLAIN.gif'),
			'mif'	=> array('application/vnd.mif', 'iconPLAIN.gif'),
			'odc'	=> array('application/vnd.oasis.opendocument.chart', 'iconOFFICE.gif'),
			'odf'	=> array('application/vnd.oasis.opendocument.formula', 'iconOFFICE.gif'),
			'odg'	=> array('application/vnd.oasis.opendocument.graphics', 'iconOFFICE.gif'),
			'odi'	=> array('application/vnd.oasis.opendocument.image', 'iconOFFICE.gif'),
			'odp'	=> array('application/vnd.oasis.opendocument.presentation', 'iconOFFICE.gif'),
			'ods'	=> array('application/vnd.oasis.opendocument.spreadsheet', 'iconOFFICE.gif'),
			'odt'	=> array('application/vnd.oasis.opendocument.text', 'iconOFFICE.gif'),
			'wbxml' => array('application/wbxml', 'iconPLAIN.gif'),
			'wmlc'	=> array('application/wmlc', 'iconPLAIN.gif'),
			'dmg'	=> array('application/x-apple-diskimage', 'iconRAR.gif'),
			'dcr'	=> array('application/x-director', 'iconPLAIN.gif'),
			'dir'	=> array('application/x-director', 'iconPLAIN.gif'),
			'dxr'	=> array('application/x-director', 'iconPLAIN.gif'),
			'dvi'	=> array('application/x-dvi', 'iconPLAIN.gif'),
			'gtar'	=> array('application/x-gtar', 'iconRAR.gif'),
			'inc'	=> array('application/x-httpd-php', 'iconPHP.gif'),
			'php'	=> array('application/x-httpd-php', 'iconPHP.gif'),
			'php3'	=> array('application/x-httpd-php', 'iconPHP.gif'),
			'php4'	=> array('application/x-httpd-php', 'iconPHP.gif'),
			'php5'	=> array('application/x-httpd-php', 'iconPHP.gif'),
			'phtml' => array('application/x-httpd-php', 'iconPHP.gif'),
			'phps'	=> array('application/x-httpd-php-source', 'iconPHP.gif'),
			'js'	=> array('application/x-javascript', 'iconJS.gif'),
			'psd'	=> array('application/x-photoshop', 'iconPLAIN.gif'),
			'rar'	=> array('application/x-rar', 'iconRAR.gif'),
			'fla'	=> array('application/x-shockwave-flash', 'iconSWF.gif'),
			'swf'	=> array('application/x-shockwave-flash', 'iconSWF.gif'),
			'sit'	=> array('application/x-stuffit', 'iconRAR.gif'),
			'tar'	=> array('application/x-tar', 'iconRAR.gif'),
			'tgz'	=> array('application/x-tar', 'iconRAR.gif'),
			'xhtml' => array('application/xhtml+xml', 'iconPLAIN.gif'),
			'xht'	=> array('application/xhtml+xml', 'iconPLAIN.gif'),
			'zip'	=> array('application/zip', 'iconRAR.gif'),

			// Audio files
			'm4a'	=> array('audio/x-m4a', 'iconAUDIO.gif'),
			'mp3'	=> array('audio/mp3', 'iconAUDIO.gif'),
			'wma'	=> array('audio/wma', 'iconAUDIO.gif'),
			'mpeg'	=> array('audio/mpeg', 'iconAUDIO.gif'),
			'wav'	=> array('audio/wav', 'iconAUDIO.gif'),
			'mid'	=> array('audio/midi', 'iconAUDIO.gif'),
			'midi'	=> array('audio/midi', 'iconAUDIO.gif'),
			'aif'	=> array('audio/x-aiff', 'iconAUDIO.gif'),
			'aiff'	=> array('audio/x-aiff', 'iconAUDIO.gif'),
			'aifc'	=> array('audio/x-aiff', 'iconAUDIO.gif'),
			'ram'	=> array('audio/x-pn-realaudio', 'iconAUDIO.gif'),
			'rm'	=> array('audio/x-pn-realaudio', 'iconAUDIO.gif'),
			'rpm'	=> array('audio/x-pn-realaudio-plugin', 'iconAUDIO.gif'),
			'ra'	=> array('audio/x-realaudio', 'iconAUDIO.gif'),

			// Images
			'bmp'	=> array('image/bmp', 'iconBMP.gif'),
			'gif'	=> array('image/gif', 'iconGIF.gif'),
			'jpeg'	=> array('image/jpeg', 'iconJPG.gif'),
			'jpg'	=> array('image/jpeg', 'iconJPG.gif'),
			'jpe'	=> array('image/jpeg', 'iconJPG.gif'),
			'png'	=> array('image/png', 'iconTIF.gif'),
			'tiff'	=> array('image/tiff', 'iconTIF.gif'),
			'tif'	=> array('image/tiff', 'iconTIF.gif'),

			// Mailbox files
			'eml'	=> array('message/rfc822', 'iconPLAIN.gif'),

			// Text files
			'asp'	=> array('text/asp', 'iconPLAIN.gif'),
			'css'	=> array('text/css', 'iconCSS.gif'),
			'html'	=> array('text/html', 'iconHTML.gif'),
			'htm'	=> array('text/html', 'iconHTML.gif'),
			'shtml' => array('text/html', 'iconHTML.gif'),
			'txt'	=> array('text/plain', 'iconPLAIN.gif'),
			'text'	=> array('text/plain', 'iconPLAIN.gif'),
			'log'	=> array('text/plain', 'iconPLAIN.gif'),
			'rtx'	=> array('text/richtext', 'iconPLAIN.gif'),
			'rtf'	=> array('text/rtf', 'iconPLAIN.gif'),
			'xml'	=> array('text/xml', 'iconPLAIN.gif'),
			'xsl'	=> array('text/xml', 'iconPLAIN.gif'),

			// Videos
			'mp4'	=> array('video/mp4', 'iconVIDEO.gif'),
			'm4v'	=> array('video/x-m4v', 'iconVIDEO.gif'),
			'mov'	=> array('video/mov', 'iconVIDEO.gif'),
			'wmv'	=> array('video/wmv', 'iconVIDEO.gif'),
			'webm'	=> array('video/webm', 'iconVIDEO.gif'),
			'qt'	=> array('video/quicktime', 'iconVIDEO.gif'),
			'rv'	=> array('video/vnd.rn-realvideo', 'iconVIDEO.gif'),
			'avi'	=> array('video/x-msvideo', 'iconVIDEO.gif'),
			'movie' => array('video/x-sgi-movie', 'iconVIDEO.gif')
		);		

		// Extend the default lookup array
		if (is_array($GLOBALS['TL_MIME']) && !empty($GLOBALS['TL_MIME']))
		{
			$arrMimeTypes = array_merge($arrMimeTypes, $GLOBALS['TL_MIME']);
		}

		// Fallback to application/octet-stream
		if (!isset($arrMimeTypes[$this->extension]))
		{
			return array('application/octet-stream', 'iconPLAIN.gif');
		}

		return $arrMimeTypes[$this->extension];
	}


	/**
	 * get content of a file
	 * 
	 * @return string
	 */	
	abstract public function getFile();
	
	
	/**
	 * upload file
	 * 
	 * @param string file path for uploading
	 */
	abstract public function putFile($strPath);


	/**
	 * get path to thumbnail
	 * 
	 * @return string
	 */
	abstract public function getThumbnail();


	/**
	 * move file to a new place
	 * 
	 * @param string new path
	 */
	abstract public function move($strNewPath);

	/**
	 * copy file to a new place
	 * 
	 * @param string new path
	 * @param bool set bool if you want to get a node of the new file
	 * @return mixed
	 */
	abstract public function copy($strNewPath, $blnReturnNode=false);

}
