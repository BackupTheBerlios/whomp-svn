<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/previewable.php
 * 
 * Whomp previewable file. Includes the {@link Whomp_Previewable Whomp_Previewable} interface.
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
  * The Whomp previewable interface
  * 
  * Defines previewability for whomp extensions.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  */
 interface Whomp_Previewable {
	 
	 /**
	  * Renders as a preview
	  * 
	  * The xml should have been posted to the script and should be added to 
	  * the user's session variable so it can be saved and previewed. Also 
	  * renders the page with the preview.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function renderPreview(); // end function
	 
 } // end interface
?>