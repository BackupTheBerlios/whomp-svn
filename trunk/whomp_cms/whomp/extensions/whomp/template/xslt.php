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
 class Whomp_Template_Xslt implements Whomp_Template, Whomp_Editable {
	 
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
	  * The template Schema DOMDocument
	  * 
	  * @var DOMDocument $_template_schema
	  * @access protected
	  */
	 protected $_template_schema;
	 
	 /**
	  * The transformed document
	  * 
	  * @var string $_template_transformed
	  * @access protected
	  */
	 protected $_template_transformed;
	 
	 /**
	  * The document's content type
	  * 
	  * @var string $_content_type
	  * @access protected
	  */
	 protected $_content_type;
	 
	 /**
	  * The document's layout
	  * 
	  * @var string $_layout
	  * @access protected
	  */
	 protected $_layout;
	 
	 /**
	  * The document's format
	  * 
	  * @var string $_format
	  * @access protected
	  */
	 protected $_format;
	 
	 /**
	  * The document's template
	  * 
	  * @var string $_template
	  * @access protected
	  */
	 protected $_template;
	 
	 /**
	  * The document's charset
	  * 
	  * @var string $_charset
	  * @access protected
	  */
	 protected $_charset = 'utf-8';
	 
	 protected $_edit = 0;
	 protected $_editid = '';
	 protected $_onload = '';
	 
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
		 
		 $this->page = $options['page'];
		 $this->_content_type = $options['content_type'];
		 $this->_layout = $options['layout'];
		 $this->_format = $options['format'];
		 $this->_template = $options['template'];
		 $this->_template_xml = new DomDocument('1.0', $this->_charset);
		 $this->_template_xsl = new DomDocument('1.0', $this->_charset);
		 $this->_template_schema = new DomDocument('1.0', $this->_charset);
	 } // end function
	 
	 /**
	  * Inserts the XML into the correct location(s)
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if the node is not found in the xml
	  * @param string $xml_path path to the xml file
	  * @param string $xpath_query query which returns the xml node(s) to insert the xml
	  */
	 public function insertXml($xml_path, $xpath_query = '//node') {
		 global $_whomp_storage_path;
		 
		 // initialize the template xml
		 $this->_template_xml->load($_whomp_storage_path . '/layouts/' . $this->_layout . '.xml');
		 // find the specified node
		 $node_xml = new DOMDocument();
		 $node_xml->load($xml_path);
		 $importNode = $this->_template_xml->importNode($node_xml->documentElement, true);
		 $xpath = new DOMXpath($this->_template_xml);
		 $node_list = $xpath->query($xpath_query);
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
	  * Inserts XSL import into the xsl file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xsl_path the path to the XSL file
	  * @global string the whomp storage url
	  */
	 public function insertXsl($xsl_path) {
		 global $_whomp_storage_url;
		 
		 $whomp_head = whomp_get_head_data_string();
		 $xsl = <<<XSL
<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="{$xsl_path}" />
	<xsl:import href="{$_whomp_storage_url}/templates/{$this->_template}/{$this->_format}.xsl" />
	<xsl:variable name="_whomp_storage_url">{$_whomp_storage_url}</xsl:variable>
	<xsl:variable name="whomp_edit" select="boolean({$this->_edit})" />
	<xsl:variable name="whomp_head">{$whomp_head}</xsl:variable>
	<xsl:variable name="whomp_onload">{$this->_onload}</xsl:variable>
	<xsl:variable name="whomp_editid">{$this->_editid}</xsl:variable>
</xsl:stylesheet>
XSL;
		 $this->_template_xsl->loadXML($xsl);
	 } // end function
	 
	 /**
	  * Inserts schema into the template schema
	  * 
	  * @author Schmalls / Joshua Thompson <schmall@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $schema_path the path to the node schema
	  * @global string the whomp storage url
	  */
	 public function insertSchema($schema_path) {
		 global $_whomp_storage_url;
		 
		 $schema = <<<SCHEMA
<?xml version="1.0" encoding="utf-8" ?>
<grammar ns="" datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes">
	<include href="{$schema_path}"/>
	<include href="{$_whomp_storage_url}/layouts/schema.xml"/>
</grammar>	
SCHEMA;
		 $this->_template_schema->loadXML($schema);
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
	 public function transformTemplate() {
		 
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
	 public function renderTemplate() {
		 
		 // set the content type and charset
		 header('Content-Type: ' . $this->_content_type . '; charset=' . $this->_charset);
		 // display the document
		 echo $this->_template_transformed;
		 // return unique identifier
		 return array('content_type' => $this->_content_type,
		 			  'charset' => $this->_charset);
	 } // end function
	 
	 /* -- Whomp_Template methods -- */
	 
	 /* ++ Whomp_Editable methods ++ */
	 
	 /**
	  * Makes editable
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function makeEditable() {
		 global $_whomp_base_url;
		 
		 $this->_edit = 1;
		 $this->_onload = 'bxe_start(\'' . $_whomp_base_url . '/' . $this->page . '?whomp_operation=config\')';
		 $this->_editid = 'bxe_area';
	 } // end function
	 
	 /**
	  * Prints the xml
	  * 
	  * Outputs only the xml file. The type should be set to 'text/xml' for
	  * compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printXml() {
		 
		 echo $this->_template_xml->saveXML();
	 } // end function
	 
	 /**
	  * Prints the xsl
	  * 
	  * Outputs only the xsl file. The type should be set to 'text/xml' for
	  * compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printXsl() {
		 
		 echo $this->_template_xsl->saveXML();
	 } // end function
	 
	 /**
	  * Prints the schema validation information
	  * 
	  * Outputs only the schema validation file. Should communicate with
	  * the configured editor to determine which schema type to print. The type
	  * should be set to 'text/xml' for compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printSchema() {
		 
		 echo $this->_template_schema->saveXML();
	 } // end function
	 
	 /**
	  * Prints the editor configuration file
	  * 
	  * Outputs the editor configuration file. Should communicate with the 
	  * configured editor to determine the correct format for output.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printConfig() {
	 } // end function
	 
	 /* -- Whomp_Editable Methods -- */
	 
 } // end class
?>