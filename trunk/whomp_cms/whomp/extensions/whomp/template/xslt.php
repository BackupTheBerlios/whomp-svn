<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/template/xslt.php
 * 
 * Whomp template engine file. Includes the 
 * {@link Whomp_Template_Xslt Whomp_Template_Xslt} class.
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
  * The Whomp template xslt class
  * 
  * Template engine that expands the abstact Whomp_Template class with php xslt.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Template_Xslt implements Whomp_Template, Whomp_Template_Editable {
	 
	 /**
	  * The template XML DOMDocument
	  * 
	  * @var DOMDocument $_template_xml
	  * @access protected
	  */
	 protected $_template_xml;
	 
	 /**
	  * The template XSL DOMDocument
	  * 
	  * @var DOMDocument $_template_xsl
	  * @access protected
	  */
	 protected $_template_xsl;
	 
	 /**
	  * The transformed document
	  * 
	  * @var string $_template_transformed
	  * @access protected
	  */
	 protected $_template_transformed;
	 
	 /**
	  * The acceptable output formats
	  * 
	  * This should be an array with formats as keys and content-types as 
	  * values. For example:
	  * <pre>
	  * Array (
	  * 	'html' => 'text/html'
	  * 	'xhtml+xml' => 'application/xhtml+xml'
	  * 	'xhtml' => 'application/xhtml+xml'
	  * )
	  * </pre>
	  * 
	  * @var array $_formats
	  * @access protected
	  */
	 protected $_formats;
	 
	 /**
	  * The document's content type
	  * 
	  * @var string $_content_type
	  * @access protected
	  */
	 protected $_content_type;
	 
	 /**
	  * The document's charset
	  * 
	  * @var string $_charset
	  * @access protected
	  */
	 protected $_charset = 'utf-8';
	 
	 /* ++ Whomp_Template methods ++ */
	 
	 /**
	  * Loads the template
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options the node information
	  */
	 public function loadTemplate($options) {
		 
		 $this->_template_xml = new DomDocument('1.0', $this->_charset);
		 $this->_template_xsl = new DomDocument('1.0', $this->_charset);
	 } // end function
	 
	 /**
	  * Inserts the node XML into the correct location(s)
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if the node is not found in the xml
	  * @param DOMDocument $node_xml the node XML to be inserted
	  * @param string $layout the layout to use
	  * @param string $node_name the name of the node that needs to be inserted
	  * @global string the whomp storage path
	  */
	 public function insertNodeXml(DOMDocument $node_xml, $layout, $node_name = '') {
		 global $_whomp_storage_path;
		 
		 // initialize the template xml
		 $this->_template_xml->load($_whomp_storage_path . '/layouts/' . $layout . '.xml');
		 // find the specified node
		 $importNode = $this->_template_xml->importNode($node_xml->documentElement, true);
		 $this->_template_xml->saveXML();
		 $xpath = new DOMXpath($this->_template_xml);
		 $node_list = $xpath->query('//node');
		 // check if the node was found
		 if (count($node_list) != 0) {
			 // if so, append the node XML
			 foreach ($node_list as $node) {
				 $node->appendChild($importNode);
			 } // end foreach
		 } else {
			 // if not, throw exception
			 throw new Exception('The node was not found in the XML document.');
		 } // end if
	 } // end function
	 
	 /**
	  * Inserts XSL path as an import into the template XSL file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $node_xsl_path the path to the XSL file
	  * @param string $template the template to use
	  * @param string $format the format to use
	  * @global string the whomp storage url
	  */
	 public function insertNodeXsl($node_xsl_path, $template, $format) {
		 global $_whomp_storage_url;
		 
		 $xsl = <<<XSL
<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="{$node_xsl_path}" />
	<xsl:import href="{$_whomp_storage_url}/templates/{$template}/{$format}.xsl" />
	<xsl:variable name="_whomp_storage_url">{$_whomp_storage_url}</xsl:variable>
</xsl:stylesheet>
XSL;
		 $this->_template_xsl->loadXML($xsl);
	 } // end function
	 
	 /**
	  * Transforms the XML document with XSL
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if there is an error transforming the document
	  */
	 public function transform() {
		 
		 // create the XSLT processor and import the stylesheet
		 $processor = new XSLTProcessor();
		 $processor->importStyleSheet($this->_template_xsl);
		 // transform the XML file
		 $this->_template_transformed = $processor->transformToXML($this->_template_xml);
		 // check if the transformation went alright
		 if ($this->_template_transformed === false) {
			 // if not, throw exception
			 throw new Exception('Error transforming XML document with XSL.');
		 } // end if
	 } // end function
	 
	 /**
	  * Outputs the transformed XML file to the screen
	  * 
	  * It also sets the correct header information.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function render() {
		 
		 // set the content type and charset
		 header('Content-Type: ' . $this->_content_type . '; charset=' . $this->_charset);
		 // display the document
		 echo $this->_template_transformed;
		 // return unique identifier
		 return array('content_type' => $this->_content_type,
		 			  'charset' => $this->_charset);
	 } // end function
	 
	 /* -- Whomp_Template methods -- */
	 
	 /* ++ Whomp_Template_Editable methods ++ */
	 
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
	 public function insertEditableNodeXml(DOMDocument $node_xml, $layout, $node_name = '') {
		 
		 $this->insertXml($node_xml, $layout, $node_name);
	 } // end function
	 
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
	 public function insertEditableNodeXsl($xsl_path, $template, $format) {
		 global $_whomp_storage_url;
		 
		 $xsl = <<<XSL
<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="{$node_xsl_path}" />
	<xsl:import href="{$_whomp_storage_url}/templates/{$template}/{$format}.xsl" />
	<xsl:variable name="_whomp_storage_url">{$_whomp_storage_url}</xsl:variable>
	<xsl:variable name="edit" select="boolean(1)" />
	<xsl:variable name="editid">bxe_area</xsl:variable>
</xsl:stylesheet>
XSL;
		 $this->_template_xsl->loadXML($xsl);
	 } // end function
	 
	 /**
	  * Transforms the XML document with XSL in an editable form
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function transformEditable() {
		 
		 $this->transform();
	 } // end function
	 
	 /* -- Whomp_Template_Editable methods -- */
	 
 } // end class
?>