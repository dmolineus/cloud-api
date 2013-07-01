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
 * extends files model for fetching cloud nodes entries
 */
class CloudNodeModel extends FilesModel
{

	/**
	 * children noded
	 * @var array
	 */
	protected $objChildren;

	/**
	 * Pathinfo
	 * @var array
	 */
	protected $arrPathinfo = array();
	
	/**
	 * cloud api name
	 * @var string
	 */
	protected static $objApi = null;
	
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_cloud_node';
	

	/**
	 * initialize object and set reference to the cloud api
	 * 
	 * @return void
	 * @param string path
	 * @param string Netzmacht\Cloud\Api\CloudApi
	 */
	public function __construct(\Database\Result $objResult=null, $strPath=null, \Netzmacht\Cloud\Api\CloudApi $objApi=null)
	{
		parent::__construct($objResult);
		
		if($strPath !== null)
		{
			$this->path = $strPath;	
		}
		
		if($objApi === null && static::$objApi === null && $objResult !== null)
		{
			static::$objApi = Netzmacht\Cloud\Api\CloudApiManager::getApi($objResult->cloudapi, 'id');			
		}
		elseif($objApi !== null) 
		{
			static::$objApi = $objApi;
		}
	}
	
	
	/**
	 * provide access for attributes
	 * 
	 * support following keys:
	 *  - string basename
	 *  - string extension
	 *  - string dirname
	 *  - string icon
	 *  - bool isCached
	 *  - bool isGdImage
	 *  - string mime
	 *  - string name
	 *  - string cacheKey
	 *  - string downloadUrl
	 *  - string downloadUrlExpires	 
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
		// value is cached
		if(isset($this->arrCache[$strKey]))
		{
			return $this->arrCache[$strKey];
		}
		
		// generate value
		switch ($strKey)
		{
			case 'cacheKey':
				$this->arrCache[$strKey] = sprintf('/%s%s',
					static::$objApi->name,
					$this->path
				);				 
				break;
				
			case 'cacheThumbnailKey':
				$arrPathInfo = pathinfo($this->path);
				
				$this->arrCache[$strKey] = sprintf(
					'%s/%s/%s_thumb.%s.jpg', 
					static::$objApi->name, 
					$arrPathInfo['dirname'], 
					$arrPathInfo['filename'], 
					$arrPathInfo['extension']
				); 
				break;
		
			case 'dirname':
				if(isset($this->arrData[$strKey]))
				{
					return $this->arrData[$strKey];
				}
				
				if (!isset($this->arrPathinfo[$strKey]))
				{
					$this->arrPathinfo = pathinfo($this->path);
				}
				$this->arrCache[$strKey] = $this->arrPathinfo[$strKey];
				break;
			
			case 'icon':
				$arrMimeInfo = $this->getMimeInfo();
				$this->arrCache[$strKey] = $arrMimeInfo[1];			 
				break;
				
			case 'isCached':
				$this->arrCache[$strKey] = Netzmacht\Cloud\Api\CloudCache::isCached($this->cacheKey) && ($this->version == $this->cachedVersion);
				break;
				
			case 'isGdImage':
				$this->arrCache[$strKey] = in_array($this->extension, array('gif', 'jpg', 'jpeg', 'png'));
				break;
				
			case 'mime':
				$arrMimeInfo = $this->getMimeInfo();
				$this->arrCache[$strKey] = $arrMimeInfo[0];
				break;
				
			case 'basename':
				return $this->name;
				break;				
				
			default:
				return parent::__get($strKey); 
		}
				
		return $this->arrCache[$strKey];
	}


	/**
	 * copy file to a new place
	 * 
	 * @param string new path
	 * @return mixed
	 */
	public function copy($strNewPath)
	{
		// actually this method should be abstract but we cannot declare class abstract
		return;		
	}
	
	
	/**
	 * Return the number of records matching certain criteria
	 * 
	 * @param mixed $strColumn An optional property name
	 * @param mixed $varValue  An optional property value
	 * 
	 * @return integer The number of matching rows
	 */
	public static function countBy($strColumn=null, $varValue=null)
	{
		if (static::$strTable == '')
		{
			return 0;
		}

		$strQuery = \Model\QueryBuilder::count(array
		(
			'table'  => static::$strTable,
			'column' => $strColumn,
			'value'  => $varValue
		));

		return \Database::getInstance()->prepare($strQuery)->execute($varValue)->count;
	}
	
	
	/**
	 * get content of a file
	 * 
	 * @return string
	 */	
	public function downloadFile()
	{
		// actually this method should be abstract but we cannot declare class abstract
		return;
	}
	
	
	/**
	 * get children nodes
	 * 
	 * @return array
	 */
	public function getChildren()
	{
		if($this->objChildren instanceof Netzmacht\Cloud\Api\CloudNodeModelCollection) 
		{
			return $this->objChildren;
		}
		
		$this->objChildren = static::findByPid($this->id === null ? 0 : $this->id);
		
		return $this->objChildren;
	}
	
	
	/**
	 * get content of the thumbnail of a file
	 * 
	 * @return string
	 */	
	public function getThumbnail()
	{
		// actually this method should be abstract but we cannot declare class abstract
		return;
	}
	
		
	/**
	 * move file to a new place
	 * 
	 * @param string new path
	 */
	public function move($strNewPath)
	{
		// actually this method should be abstract but we cannot declare class abstract
		return;
	}
	
	
	/**
	 * limit api
	 * 
	 * @param int api pid
	 */
	public static function setApi($objApi)
	{
		static::$objApi = $objApi;		
	}
	

	/**
	 * get content of a file
	 * 
	 * @param string $mxdPathOrFile open file handle or local path
	 * @param string
	 */	
	public function uploadFile($mxdPathOrFile)
	{
		// actually this method should be abstract but we cannot declare class abstract
	}
	
	
	/**
	 * Find records and return the model or model collection
	 * 
	 * Supported options:
	 * 
	 * * column: the field name
	 * * value:  the field value
	 * * limit:  the maximum number of rows
	 * * offset: the number of rows to skip
	 * * order:  the sorting order
	 * * eager:  load all related records eagerly
	 * 
	 * @param array $arrOptions The options array
	 * 
	 * @return \Model|\Model\Collection|null A model, model collection or null if the result is empty
	 */
	protected static function find(array $arrOptions)
	{
		if (static::$strTable == '')
		{
			return null;
		}

		$arrOptions['table'] = static::$strTable;
		$strQuery = \Model\QueryBuilder::find($arrOptions);

		$objStatement = \Database::getInstance()->prepare($strQuery);

		// Defaults for limit and offset
		if (!isset($arrOptions['limit']))
		{
			$arrOptions['limit'] = 0;
		}
		if (!isset($arrOptions['offset']))
		{
			$arrOptions['offset'] = 0;
		}

		// Limit
		if ($arrOptions['limit'] > 0 || $arrOptions['offset'] > 0)
		{
			$objStatement->limit($arrOptions['limit'], $arrOptions['offset']);
		}

		$objStatement = static::preFind($objStatement);

		// cached or uncached
		if(isset($arrOptions['uncached']))
		{
			$objResult = $objStatement->executeUncached($arrOptions['value']);	
		}
		else
		{
			$objResult = $objStatement->execute($arrOptions['value']);
		}

		if ($objResult->numRows < 1)
		{
			return null;
		}

		$objResult = static::postFind($objResult);
		
		
		// return collection
		if(($arrOptions['return'] != 'Model'))
		{
			return new \Netzmacht\Cloud\Api\CloudNodeModelCollection($objResult, static::$strTable);
		}
		
		if(static::$objApi !== null)
		{
			$strClass = static::$objApi->modelClass;
		}
		else
		{
			$objApi = Netzmacht\Cloud\Api\CloudApiManager::getApi($objResult->cloudapi);
			
			if($objApi !== null)
			{
				$strClass = static::$objApi->modelClass;
			}
			else
			{
				$strClass = 'CloudNodeModel';
			}
		}
		
		return new $strClass($objResult, null, static::$objApi);
	}	
	
	
	/**
	 * 
	 */
	public static function findMultipleByIds($arrIds, array $arrOptions=array())
	{
		if (!is_array($arrIds) || empty($arrIds))
		{
			return null;
		}
		
		$arrIds = implode(',', array_map('intval', $arrIds));
		
		$t = static::$strTable;
		$db = \Database::getInstance();
		
		return static::findBy
		(
			array( "$t.id IN(" . $arrIds . ")"),
			null,
			array('order' => $db->findInSet("$t.id", $arrIds) )
		);
	}
	
	
	/**
	 * find model by path. if it does not exists in the database we try to fetch it from the cloud service by calling the getMetaData
	 * 
	 * @param string path
	 * @param search in cloud service if node was not found in database
	 * @return \CloudNodeModel
	 */
	public static function findOneByPath($strPath, $blnSearchCloudService=true, $arrOptions=array())
	{
		$objNode = parent::findOneByPath($strPath, $arrOptions);
		
		if($objNode !== null)
		{
			return $objNode;
		}
		
		if(!$blnSearchCloudService)
		{
			return null;
		}
		
		if(static::$objApi !== null)
		{
			$strClass = static::$objApi->modelClass;
		}
		else 
		{
			$strClass = 'CloudNodeModel';
		}
		
		$objNode = new $strClass(null, null, static::$objApi);
		$objNode->path = $strPath;
		
		if($strPath != '/' && $strPath != '')
		{
			$objNode->getMetaData();	
		}
		
		return $objNode;
	}


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
	 * save node, make sure that cloudapi is saved
	 * 
	 * @bool forceinsert
	 */
	public function save($blnForceInsert=false)
	{
		if(!isset($this->cloudapi) || $this->cloudapi == '')
		{
			$this->cloudapi = static::$objApi->id;
		}
		
		parent::save($blnForceInsert);
	}


	/**
	 * use pre find function to limit cloud models
	 * 
	 * @param Statmenet query statement
	 * @return Statement
	 */
	protected static function preFind(\Database\Statement $objStatement)
	{
		if(static::$objApi !== null)
		{
			$strQuery = $objStatement->query;
			$strSubQuery = ' cloudapi=' . static::$objApi->id;
			
			// inject where parte for api limiting
			if(($intPos = stripos($strQuery, 'where')) !== null)
			{
				$strSubQuery .= ' AND ';		
				$strQuery = preg_replace('/(WHERE\s*)([^\s]*\s)/i', "WHERE" . $strSubQuery . '\2', $strQuery);
			}
			else 
			{
				$strSubQuery = ' WHERE ' . $strSubQuery;				
				$strQuery = preg_replace('/FROM\s*([A-z0-9_\-])*/i', "\0" . $strSubQuery, $strQuery);
			}
			
			$objStatement->prepare($strQuery);			
		}
		
		return $objStatement;
	}
	
}
