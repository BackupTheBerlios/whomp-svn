<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/template/editable.php
 * 
 * Whomp template editable file. Includes the {@link Whomp_Template_Editable Whomp_Template_Editable} interface.
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
  * The Whomp template editable interface
  * 
  * Defines editability for whomp templates.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Template_Editable extends Whomp_Template {
	 
	 /**
	  * Inserts the node xml in an editable form
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param DOMDocument $node_xml the node XML to be inserted
	  * @param string $layout the layout to use
	  * @param string $node_name the name of the node that needs to be inserted
	  */
	 public function insertEditableNodeXml(DOMDocument $node_xml, $layout, $node_name = ''); // end function
	 
	 /**
	  * Inserts XSL import into the xsl file in an editable form
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xsl_path the path to the XSL file
	  * @param string $template the template to use
	  * @param string $format the format to use
	  */
	 public function insertEditableNodeXsl($xsl_path, $template, $format); // end function
	 
	 /**
	  * Transforms the XML document with XSL in an editable form
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function transformEditable(); // end function
	 	 
 } // end interface
?>