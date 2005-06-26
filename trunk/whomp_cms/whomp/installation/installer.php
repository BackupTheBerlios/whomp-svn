<?php
/* $Id$ */
/**
 * /whomp/includes/installer.php
 * 
 * Main installation file for Whomp.
 * 
 * @package Whomp
 * @copyright © 2005 Schmalls / Joshua Thompson / All Rights Reserved
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
 * @version 0.0.0
 * @since 0.0.0
 * @link http://www.schmalls.com
 */

 /**
  * Make sure all subsequent files are only included by this file
  */
 define('_VALID_WHOMP', true);
 
 /**
  * The whomp base path
  * 
  * @global string $_whomp_base_path
  */
 $_whomp_base_path = (string)$_REQUEST['base_path'];
 
 /**
  * The whomp base url
  * 
  * @global string $_whomp_base_url
  */
 $_whomp_base_url = (string)$_REQUEST['base_url'];
 
 /**
  * The whomp storage path
  * 
  * @global string $_whomp_storage_path
  */
 $_whomp_storage_path = (string)$_REQUEST['storage_url'];
 
 /**
  * Require the whomp configuration file
  */
 require_once($_whomp_base_path . '/configuration.php');
 
 /**
  * Access to the configuration options
  * 
  * @global $_whomp_configuration
  */
 $_whomp_configuration = new Whomp_Configuration();
 
 // check if whomp is already installed
 if ($_whomp_configuration->installed = true) {
	 // if so, tell user to remove the installation directory and exit
	 echo <<<HTML
<html>
	<head>
		<title>Please remove the installation directory.</title>
	</head>
	<body>
		<p>Please remove the installation directory.</p>
	</body>
</html>
HTML;
	 exit;
 } // end if
 
 /**
  * Require the functions file
  */
 require_once($_whomp_storage_path . '/includes/functions.php');
 
 /**
  * Information to be included in the head of a document
  * 
  * @global array $_whomp_head_data
  */
 $_whomp_head_data = array('base' => '<base href="' . $_whomp_base_url . '" />',
 						   'link' => array(),
						   'meta' => array('generator' => '<meta name="generator" value="' . $_whomp_configuration->version_information . '" />'),
						   'script' => array(),
						   'style' => array(),
						   'title' => 'Whomp CMS Installer');
 
 /**
  * Require the installer html file
  */
 require_once($_whomp_storage_path . '/installation/whomp_installer_html.php');
 
 /**
  * Require the {@link Whomp_Ajax Whomp_Ajax} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_ajax.php');
 
 // create the ajax options
 $whomp_ajax_options = array('_callback' => $_whomp_storage_url . '/installer.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_url_path . '&storage_path=' . $_whomp_storage_path,
 							 '_method' => 'POST',
							 '_return_type' => 'xml',
							 '_async' => 'true');
							 
 /**
  * Access to the whomp ajax class
  * 
  * @global class $_whomp_installer_ajax
  */
 $_whomp_installer_ajax = new Whomp_Ajax($whomp_ajax_options);
 
 // check if one of the functions has been called
 $_whomp_installer_ajax->checkRequest();
 
 // register the getPage function
 $_whomp_installer_ajax->registerFunction(array('Whomp_Installer_Html', 'getPageXml'));
 
 // initialize ajax
 $_whomp_installer_ajax->initialize();
 
 // load the first page
 Whomp_Installer_Html::loadPage();
?>