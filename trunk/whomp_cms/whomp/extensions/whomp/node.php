<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/node.php
 * 
 * Whomp node file. Includes the {@link Whomp_Node Whomp_Node} class.
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
  * The Whomp node interface
  * 
  * Implements node objects for Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Node {
	 
	 /**
	  * Loads the node into the object
	  * 
	  * Takes the node array and uses this information for other methods, 
	  * such as the renderPage method. The array should be in the form 
	  * returned from the whomp_get_node_array function. Any information 
	  * retrieval (from the database for example) should be done within this 
	  * function. It should be the first thing called after the node object's 
	  * initialization.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options the node array
	  */
	 public function loadNode($options); // end function
	 
	 /**
	  * Renders the page
	  * 
	  * This method finds the template engine class file and initializes 
	  * the template class. It then uses the template class to insert the 
	  * node's XML and XSL information. After it has been inserted, the 
	  * template is transformed to the desired ouput format with XSL and 
	  * then printed to the screen. To insert the XML the template insertNodeXml
	  * function should be called with the node xml sent as a domdocument as 
	  * the first parameter and an optional node name parameter. To insert the 
	  * node xsl, the template insertNodeXsl method should be called with the 
	  * path to the node's xsl file as the only parameter. Then the template 
	  * class render method should be called. It returns an array of options 
	  * useful for the cache class's end method. These options should generally
	  * be supplemented with node specific information for the cache and 
	  * returned.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function renderPage(); // end function
	 
 } // end class
?>