<?php
/* $Id$ */
/**
 * /whomp/includes/installation.php
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
 
 // get which installation step we are on
 $install_step = $_POST['install_step'];
?>