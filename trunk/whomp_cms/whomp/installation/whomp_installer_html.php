<?php
/* $Id$ */
/**
 * /whomp/installation/whomp_installer_html.php
 * 
 * Whomp installer html file. Includes the 
 * {@link Whomp_Installer_Html Whomp_Installer_Html} class.
 * 
 * @package Whomp
 * @copyright � 2005 Schmalls / Joshua Thompson / All Rights Reserved
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
 * @version 0.0.0
 * @since 0.0.0
 * @link http://www.schmalls.com
 */

 /**
  * Make sure this file is being included by a parent file
  */
 defined('_VALID_WHOMP') or exit('Direct access to this location is not allowed!');
 
 /**
  * The Whomp installer html class
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo finish this
  */
 class Whomp_Installer_Html {
	 
	 /**
	  * Outputs the current page
	  * 
	  * If no page information is given then the start page is used.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global class the whomp storage url
	  */
	 public function loadPage() {
		 global $_whomp_storage_url;
		 
		 // format the head information
		 $head = whomp_get_head_data_string();
		 // check for page information
		 $page = isset($_GET['page']) ? self::getPage($_GET['page']) : self::getPage('page1');
		 // check for page navigation information
		 $nav = isset($_GET['page']) ? self::getNav($_GET['page']) : self::getNav('page1');
		 // output the page
		 echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="style.css" type="text/css" />
{$head}
	<script language="javascript" type="text/javascript">
	/* <![CDATA[ */
	var global_pagename;
	function getPageTwo() {
		var keys = 'agree';
		var values = 0 + document.getElementById('agree').checked;
		global_pagename = 'page2';
		Whomp_Installer_Html_getPageText(global_pagename, keys, values);
	}
	function getPageThree() {
		var keys = 'site_name,site_url,site_path,database_type,database_host,database_name,database_user,database_password,database_prefix';
		var values = document.getElementById('site_name').value + ',' 
						+ document.getElementById('site_url').value + ','
						+ document.getElementById('site_path').value + ','
						+ document.getElementById('database_type').value + ','
						+ document.getElementById('database_host').value + ','
						+ document.getElementById('database_name').value + ','
						+ document.getElementById('database_user').value + ','
						+ document.getElementById('database_password').value + ','
						+ document.getElementById('database_prefix').value;
		global_pagename = 'page3';
		Whomp_Installer_Html_getPageText(global_pagename, keys, values);
	}
	function getPageFour() {
		var keys = 'database_type,database_host,database_name,database_user,database_password,database_prefix,admin_user,admin_name,admin_email,admin_password,admin_password_confirm';
		var values = document.getElementById('database_type').value + ','
						+ document.getElementById('database_host').value + ','
						+ document.getElementById('database_name').value + ','
						+ document.getElementById('database_user').value + ','
						+ document.getElementById('database_password').value + ','
						+ document.getElementById('database_prefix').value + ','
						+ document.getElementById('admin_user').value + ',' 
						+ document.getElementById('admin_name').value + ','
						+ document.getElementById('admin_email').value + ','
						+ document.getElementById('admin_password').value + ','
						+ document.getElementById('admin_password_confirm').value;
		global_pagename = 'page4';
		Whomp_Installer_Html_getPageText(global_pagename, keys, values);
	}
	function Whomp_Installer_Html_getPageText_callback(result) {
		document.getElementById('main_area').innerHTML = result;
		Whomp_Installer_Html_getNavText(global_pagename);
	}
	function Whomp_Installer_Html_getNavText_callback(result) {
		document.getElementById('navcontainer').innerHTML = result;
	}
	/* ]]> */
	</script>
	</head>
	<body>
		<div class="header" style="height:76px;">Whomp CMS Installer</div>
		<div style="float:left;" id="main_area">
{$page}
		</div>
		<div id="navcontainer">     
{$nav}
		</div>
	</body>
</html>
HTML;
	 } // end function
	 
	 /**
	  * Gets the current page information and outputs it as XML
	  * 
	  * This function is static and should be used with the Whomp_Ajax class
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param string $pagename the page to retrieve
	  * @todo see why this isn't working
	  */
	 public static function getPageXml($pagename, $post_keys = null, $post_values = null) {
		 
		 // output the xml
		 echo '<?xml version="1.0" standalone="yes" ?><installer_page><page>' . self::getPage($pagename, $post_keys, $post_values) . '</page><nav>' . self::getNav($pagename) . '</nav></installer_page>';
	 } // end function
	 
	 /**
	  * Gets the current page information and outputs it as text
	  * 
	  * This function is static and should be used with the Whomp_Ajax class
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param string $pagename the page to retrieve
	  */
	 public static function getPageText($pagename, $post_keys = null, $post_values = null) {
		 
		 // output the text
		 echo self::getPage($pagename, $post_keys, $post_values);
	 } // end function
	 
	 /**
	  * Private static function to get the requested page data as (x)html
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param string $pagename the page to retrieve
	  * @param string $post_keys keys that have been posted to this page
	  * @param string $post_values values that have been posted to this page
	  * @return string the (x)html page data
	  */
	 public static function getPage($pagename, $post_keys = null, $post_values = null) {
		 global $_whomp_base_path, $_whomp_base_url, $_whomp_storage_path, $_whomp_storage_url, $_whomp_configuration;
		 
		 // check if keys and values were sent
		 if (($post_keys !== null) && ($post_values !== null)) {
			 // if so, create the post values array
			 $post_keys = explode(',', $post_keys);
			 $post_values = explode(',', $post_values);
			 $post_array = array_combine($post_keys, $post_values);
		 } else {
			 // if not, make the post global the post array
			 $post_array = $_POST;
		 } // end if		 
		 // check which page to get
		 switch ($pagename) {
			 case ('page1') :
			 	 // get required settings
			 	 $required = array();
				 $required['php5'] = (phpversion() >= '5.0' ? '<span class="good_setting">Available</span>' : '<span class="bad_setting">Unavailable</span>');
				 $required['dom'] = (extension_loaded('dom') ? '<span class="good_setting">Available</span>' : '<span class="bad_setting">Unavailable</span>');
				 $required['xsl'] = extension_loaded('xsl') ? '<span class="good_setting">Available</span>' : '<span class="bad_setting">Unavailable</span>';
				 // get recommended settings
				 $recommended = array();
				 $recommended['register_globals'] = (ini_get('register_globals')) ? '<span class="bad_setting">On</span>' : '<span class="good_setting">Off</span>';
				 $recommended['output_buffering'] = (ini_get('output_buffering')) ? '<span class="bad_setting">On</span>' : '<span class="good_setting">Off</span>';
				 $recommended['session.auto_start'] = (ini_get('session.auto_start')) ? '<span class="bad_setting">On</span>' : '<span class="good_setting">Off</span>';
				 $recommended['magic_quotes_runtime'] = (ini_get('magic_quotes_runtime')) ? '<span class="bad_setting">On</span>' : '<span class="good_setting">Off</span>';
				 $recommended['file_uploads'] = (ini_get('file_uploads')) ? '<span class="good_setting">On</span>' : '<span class="bad_setting">Off</span>';
				 // get directory information
			 	 $html = <<<HTML
<h3 class="title">Install Step 1: Server Configuration and License</h3>
<h4 class="title">Required Settings</h4>
<p>These options are required for Whomp to function properly. If the option is green everything is ok, otherwise you will need to change your server configuration before continuing.</p>
<ul class="settings">
	<li class="light">
		{$required['php5']}
		PHP5
	</li>
	<li class="dark">
		{$required['dom']}
		DOM Support
	</li>
	<li class="light">
		{$required['xsl']}
		XSL Support
	</li>
</ul>
<h4 class="title">Recommended Settings</h4>
<p>These options are recommended for Whomp compatibility. However, Whomp should function without them.</p>
<ul class="settings">
	<li class="dark">
		{$recommended['register_globals']}
		Register Globals
	</li>
	<li class="light">
		{$recommended['output_buffering']}
		Output Buffering
	</li>
	<li class="dark">
		{$recommended['session.auto_start']}
		Session Auto Start
	</li>
	<li class="light">
		{$recommended['magic_quotes_runtime']}
		Magic Quotes Runtime
	</li>
	<li class="dark">
		{$recommended['file_uploads']}
		File Uploads
	</li>
</ul>
<h4 class="title">Filesystem Permissions</h4>
<p>Whomp needs access and/or write permissions to several directories. If any of the following are red please change the permissions so that Whomp can access them before continuing.</p>
<ul class="settings">
	<li class="light">
		/whomp/includes
	</li>
</ul>
<h4 class="title">License</h4>
<p>Please read the license.</p>
<div class="license">
<p>Copyright &copy; 2005 Schmalls / Joshua Thompson<br />
All rights reserved.<br />
<br />
Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
</p>
<ul>
	<li>
		Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	</li>
	<li>
    	Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    </li>
    <li>
		Neither the name of Schmalls / Joshua Thompson nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
	</li>
</ul>
<p>
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
</p>
</div>
<form action="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&base_url={$_whomp_url_path}&storage_path={$_whomp_storage_path}&storage_url={$_whomp_storage_url}&page=page2" method="get">
	<input type="checkbox" name="agree" value="1" id="agree" /> Agree
<br />
<br />
<ul class="bottomnav">
	<li>
		<a href="javascript:getPageTwo();" class="right">Next Step -&gt;</a>
		<noscript><input type="submit" name="Next Step" /></noscript>
	</li>
</ul>
</form>
HTML;
			 	 break;
			 case ('page2') :
			 	 // check if agree was checked
			 	 if ($post_array['agree'] == 1) {
			 	 	 $html = <<<HTML
<h3 class="title">Install Step 2: Site and Database Information</h3>
<form action="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&base_url={$_whomp_url_path}&storage_path={$_whomp_storage_path}&storage_url={$_whomp_storage_url}&page=page3" method="post">
<h4 class="title">Site Details</h4>
<p>Please enter the following details about your site.</p>
<ul class="settings">
	<li class="light">
		<span class="right"><input id="site_name" type="text" size="40" name="site_name" value="" /></span>
		Site Name
	</li>
	<li class="dark">
		<span class="right"><input id="site_url" type="text" size="40" name="site_url" value="{$_whomp_base_url}" /></span>
		Site Url
	</li>
	<li class="light">
		<span class="right"><input id="site_path" type="text" size="40" name="site_path" value="{$_whomp_base_path}" /></span>
		Site Path
	</li>
</ul>
<h4 class="title">Database Information</h4>
<p>You must have access to a database to install Whomp. The following settings are necessary for successful installation.</p>
<ul class="settings">
	<li class="light">
		<span class="right">
			<select id="database_type" name="database_type" size="1">
				<option value="mysql">mysql</option>
			</select>
		</span>
		Database type
	</li>
	<li class="dark">
		<span class="right"><input id="database_host" type="text" size="40" name="database_host" value="localhost" /></span>
		Database host
	</li>
	<li class="light">
		<span class="right"><input id="database_name" type="text" size="40" name="database_name" value="" /></span>
		Database name
	</li>
	<li class="dark">
		<span class="right"><input id="database_user" type="text" size="40" name="database_user" value="" /></span>
		Database user
	</li>
	<li class="light">
		<span class="right"><input id="database_password" type="password" size="40" name="database_password" /></span>
		Database password
	</li>
	<li class="dark">
		<span class="right"><input id="database_prefix" type="text" size="40" name="database_prefix" value="whomp_" /></span>
		Database prefix
	</li>
</ul>
<br />
<br />
<ul class="bottomnav">
	<li>
		<a href="javascript:getPageThree();" class="right">Next Step -&gt;</a>
		<input type="submit" name="Next Step" />
	</li>
</ul>
</form>
HTML;
			 	 } else {
					 // if not, load first page
					 return self::getPage('page1');
			 	 } // end if
				 break;
			 case ('page3') :
			 	 // add the database tables
			 	 $check = self::addDatabaseTables($post_array);
				 // change the database options in the configuration file
				 $_whomp_configuration->startEdit($_whomp_base_path . '/whomp_configuration.php');
				 $_whomp_configuration->set('site_name', "'" . $post_array['site_name'] . "'");
				 $_whomp_configuration->set('site_url', "'" . $post_array['site_url'] . "'");
				 $_whomp_configuration->set('site_path', "'" . $post_array['site_path'] . "'");
				 $_whomp_configuration->set('database_type', "'" . $post_array['database_type'] . "'");
				 $_whomp_configuration->set('database_host', "'" . $post_array['database_host'] . "'");
				 $_whomp_configuration->set('database_username', "'" . $post_array['database_user'] . "'");
				 $_whomp_configuration->set('database_password', "'" . $post_array['database_password'] . "'");
				 $_whomp_configuration->set('database_database', "'" . $post_array['database_name'] . "'");
				 $_whomp_configuration->set('database_table_prefix', "'" . $post_array['database_prefix'] . "'");
				 $_whomp_configuration->endEdit($_whomp_base_path . '/whomp_configuration.php');
				 // check if the tables were added successfully
				 if ($check !== false) {
					 // if so, display page 3
					 $html = <<<HTML
<form action="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&base_url={$_whomp_url_path}&storage_path={$_whomp_storage_path}&storage_url={$_whomp_storage_url}&page=page4" method="post">
<h3 class="title">Install Step 3: Administrator Information</h3>
<ul class="settings">
	<li class="light">
		<span class="right"><input id="admin_user" type="text" size="40" name="admin_user" value="admin" /></span>
		Admin username
	</li>
	<li class="dark">
		<span class="right"><input id="admin_name" type="text" size="40" name="admin_name" value="Administrator" /></span>
		Admin name
	</li>
	<li class="light">
		<span class="right"><input id="admin_email" type="text" size="40" name="admin_email" value="" /></span>
		Admin email address
	</li>
	<li class="dark">
		<span class="right"><input id="admin_password" type="password" size="40" name="admin_password" value="" /></span>
		Admin password
	</li>
	<li class="light">
		<span class="right"><input id="admin_password_confirm" type="password" size="40" name="admin_password_confirm" value="" /></span>
		Confirm admin password
	</li>
</ul>
<br />
<br />
<ul class="bottomnav">
	<li>
		<a href="javascript:getPageFour();" class="right">Next Step -&gt;</a>
		<noscript><input type="submit" name="Next Step" /></noscript>
	</li>
</ul>
<input type="hidden" id="database_type" name="database_type" value="{$post_array['database_type']}" />
<input type="hidden" id="database_host" name="database_host" value="{$post_array['database_host']}" />
<input type="hidden" id="database_name" name="database_name" value="{$post_array['database_name']}" />
<input type="hidden" id="database_user" name="database_user" value="{$post_array['database_user']}" />
<input type="hidden" id="database_password" name="database_password" value="{$post_array['database_password']}" />
<input type="hidden" id="database_prefix" name="database_prefix" value="{$post_array['database_prefix']}" />
</form>
HTML;
				 } else {
					 // if not, display page 2
					 return self::getPage('page2');
				 } // end if
				 break;
			 case ('page4');
			 	 // add the admin to the users table
			 	 $check = self::addAdmin($post_array);
			 	 $html = <<<HTML
<h3 class="title">Install Step 4: Final Words</h3>
HTML;
				 break;
		 } // end switch
		 // add copyright
		 $html .= <<<HTML
<br />
<br />
<span id="copyright">
	&copy; 2005 Schmalls / Joshua Thompson &lt;<a href="mailto:schmalls@gmail.com">schmalls@gmail.com</a>&gt;. All Rights Reserved.
</span>
HTML;
		 // return the html
		 return $html;
	 } // end function
	 
	 /**
	  * Gets the current page navigation information and ouputs it as text
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param string $pagename the page to retrieve navigation for
	  */
	 public static function getNavText($pagename) {
		 
		 // output the text
		 echo self::getNav($pagename);
	 } // end function
	 
	 /**
	  * Private static function to get the page navigation information as 
	  * (x)html
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param string $pagename the page to retrieve navigation for
	  * @return string the (x)html page navigation data
	  */
	 public static function getNav($pagename) {
		 
		 // check which page to get
		 switch ($pagename) {
			 case ('page1') :
			 	 $html = <<<HTML
<b>navigation</b>
<br/><br/>
<ul id="navlinks">
	<li><strong>Step 1</strong></li>
	<li>Step 2</li>
	<li>Step 3</li>
	<li>Step 4</li>
</ul>
HTML;
				 break;
			 case ('page2') :
			 	 $html = <<<HTML
<b>navigation</b>
<br/><br/>
<ul id="navlinks">
	<li><span class="done">Step 1</span></li>
	<li><strong>Step 2</strong></li>
	<li>Step 3</li>
	<li>Step 4</li>
</ul>
HTML;
				 break;
			 case ('page3') :
			 	 $html = <<<HTML
<b>navigation</b>
<br/><br/>
<ul id="navlinks">
	<li><span class="done">Step 1</span></li>
	<li><span class="done">Step 2</span></li>	
	<li><strong>Step 3</strong></li>
	<li>Step 4</li>
</ul>
HTML;
				 break;
			 case ('page4') :
			 	 $html = <<<HTML
<b>navigation</b>
<br/><br/>
<ul id="navlinks">
	<li><span class="done">Step 1</span></li>
	<li><span class="done">Step 2</span></li>	
	<li><span class="done">Step 3</span></li>
	<li><strong>Step 4</strong></li>
</ul>
HTML;
		 } // end switch
		 // return the html
		 return $html;
	 } // end function
	 
	 /**
	  * Function to add the required database tables
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param array $post_array posted variables
	  * @return boolean whether it was successful or not
	  */
	 public static function addDatabaseTables($post_array) {
		 global $_whomp_storage_path;
		 
		 /**
		  * Require the Whomp_Database class file
		  */
		 require_once($_whomp_storage_path . '/includes/whomp_database.php');
		 // create the database options array
		 $database_options = array('type' => $post_array['database_type'],
 								   'host' => $post_array['database_host'],
								   'username' => $post_array['database_user'],
								   'password' => $post_array['database_password'],
								   'database' => $post_array['database_name'],
								   'table_prefix' => $post_array['database_prefix']);
		 // try to connect to the database
		 try {
			 $database = new Whomp_Database($database_options);
			 // create the table options
			 $tables = array('#__users' => array('fields' => '`id` I KEY AUTO,
			 												  `username` C(50) NOTNULL DEFAULT \'\',
			 												  `password` C(32) NOTNULL DEFAULT \'\',
			 												  `name` C(100) NOTNULL DEFAULT \'\',
			 												  `usertype` C(50) NOTNULL DEFAULT \'\',
			 												  `email` C(255) NOTNULL DEFAULT \'\',
			 												  `last_visit_date` T NOTNULL DEFAULT \'0000-00-00 00:00:00\',
			 												  `register_date` T NOTNULL DEFAULT \'0000-00-00 00:00:00\'',
												 'table_options' => array('mysql' => 'TYPE=MyISAM', 'REPLACE'),
												 'index' => 'username',
												 'index_fields' => '`username`',
												 'index_options' => array('UNIQUE')),
							 '#__en_nodes' => array('fields' => '`id` I KEY AUTO,
							  									 `name` C(255) NOTNULL DEFAULT \'\',
							  									 `type` C(255) NOTNULL DEFAULT \'\',
							  									 `modified` T NOTNULL DEFAULT \'0000-00-00 00:00:00\',
							  									 `modified_by` I NOTNULL DEFAULT \'0\',
							  									 `layouts` X NOTNULL DEFAULT \'\',
							  									 `parents` X NOTNULL DEFAULT \'\',
							  									 `children` X NOTNULL DEFAULT \'\',
							  									 `relatives` X NOTNULL DEFAULT \'\',
							  									 `_group` X NOTNULL DEFAULT \'\',
							  									 `_user` X NOTNULL DEFAULT \'\',
							  									 `_headers` X NOTNULL DEFAULT \'\',
							  									 `_show_logged` L NOTNULL DEFAULT \'1\'',
													 'table_options' => array('mysql' => 'TYPE=MyISAM', 'REPLACE'),
													 'index' => 'name',
													 'index_fields' => '`name`',
													 'index_options' => array('UNIQUE')),
							 '#__node_types' => array ('fields' => '`id` I KEY AUTO,
							 										`type` C(255) NOTNULL DEFAULT \'\'',
													   'table_options' => array('mysql' => 'TYPE=MyISAM', 'REPLACE'),
													   'index' => 'type',
													   'index_fields' => '`type`',
													   'index_options' => array('UNIQUE')));
			 // create the tables
			 $database->createTables($tables);
		 } catch (Exception $e) {
			 whomp_output_exception($e);
		 } // end try
		 // return true
		 return true;
	 } // end function
	 
	 /**
	  * Add the admin to the users table
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @static
	  * @param array $post_array posted variables
	  */
	 public static function addAdmin($post_array) {
		 global $_whomp_storage_path;
		 
		 /**
		  * Require the Whomp_Database class file
		  */
		 require_once($_whomp_storage_path . '/includes/whomp_database.php');
		 // create the database options array
		 $database_options = array('type' => $post_array['database_type'],
 								   'host' => $post_array['database_host'],
								   'username' => $post_array['database_user'],
								   'password' => $post_array['database_password'],
								   'database' => $post_array['database_name'],
								   'table_prefix' => $post_array['database_prefix']);
		 // try to connect to the database
		 try {
			 $database = new Whomp_Database($database_options);
			 $record = array('username' => $post_array['admin_user'],
			 				 'name' => $post_array['admin_name'],
							 'email' => $post_array['admin_email'],
							 'password' => md5($post_array['admin_password']),
							 'usertype' => 'admin',
							 'last_visit_date' => date('Y-m-d H:i:s'),
							 'register_date' => date('Y-m-d H:i:s'));
			 $database->insert('#__users', $record);
		 } catch (Exception $e) {
			 whomp_output_exception($e);
		 } // end try
		 return true;
	 } // end function
	 
 } // end class
?>