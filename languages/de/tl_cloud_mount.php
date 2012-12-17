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
 
// mount
$GLOBALS['TL_LANG']['tl_cloud_mount']['mountLocal'] 			= 'Lokal:';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mountCloud'] 			= 'Cloud:';

// legends
$GLOBALS['TL_LANG']['tl_cloud_mount']['general_legend']			= 'Allgemeine Einstellungen';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mounts_legend']		 	= 'Einhängepunkt definieren';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_legend']			= 'Sychnronisation';

// buttons
$GLOBALS['TL_LANG']['tl_cloud_mount']['create'] 				= array('Einhängepunkt erstellen', 'Einen neuen Einhängepunkt erstellen');
$GLOBALS['TL_LANG']['tl_cloud_mount']['delete'] 				= array('Einhängepunkt "%s" entfernen', 'Einhängepunkt der ID "%s" entfernen');
$GLOBALS['TL_LANG']['tl_cloud_mount']['edit']					= array('Einhängepunkt "%s" bearbeiten', 'Einhängepunkt der ID "%s" bearbeiten');
$GLOBALS['TL_LANG']['tl_cloud_mount']['copy'] 					= array('Einhängepunkt "%"s duplizieren', 'Einhängepunkt der ID "%s" duplizieren');
$GLOBALS['TL_LANG']['tl_cloud_mount']['goto'] 					= array('Zur Dateiverwaltung gehen', 'Zur Dateiverwaltung gehen');
$GLOBALS['TL_LANG']['tl_cloud_mount']['enable'] 				= array('Einhängepunkt aktivieren/deaktivieren', 'Einhängepunkt aktivieren/deaktivieren');

// fields
$GLOBALS['TL_LANG']['tl_cloud_mount']['name'][0]				= 'Name';
$GLOBALS['TL_LANG']['tl_cloud_mount']['name'][1]				= 'Der Name dient als interne Bezeichnung.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['description'][0]			= 'Beschreibung';
$GLOBALS['TL_LANG']['tl_cloud_mount']['description'][1]			= 'Die Beschreibung können Sie nutzen um die Funktion zu beschreiben.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['pid'][0]					= 'Cloud Service';
$GLOBALS['TL_LANG']['tl_cloud_mount']['pid'][1]					= 'Wählen Sie den Cloud Service, den sie zur Installation verwenden wollen';
$GLOBALS['TL_LANG']['tl_cloud_mount']['enabled'][0]				= 'Aktiviert';
$GLOBALS['TL_LANG']['tl_cloud_mount']['enabled'][1]				= 'Sie können den Einhängepunkt deaktivieren. Dieser wird dann nicht ausgeführt.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['cloudId'][0]				= 'Ordner des Cloud Services';
$GLOBALS['TL_LANG']['tl_cloud_mount']['cloudId'][1]				= 'Wählen Sie einen Ordner des Cloud Services aus, der in die Dateiverwaltung synchronisiert werden soll.';
$GLOBALS['TL_LANG']['tl_cloud_mount']['localId'][0]				= 'Ordner der Dateiverwaltung';
$GLOBALS['TL_LANG']['tl_cloud_mount']['localId'][1]				= 'Wählen Sie einen Ordner der Dateiverwaltung aus, der in mit einem Ordner des Cloud-Services synchronisiert werden soll.';

$GLOBALS['TL_LANG']['tl_cloud_mount']['mode'][0]				= 'Synchronisationsrichtung';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode'][1]				= 'Geben sie an in welche Richtung synchronisiert werden soll. Sie können bidirektional synchronisieren oder nur die Synchronisation auf einer Seite durchführen.';

$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['c2l']		= 'Cloud-Ordner in lokalen Ordner';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['l2c']		= 'Lokalen Ordner in Cloud-Ordner';
$GLOBALS['TL_LANG']['tl_cloud_mount']['mode_values']['s2w']		= 'bidirektional synchronisieren';

$GLOBALS['TL_LANG']['tl_cloud_mount']['options'][0]				= 'Optionen';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options'][1]				= 'Definieren Sie, welche Aktionen bei der Synchronisation durchgeführt werden sollen';

$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['create']	= 'Dateien und Ordner erstellen';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['update']	= 'Dateien aktualisieren';
$GLOBALS['TL_LANG']['tl_cloud_mount']['options_values']['delete']	= 'Dateien und Ordner löschen';
