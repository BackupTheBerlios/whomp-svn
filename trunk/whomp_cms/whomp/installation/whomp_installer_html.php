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
	<link rel="stylesheet" href="style.css" type="text/css" />
{$head}
	<script language="javascript" type="text/javascript">
	var global_pagename;
	function getPage(pagename) {
		global_pagename = pagename;
		Whomp_Installer_Html_getPageText(global_pagename);
	}
	function Whomp_Installer_Html_getPageText_callback(result) {
		document.getElementById('main_area').innerHTML = result;
		Whomp_Installer_Html_getNavText(global_pagename);
	}
	function Whomp_Installer_Html_getNavText_callback(result) {
		document.getElementById('navcontainer').innerHTML = result;
	}
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
	 public static function getPageXml($pagename) {
		 
		 // output the xml
		 echo '<?xml version="1.0" standalone="yes" ?><installer_page><page>' . self::getPage($pagename) . '</page><nav>' . self::getNav($pagename) . '</nav></installer_page>';
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
	 public static function getPageText($pagename) {
		 
		 // output the text
		 echo self::getPage($pagename);
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
	  * @return string the (x)html page data
	  */
	 public static function getPage($pagename) {
		 global $_whomp_base_path, $_whomp_base_url, $_whomp_storage_path, $_whomp_storage_url;
		 
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
<h3 class="title">Install Step 1: Server Configuration</h3>
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
<br />
<br />
<ul>
	<li>
		<a onclick="getPage('page2'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page2" class="right">Next Step -&gt;</a>
	</li>
</ul>
HTML;
			 	 break;
			 case ('page2') :
			 	 $html = <<<HTML
<h3 class="title">Install Step 2: Site Properties</h3>
<ul>
	<li>
		<a onclick="getPage('page3'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page3" class="right">Next Step -&gt;</a>
		<a onclick="getPage('page1'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page1">&lt;- Previous Step</a>
	</li>
</ul>
HTML;
				 break;
			 case ('page3') :
			 	 //$html = '<p><a onclick="Whomp_Installer_Html_getPageText(\'check\'); return false;" href="' . $_whomp_storage_url . '/installation/index.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_base_url . '&storage_path=' . $_whomp_storage_path . '&storage_url=' . $_whomp_storage_url . '">&lt;- back</a></p>';
			 	 $html = <<<HTML
<h3 class="title">Install Step 3: Database Information</h3>
<ul>
	<li>
		<!--<a onclick="getPage('page4'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page4" class="right">Next Step -&gt;</a>-->
		<a onclick="getPage('page2'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page2">&lt;- Previous Step</a>
	</li>
</ul>
HTML;
				 break;
		 } // end switch
		 // add copyright
		 $html .= <<<HTML
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
	<li><a onclick="getPage('page2'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page2">Step 2</a></li>
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
	<li><a onclick="getPage('page1'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page1">Step 1</a></li>
	<li><strong>Step 2</strong></li>
	<li><a onclick="getPage('page3'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page3">Step 3</a></li>
	<li>Step 4</li>
</ul>
HTML;
				 break;
			 case ('page3') :
			 	 $html = <<<HTML
<b>navigation</b>
<br/><br/>
<ul id="navlinks">
	<li>Step 1</li>
	<li><a onclick="getPage('page2'); return false;" href="{$_whomp_storage_url}/installation/index.php?base_path={$_whomp_base_path}&amp;base_url={$_whomp_base_url}&amp;storage_path={$_whomp_storage_path}&amp;storage_url={$_whomp_storage_url}&amp;page=page2">Step 2</a></li>	
	<li><strong>Step 3</strong></li>
	<li>Step 4</li>
</ul>
HTML;
				 break;
		 } // end switch
		 // return the html
		 return $html;
	 } // end function
	 
 } // end class
?>