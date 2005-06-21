<?php
/**
 * /whomp/templates/whomp_template_engine.php
 * 
 * <p>Whomp template engine file. Includes the 
 * {@link Whomp_Template_Engine Whomp_Template_Engine} class.</p>
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
  * The Whomp template engine class
  * 
  * <p>Template engine that expands the abstact Whomp_Template class.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 public class Whomp_Template_Engine extends Whomp_Template {
	 
	 /**
	  * Whomp_Template_Engine constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param string $layout the layout to use
	  * @param string $format the output format
	  * @param array $node_formats the formats that the node supports
	  * @todo implement other output formats
	  */
	 public function __construct($layout, $format, $node_formats) {
		 global $_whomp_storage_path;
		 
		 // currently only xhtml+xml supported
		 $this->_content_type = 'application/xhtml+xml';
		 // currently only the default layout is supported
		 $this->_template_xml = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xml->load($_whomp_storage_path . '/templates/whomp_template_engine/layouts/default.xml');
		 $this->_template_xsl = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xsl->load($_whomp_storage_path . '/templates/whomp_template_engine/xsl/xhtml.xsl');
	 } // end function
 } // end class
?>