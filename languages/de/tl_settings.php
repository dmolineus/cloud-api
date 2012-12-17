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
 
$GLOBALS['TL_LANG']['tl_settings']['cloudapi_legend'] 						= 'Cloud API';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncInterval'][0] 				= 'Synchronisationsinterval';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncInterval'][1] 				= 'Geben Sie an, in welchen Abständen die Synchronisation ausgeführt werden kann.';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['600'] 	= '10 Minuten';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['1800'] 	= '30 Minuten';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['3600'] 	= '1 Stunde';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['7200'] 	= '2 Stunden';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['86400'] 	= '1 Tag';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncIntervalOptions']['604800'] = '1 Woche';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadTime'][0] 			= 'Downloadzeit beschränken';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadTime'][1] 			= 'Dauer des Downloads ins Sekunden. Beim Download von Dateien der Cloud-Services wird'
																			. ' überprüft, wie viel Zeit vergeht. Wird diese Zeit überschritten, wird eine'
																			. ' Weiterleitung durchgeführt. Ändern sie diese Zeit, falls es zu einem Timeout bei'
																			. ' der Synchronisation kommt';
																	
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadLimit'][0] 			= 'Anzahl der Dateidownloads beschränken';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiSyncDownloadLimit'][1] 			= 'Viele Dateien sollen heruntergeladen werden, bevor eine Weiterleitung stattfindet';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerIntegration'][0] 	= 'Integration in den Dateimanager';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerIntegration'][1] 	= 'Die Einhängepfade werden im Dateimanager angezeigt. Außerdem wird eine allgemeine'
																			. ' Synchronisationsübersicht verwendet.';

$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerManageMounts'][0] 	= 'Einhängepunkte im Dateimanager verwalten';
$GLOBALS['TL_LANG']['tl_settings']['cloudapiFileManagerManageMounts'][1]	= 'Sie können die Schaltfläche zur Verwaltung der Einhängepunkte auch im Dateimanager anzeigen.';