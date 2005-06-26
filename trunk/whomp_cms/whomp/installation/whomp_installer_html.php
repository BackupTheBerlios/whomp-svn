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
 public class Whomp_Installer_Html {
	 
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
	 public function startPage() {
		 
		 // format the head information
		 $head = whomp_get_head_data_string();
		 // check for page information
		 $page = isset($_POST['page']) ? self::getPageHtml($_POST['page']) : self::getPageHtml('check');
		 // output the page
		 echo <<<HTML
<html>
	<head>
{$head}
	</head>
	<body>
{$page}
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
		 echo '<installer_page>' . self::getPageHtml($pagename) . '</installer_page>';
	 } // end function
	 
	 /**
	  * Private static function to get the requested page data as (x)html
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access private
	  * @param string $pagename the page to retrieve
	  */
	 private statuc function getPageHtml($pagename) {
		 
		 // check which page to get
		 switch ($pagename) {
			 case ('check') :
			 	 ob_start()
			 	 phpinfo();
				 $html = ob_get_clean();
			 	 break;
			 default :
			 	 $html = null;
		 } // end switch
		 // return the html
		 return $html;
	 } // end function
 } // end class
?>