<?php
/**
 * index.php
 * 
 * <p>Main index file for Whomp.</p>
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
  * The script start time
  * 
  * @global float $_whomp_start_time
  */
 $_whomp_start_time = microtime(true);
 
 /**
  * Require the {@link whomp_configuration.php Whomp_Configuration} class file
  */
 require_once('whomp_configuration.php');
 
 /**
  * Access to the {@link Whomp_Configuration Whomp_Configuration} class
  * 
  * @global class $_whomp_configuration
  */
 $_whomp_configuration = new Whomp_Configuration();
 
 /**
  * The Whomp base directory
  * 
  * @global string $_whomp_base_path
  */
 $_whomp_base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_FILENAME']);
 
 /**
  * The Whomp storage directory
  * 
  * @global string $_whomp_storage_path
  */
 $_whomp_storage_path = $_whomp_base_path . $_whomp_configuration->base_dir;
 
 /**
  * The Whomp base url
  * 
  * @global string $_whomp_base_url
  */
 $_whomp_base_url = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
 
 // Check if we need to install
 if ($_whomp_configuration->installed == 0) {
	 // if so, redirect to the installation file
	 header('Location: ' . $_whomp_base_url . '/' . $_whomp_configuration->base_dir . '/includes/installation.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_url_path);
 } // end if
 
 /**
  * Require the {@link whomp/includes/functions.php functions} file
  */
 require_once($_whomp_storage_path . '/includes/functions.php');
 
 /**
  * Require the {@link whomp/includes/whomp_cache.php Whomp_Cache} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_cache.php');
 
 // create the options array to pass to the Whomp_Cache class
 $whomp_cache_options = array('cache_dir' => $_whomp_configuration->cache_dir,
 							  'enable_caching' => $_whomp_configuration->cache_enable_caching,
							  'lifetime' => $_whomp_configuration->cache_default_lifetime,
							  'compress_output' => $_whomp_configuration->cache_compress_output);
 
 /**
  * Access to the {@link Whomp_Cache Whomp_Cache} class
  * 
  * @global class $_whomp_cache
  */
 $_whomp_cache = new Whomp_Cache($whomp_cache_options);
 
 /**
  * The requested page and format
  * 
  * @global array $_whomp_requested_page
  */
 $_whomp_requested_page = whomp_get_requested_page();
 
 /**
  * The accept header information
  * 
  * @global array $_whomp_accept
  */
 $_whomp_accept_headers = whomp_get_accept_headers();
 
 // starts caching or outputs page and quits if it is available
 $_whomp_cache->start($_whomp_requested_node, true, true);
 
 /**
  * Require the {@link /whomp/includes/whomp_database.php Whomp_Database} class file
  */
 require_once($_whomp_base_path . '/includes/whomp_database.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_language.php Whomp_Language} class file
  */
 require_once($_whomp_base_path . '/includes/whomp_language.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_current_user.php Whomp_Current_User} class file
  */
 require_once($_whomp_base_path . '/includes/whomp_current_user.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_xml.php Whomp_Xml} class file
  */
 require_once($_whomp_base_path . '/includes/whomp_xml.php');
 
 // create the options array to pass to the Whomp_Database class
 $whomp_database_options = array('type' => $_whomp_configuration->database_type,
 								 'host' => $_whomp_configuration->database_host,
								 'username' => $_whomp_configuration->database_username,
								 'password' => $_whomp_configuration->database_password,
								 'database' => $_whomp_configuration->database_database,
								 'table_prefix' => $_whomp_configuration->database_table_prefix); 
 
 /**
  * Access to the {@link Whomp_Database Whomp_Database} class
  * 
  * @global $_whomp_database
  */
 $_whomp_database = new Whomp_Database($whomp_database_options);
 
 // create languages array
 $whomp_languages_array = explode(',', str_replace(' ', '', $_whomp_configuration->lamguage_languages));
 // check if default (en) is in the language array
 if (!in_array('en', $whomp_languages_array)) {
	 // if not, add it
	 $whomp_languages_array[] = 'en';
 } // end if
 // create the options array to pass to the Whomp_Language class
 $whomp_language_options = array('directory' => $_whomp_base_path . '/languages',
 								 'class_prefix' => 'Whomp_Language_',
								 'languages' => $whomp_languages_array);
 /**
  * Access to the {@link Whomp_Language Whomp_Language} class
  * 
  * @global $_whomp_language
  */
 $_whomp_langauge = new Whomp_Language($whomp_language_options);
 
 /**
  * Access to the {@link Whomp_Database Whomp_User} class
  * 
  * @global $_whomp_user
  */
 $_whomp_current_user = new Whomp_Current_User();
 
 // ends caching
 $_whomp_cache->end();
?> 