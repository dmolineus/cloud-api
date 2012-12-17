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
 
// legends
$GLOBALS['TL_LANG']['tl_cloud_api']['connection_legend'] = 'Allgemeine Einstellungen';
$GLOBALS['TL_LANG']['tl_cloud_api']['folder_legend'] = 'Ordner mounten';
$GLOBALS['TL_LANG']['tl_cloud_api']['custom_legend'] = 'Cloud Service konfigurieren';

// buttons
$GLOBALS['TL_LANG']['tl_cloud_api']['delete'] = array('Cloud Service deinstallieren', 'Cloud Service %s deinstallieren');
$GLOBALS['TL_LANG']['tl_cloud_api']['edit'] = array('Cloud Service bearbeiten', 'Einstellungen des Cloud Services %s bearbeiten');
$GLOBALS['TL_LANG']['tl_cloud_api']['enable'] = array('Cloud Service aktivieren/deaktivieren', 'Cloud Service aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_cloud_api']['mount'] = array('Einhängepunkte verwalten', 'Einhängepunkte verwalten');
$GLOBALS['TL_LANG']['tl_cloud_api']['install'] = array('Cloud Service installieren', 'Cloud Service installieren');
$GLOBALS['TL_LANG']['tl_cloud_api']['overview'] = array('Synchronisieren', 'Dateisystem und Cloud Services synchronisieren');
$GLOBALS['TL_LANG']['tl_cloud_api']['sync'] = array('Cloud Service synchronisieren', 'Cloud Service ID "%s" synchronisieren');

// fields
$GLOBALS['TL_LANG']['tl_cloud_api']['name'][0] = 'Cloud Service';
$GLOBALS['TL_LANG']['tl_cloud_api']['name'][1] = 'Name des Cloud Services';
$GLOBALS['TL_LANG']['tl_cloud_api']['title'][0] = 'Cloud Service';
$GLOBALS['TL_LANG']['tl_cloud_api']['title'][1] = 'Bezeichnung des Cloud Services (nicht editierbar)';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessTokenLink'] = 'Token anfordern. Danach diese Seite neu laden.';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessTokenConnected'] = 'verbunden mit:';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'][0] = 'Access Token';
$GLOBALS['TL_LANG']['tl_cloud_api']['accessToken'][1] = 'Sie benötigen die Erlaubnis des Cloud-Services, um auf dessen Daten zugreifen zu können. Benutzen Sie den Link und befolgen die Anweisungen des Cloud-Services. Laden Sie danach diese Seite neu.';
$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'][0] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_cloud_api']['enabled'][1] =  'Cloud-Service aktivieren, damit er verwendet werden kann.';
$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'][0] = 'Ordner mounten';
$GLOBALS['TL_LANG']['tl_cloud_api']['mountedFolders'][1] = 'Normalerweise werden alle Ordner des Cloud-Services eingebunden. Geben Sie hier pro Zeile einen Ordner an, wenn sie den Zugriff auf die angegebenen Ordner beschränken wollen. Beispiel: /verzeichnis';
$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'][0] = 'App Einstellungen anpassen';
$GLOBALS['TL_LANG']['tl_cloud_api']['useCustomApp'][1] = 'Je nach Cloud Service ist eine angepasste Konfiguration möglich. Ändern Sie die Einstellungen nur, wenn dies erforderlich ist.';
$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'][0] = 'App Schlüssel';
$GLOBALS['TL_LANG']['tl_cloud_api']['appKey'][1] = 'Geben Sie den Schlüssel der App an.';
$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'][0] = 'App Secret';
$GLOBALS['TL_LANG']['tl_cloud_api']['appSecret'][1] = 'Geben Sie den Secrete Code der App an.';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'][0] = 'Synchronisert am';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncTstamp'][1] = 'Datum der letzten Synchronisation der Daten mit dem Cloud-Service';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'][0] = 'Synchronisation aktiv';
$GLOBALS['TL_LANG']['tl_cloud_api']['syncInProgress'][1] = 'Synchronisation wird derzeit durchgeführt';
$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'][0] = 'oAuth Implementierung';
$GLOBALS['TL_LANG']['tl_cloud_api']['oAuthClass'][1] = 'Der Cloud-Service bietet verschiedene oAuth Implementierungen. Wählen Sie die entsprechende passend zu Ihrer Serverkonfiguration';
$GLOBALS['TL_LANG']['tl_cloud_api']['root'][0] = 'Wurzel Verzeichnis';
$GLOBALS['TL_LANG']['tl_cloud_api']['root'][1] = 'Cloud-Service kann in unterschiedlichen Wurzel-Verzeichnissen arbeiten. So bietet Dropbox beispielsweise die Möglichkeit in einer Sandbox zu arbeiten.';
