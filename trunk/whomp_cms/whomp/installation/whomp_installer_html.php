<?php
/* $Id$ */
/**
 * /whomp/installation/whomp_installer_html.php
 * 
 * Whomp installer html file. Includes the 
 * {@link Whomp_Installer_Html Whomp_Installer_Html} class.
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
	  */
	 public function loadPage() {
		 
		 // format the head information
		 $head = whomp_get_head_data_string();
		 // check for page information
		 $page = isset($_GET['page']) ? self::getPage($_GET['page']) : self::getPage('check');
		 // output the page
		 echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
{$head}
	<script language="javascript" type="text/javascript">
	function Whomp_Installer_Html_getPageText_callback(result) {
		document.getElementById('main_area').innerHTML = result;
	}
	</script>
	</head>
	<body>
		<div id="main_area">
			{$page}
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
	  */
	 public static function getPageXml($pagename) {
		 
		 // output the xml
		 echo '<?xml version="1.0" standalone="yes" ?><installer_page>' . self::getPage($pagename) . '</installer_page>';
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
	  * @param string $pagename the page to retrieve
	  */
	 public static function getPage($pagename) {
		 global $_whomp_base_path, $_whomp_base_url, $_whomp_storage_path, $_whomp_storage_url;
		 
		 // check which page to get
		 switch ($pagename) {
			 case ('check') :
			 	 $html = '<p><a onclick="Whomp_Installer_Html_getPageText(\'page2\'); return false;" href="' . $_whomp_storage_url . '/installation/index.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_base_url . '&storage_path=' . $_whomp_storage_path . '&storage_url=' . $_whomp_storage_url . '&page=page2">something -&gt;</a></p>';
			 	 break;
			 default :
			 	 $html = '<p><a onclick="Whomp_Installer_Html_getPageText(\'check\'); return false;" href="' . $_whomp_storage_url . '/installation/index.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_base_url . '&storage_path=' . $_whomp_storage_path . '&storage_url=' . $_whomp_storage_url . '">&lt;- back</a></p>';
		 } // end switch
		 // return the html
		 return $html;
	 } // end function
 } // end class
?>