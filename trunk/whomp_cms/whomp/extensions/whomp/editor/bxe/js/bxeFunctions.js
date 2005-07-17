// +--------------------------------------------------------------------------+
// | BXE                                                                      |
// +--------------------------------------------------------------------------+
// | Copyright (c) 2003,2004 Bitflux GmbH                                     |
// +--------------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");          |
// | you may not use this file except in compliance with the License.         |
// | You may obtain a copy of the License at                                  |
// |     http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software      |
// | distributed under the License is distributed on an "AS IS" BASIS,        |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. |
// | See the License for the specific language governing permissions and      |
// | limitations under the License.                                           |
// +--------------------------------------------------------------------------+
// | Author: Christian Stocker <chregu@bitflux.ch>                            |
// +--------------------------------------------------------------------------+
//
// $Id: bxeFunctions.js 1323 2005-06-02 13:12:02Z chregu $

const BXENS = "http://bitfluxeditor.org/namespace";
const XMLNS = "http://www.w3.org/2000/xmlns/";

const E_FATAL = 1;

const BXE_SELECTION = 1;
const BXE_APPEND = 2;
const BXE_SPLIT_IF_INLINE = 1;

var bxe_snapshots = new Array();
var bxe_snapshots_position = 0;
var bxe_snapshots_last = 0;
const BXE_SNAPSHOT_LENGTH = 20;
function __bxeSave(e) {
    if (bxe_bug248172_check()) {
		alert ("THIS DOCUMENT COULD NOT BE SAVED!\n You are using a Mozilla release with a broken XMLSerializer implementation.\n Mozilla 1.7 and Firefox 0.9/0.9.1 are known to have this bug.\n Please up- or downgrade.");
		return false;
	}

	var td = new mozileTransportDriver(bxe_config.xmlfile_method);
	td.Docu = this;
	if (e.additionalInfo ) {
		td.Exit = e.additionalInfo.exit;
	} else {
		td.Exit = null;
	}
	var xml = bxe_getXmlDomDocument(true);
	if (!xml) {
		alert("You're in Source Mode. Not possible to use this button");
	}
	
	var xmlstr =xml.saveXML(xml);
	
	function callback (e) {
		
		
		if (e.isError) {
			var widg = mozilla.getWidgetModalBox("Saving");
			widg.addText("Document couldn't be saved");
			widg.addText(e.statusText,true);
			widg.show((window.innerWidth- 500)/2,50, "fixed");
			return;
		}
		bxe_lastSavedXML = bxe_getXmlDocument();
		bxe_status_bar.showMessage("Document successfully saved");
		if	(e.status == 201 && bxe_config.options['onSaveFileCreated']) {
			eval(bxe_config.options['onSaveFileCreated']);
			
		}

		if (e.td.Exit) {
			eDOMEventCall("Exit",document);
		}
	}
	var url = bxe_config.xmlfile;
	if (td.Exit) {
		url = bxe_addParamToUrl(url,"exit=true");
	} else {
		url = bxe_addParamToUrl(url,"exit=false");
	}
	td.save(url, xmlstr, callback);
}


function bxe_addParamToUrl(url, param) {
	if (url.indexOf("?") == -1) {
		url += "?" + param;
	} else {
		url += "&" + param;
	}
	return url;
}

function bench(func, string,iter) {
	
	
	var start = new Date();
	for (var i = 0; i< iter; i++) {
		func();
	}
	var end = new Date();
	

	debug ("Benchmark " + string);
//	debug ("Start " + start.getTime());
//	debug ("End   " + end.getTime() );
	debug ("Total " +(end-start) + " / " +  iter + " = " + (end-start)/iter); 
}

function bxe_bench() {
	
	bench(function() {xmlstr = bxe_getXmlDocument()}, "getXML", 2);
}

function bxe_history_snapshot_async()  {
	window.setTimeout("bxe_history_snapshot()",1);
}


function bxe_history_snapshot() {
	var xmlstr = bxe_getXmlDocument();
	if (!xmlstr) { return false;}
	bxe_snapshots_position++;
	bxe_snapshots_last = bxe_snapshots_position;
	bxe_snapshots[bxe_snapshots_position] = xmlstr;
	var i = bxe_snapshots_last + 1;
	while (bxe_snapshots[i]) {
		bxe_snapshots[i] = null;
		i++;
	}
	if (bxe_snapshots.length >  BXE_SNAPSHOT_LENGTH ) {
		var _temp = new Array();
		
		for (var i = bxe_snapshots_last; i >= bxe_snapshots_last - BXE_SNAPSHOT_LENGTH; i--) {
			_temp[i] = bxe_snapshots[i];
		}
		bxe_snapshots = _temp;
	}
	return (xmlstr);
}

function bxe_history_redo() {
	if (bxe_snapshots_position >= 0 && bxe_snapshots[( bxe_snapshots_position + 1)]) {
		var currXmlStr = bxe_getXmlDocument();
		if (!currXmlStr) { alert("You're in Source Mode. Not possible to use this button"); return false;} 
		bxe_snapshots_position++;
		var xmlstr = bxe_snapshots[bxe_snapshots_position];
		if (currXmlStr == xmlstr && bxe_snapshots[bxe_snapshots_position + 1]) {
			bxe_snapshots_position++;
			var xmlstr = bxe_snapshots[bxe_snapshots_position];
		}
		var BX_parser = new DOMParser();
		
		var vdom = bxe_config.xmldoc.documentElement.XMLNode.vdom;
		bxe_config.xmldoc= BX_parser.parseFromString(xmlstr,"text/xml");
		bxe_config.xmldoc.init();
		bxe_config.xmldoc.documentElement.XMLNode.vdom = vdom;
		bxe_Transform();
		
}
	
}
function bxe_history_undo() {
	if (bxe_snapshots_position >= 0) {
		if (bxe_snapshots_position == bxe_snapshots_last) {
			var currXmlStr = bxe_history_snapshot();
			bxe_snapshots_position--;
		} else {
			var currXmlStr = bxe_getXmlDocument();
		}
		
		if (!currXmlStr) { alert("You're in Source Mode. Not possible to use this button"); return false;} 
		var xmlstr = bxe_snapshots[bxe_snapshots_position];
		if (xmlstr) {
			bxe_snapshots_position--;
			while(currXmlStr == xmlstr && bxe_snapshots[bxe_snapshots_position ] ) {
				xmlstr = bxe_snapshots[bxe_snapshots_position];
				bxe_snapshots_position--;
			}
		}
		
		if (bxe_snapshots_position < 0) {
			bxe_snapshots_position = 0;
			return false;
		}
		var BX_parser = new DOMParser();
		if (xmlstr) {
			var vdom = bxe_config.xmldoc.documentElement.XMLNode.vdom;
			bxe_config.xmldoc= BX_parser.parseFromString(xmlstr,"text/xml");
			bxe_config.xmldoc.init();
			bxe_config.xmldoc.documentElement.XMLNode.vdom = vdom;
			bxe_Transform();
		}
	} 
	/*bxe_snapshots[bxe_snapshots_position] == xmlstr;
	bxe_snapshots_position++;*/
}

function bxe_getXmlDomDocument(clean) {
	
	if ( clean) {
		
		var doc = bxe_config.xmldoc.cloneNode(true);
		var res = doc.evaluate("//@__bxe_id", doc, null, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE,null);
		var _l = res.snapshotLength;
		for (var i = 0; i < _l; i++) {
			if (res.snapshotItem(i).ownerElement) {
				res.snapshotItem(i).ownerElement.removeAttributeNode(res.snapshotItem(i));
			}
			
		}
		
		res = doc.evaluate("//@__bxe_defaultcontent", doc, null, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE,null);
		_l = res.snapshotLength;
		for (var i = 0; i < _l; i++) {
			var ownE = res.snapshotItem(i).ownerElement;
			if (ownE) {
				if (ownE.firstChild && ownE.firstChild.nodeValue == "#empty") {
					ownE.removeChild(ownE.firstChild);
				}
				res.snapshotItem(i).ownerElement.removeAttributeNode(res.snapshotItem(i));
			}
			
		}
		
		return doc;
	}
	
	return bxe_config.xmldoc;
}
	

function bxe_getXmlDocument(clean) {
	
	var xml = bxe_getXmlDomDocument(clean);
	if (!xml ) { return xml;}
	if (clean) {
		xml2 = xml.saveXML(xml);
		delete xml;
		return xml2;
	} else {
		return xml.saveXML(xml);
	}

}

function bxe_getRelaxNGDocument() {
	
	var areaNodes = bxe_getAllEditableAreas();
	var xml = bxe_config.xmldoc.vdom.xmldoc;
	return xml.saveXML(xml);
}



/* Mode toggles */

function bxe_toggleTagMode(e) {
	try {
	var editableArea = e.target;
	if (editableArea._SourceMode) {
			e = new eDOMEvent();
			e.setTarget(editableArea);
			e.initEvent("toggleSourceMode");
	}
	var xmldoc = document.implementation.createDocument("","",null);
	
	if (!editableArea._TagMode) {
		createTagNameAttributes(editableArea);
		editableArea._TagMode = true;
		editableArea.AreaInfo.TagModeMenu.Checked = true;
		editableArea.AreaInfo.NormalModeMenu.Checked = false;
	} else {
		var walker = document.createTreeWalker(
			editableArea, NodeFilter.SHOW_ELEMENT,
			null, 
			true);
		var node = editableArea;
		
		do {
			if (node.hasChildNodes()) {
				node.removeAttribute("_edom_tagnameopen");
			}
			node.removeAttribute("_edom_tagnameclose");
			node =   walker.nextNode() 
		} while(node)
		editableArea._TagMode = false;
		editableArea.AreaInfo.TagModeMenu.Checked = false;
		editableArea.AreaInfo.NormalModeMenu.Checked = true;
	}
	}
	catch(e) {alert(e);}

}

function bxe_toggleNormalMode (e) {
	try {
	var editableArea = e.target;
	if (editableArea._SourceMode) {
			e = new eDOMEvent();
			e.setTarget(editableArea);
			e.initEvent("toggleSourceMode");
	}
	if (editableArea._TagMode) {
			e = new eDOMEvent();
			e.setTarget(editableArea);
			e.initEvent("toggleTagMode");
	}
	editableArea.AreaInfo.NormalModeMenu.Checked = true;
	}
	catch(e) {alert(e);}

}

function addTagnames_bxe (e) {		
	
	e.currentTarget.removeEventListener("DOMAttrModified",addTagnames_bxe,false);
	
	var nodeTarget = e.target; 
try {
	createTagNameAttributes(nodeTarget.parentNode.parentNode);
} catch (e) {bxe_catch_alert(e);}
	e.currentTarget.addEventListener("DOMAttrModified",addTagnames_bxe,false);
	
}

function createTagNameAttributes(startNode, startHere) {
	var walker = startNode.XMLNode.createTreeWalker();
	if (!startHere) {
		var node = walker.nextNode();
	} else {
		var node = walker.currentNode;
	}
	
	while( node) {
		if (node.nodeType == 1) {
			var xmlstring = node.getBeforeAndAfterString(false,true);
			node._node.setAttribute("_edom_tagnameopen",xmlstring[0]);
			if (xmlstring[1]) {
				node._node.setAttribute("_edom_tagnameclose",xmlstring[1]);
			}
		}
		node = walker.nextNode();
	}
}

function bxe_toggleAllToSourceMode() {
	var nodes = bxe_getAllEditableAreas();
	for (var i = 0; i < nodes.length; i++) {
		var e = new Object();
		e.target =  nodes[i];
		bxe_toggleSourceMode(e);
	}
	
}

function bxe_toggleSourceMode(e) {
	try {
	var editableArea = e.target;

	if (editableArea._TagMode) {
			e = new eDOMEvent();
			e.setTarget(editableArea);
			e.initEvent("toggleTagMode");
	}
	if (!editableArea._SourceMode) {
		var xmldoc = editableArea.convertToXMLDocFrag();
		
		var form = document.createElement("textarea");
		//some stuff could go into a css file
		form.setAttribute("name","sourceArea");
		form.setAttribute("wrap","soft");
		form.style.backgroundColor = "rgb(255,255,200)";
		form.style.border = "0px";
		form.style.height = editableArea.getCStyle("height");
		form.style.width = editableArea.getCStyle("width");
		/*form.style.fontFamily = editableArea.getCStyle("font-family");
		form.style.fontSize = "12px";
		*/
		editableArea.removeAllChildren();
		
		var xmlstr = document.saveChildrenXML(xmldoc,true);
		form.value = xmlstr.str;
		
		var breaks = form.value.match(/[\n\r]/g);
		if (breaks) {
			breaks = breaks.length;
			form.style.minHeight = ((breaks + 1) * 13) + "px";
		}
		
		editableArea.appendChild(form)
		form.focus();
		//editableArea.appendChild(document.createTextNode(xmlstr.str));
		editableArea.XMLNode.prefix = xmlstr.rootPrefix;
		editableArea._SourceMode = true;
		editableArea.AreaInfo.SourceModeMenu.Checked = true;
		editableArea.AreaInfo.NormalModeMenu.Checked = false;
		bxe_updateXPath(editableArea);
		
	} else {
		var rootNodeName = editableArea.XMLNode.localName;
		if (editableArea.XMLNode.prefix != null) {
			rootNodeName = editableArea.XMLNode.prefix +":"+rootNodeName;
		}
		var innerHTML = '<'+rootNodeName;
		ns = editableArea.XMLNode.xmlBridge.getNamespaceDefinitions();
		for (var i in ns ) {
			if  (i == "xmlns") {
				innerHTML += ' xmlns="'+ ns[i] + '"';
			} else {
				innerHTML += ' xmlns:' + i + '="' + ns[i] +'"';
			}
		}
		
		innerHTML += '>'+editableArea.firstChild.value +'</'+rootNodeName +'>';
		
		var innerhtmlValue = documentLoadXML( innerHTML);
		if (innerhtmlValue) {
			editableArea.XMLNode._node = editableArea.XMLNode.xmlBridge;
			
			editableArea.XMLNode.removeAllChildren();
			editableArea.XMLNode._node.removeAllChildren();
			
			editableArea.XMLNode._node.appendAllChildren(innerhtmlValue.firstChild);

			
			
			editableArea._SourceMode = false;
			//preserve vdom...
			var eaVDOM = editableArea.XMLNode._vdom;
			editableArea.XMLNode = editableArea.XMLNode._node.ownerDocument.init(editableArea.XMLNode._node);
			editableArea.XMLNode.vdom = eaVDOM;

			editableArea.removeAllChildren();
			/*
			
			innerhtmlValue.documentElement.insertIntoHTMLDocument(editableArea,true);
			*/
			editableArea.setStyle("white-space",null);
			var xmlnode = editableArea.XMLNode._node;
			
			editableArea.XMLNode.insertIntoHTMLDocument(editableArea,true);
			editableArea.XMLNode.xmlBridge = xmlnode;
			
			editableArea.AreaInfo.SourceModeMenu.Checked = false;
			editableArea.AreaInfo.NormalModeMenu.Checked = true;
			/*normalize namesapces */
			if (editableArea.XMLNode.xmlBridge.parentNode.nodeType == 1) {
				nsparent = editableArea.XMLNode.xmlBridge.parentNode.getNamespaceDefinitions();
				for (var prefix in nsparent) {
					if (nsparent[prefix] == ns[prefix]) {
						xmlnode.removeAttributeNS(XMLNS,prefix);
					}
				}
			}
			var valid = editableArea.XMLNode.isNodeValid(true);
			if ( ! valid) {
				bxe_toggleSourceMode(e);
			}
			bxe_updateXPath(editableArea);
			
		}
	}
	}
	catch (e) {bxe_catch_alert(e);}

}

function bxe_toggleTextClass(e) {
	var sel = window.getSelection();
	var cssr = sel.getEditableRange();
	if (typeof e.additionalInfo.namespaceURI == "undefined") {
		e.additionalInfo.namespaceURI = "";
	}
	if (bxe_checkForSourceMode(sel)) {
		alert("You're in Source Mode. Not possible to use this button");
		return false;
	}
	//search, if we are already in this mode for anchorNode
	var node = sel.anchorNode.parentNode.XMLNode;
	
	while (node) {
		if (node.localName == e.additionalInfo.localName && node.namespaceURI == e.additionalInfo.namespaceURI) {
			return bxe_CleanInlineIntern(e.additionalInfo.localName,e.additionalInfo.namespaceURI);
		}
		node = node.parentNode;
	}
	
	/*if (!bxe_checkIsAllowedChild( e.additionalInfo.namespaceURI,e.additionalInfo.localName,sel)) {
		return false;
	}*/
	/*var cb = bxe_getCallback(e.additionalInfo.localName, e.additionalInfo.namespaceURI);
	if (cb ) {
		bxe_doCallback(cb, BXE_SELECTION);
		return;
	}*/
	
	if (sel.isCollapsed) {
			var newNode = new XMLNodeElement(e.additionalInfo.namespaceURI,e.additionalInfo.localName, 1 , true) ;
		
			sel.insertNode(newNode._node);
	/*		debug("valid? : " + newNode.isNodeValid());
	*/		
			newNode.makeDefaultNodes(false);
			if (newNode._node.firstChild) {
				var sel = window.getSelection();
				var startip = newNode._node.firstInsertionPoint();
				var lastip = newNode._node.lastInsertionPoint();
				sel.collapse(startip.ipNode, startip.ipOffset);
				sel.extend(lastip.ipNode, lastip.ipOffset);
				
			}
	} else {
		var styleHolder;
		sel.anchorNode.parentNode.normalize();
		
		var _node = bxe_config.xmldoc.createElementNS(e.additionalInfo.namespaceURI,e.additionalInfo.localName);
		var xmlnode = bxe_getXMLNodeByHTMLNode(sel.anchorNode.parentNode);
		xmlnode.betterNormalize();
		//FIXME: switch focus and anchor, if needed
		var _position = bxe_getChildPosition(sel.anchorNode);
		xmlnode.childNodes[_position].splitText(sel.focusOffset);
		xmlnode.childNodes[_position].splitText(sel.anchorOffset);
		var textNode = xmlnode.childNodes[_position + 1];
		xmlnode.insertBefore(_node, textNode);
		_node.appendChild(textNode);
		
		var id = _node.setBxeId();
		_node.XMLNode = _node.getXMLNode();
		_node.parentNode.XMLNode.isNodeValid(true,2);
		
		if(!(_node.XMLNode.makeDefaultNodes(true))) {
			bxe_Transform(id,"select",_node.parentNode.XMLNode);
		}
		
		//sel.toggleTextClass(e.additionalInfo.localName,e.additionalInfo.namespaceURI);
	}
	sel = window.getSelection();
	cssr = sel.getEditableRange();
	
//	var _node = cssr.updateXMLNodes();
	debug("isValid?" + _node.XMLNode.isNodeValid());
	bxe_history_snapshot_async();
}


function bxe_NodeInsertedParent(e) {
//	alert("document wide");
	var oldNode = e.target.XMLNode;
	var parent = e.additionalInfo;
	
	parent.XMLNode =  bxe_XMLNodeInit(parent);
	parent.XMLNode.previousSibling = oldNode.previousSibling;
	parent.XMLNode.nextSibling = oldNode.nextSibling;
	if (parent.XMLNode.previousSibling) {
		parent.XMLNode.previousSibling.nextSibling = parent.XMLNode;
	} 
	if (parent.XMLNode.nextSibling) {
		parent.XMLNode.nextSibling.previousSibling = parent.XMLNode;
	}
	parent.XMLNode.firstChild = oldNode;
	parent.XMLNode.lastChild = oldNode;
	parent.XMLNode.parentNode = oldNode.parentNode;
	oldNode.parentNode = parent.XMLNode;
	oldNode.previousSibling = null;
	oldNode.nextSibling = null;
	
}

function bxe_NodeRemovedChild (e) {
	var parent = e.target.XMLNode;
	var oldNode  = e.additionalInfo.XMLNode;
	oldNode.unlink();
}

function bxe_NodeBeforeDelete (e) {
	var node = e.target.XMLNode;
	node.unlink();
}

function bxe_NodePositionChanged(e) {
	var node = e.target;
	node.updateXMLNode();
}
	

function bxe_NodeAppendedChild(e) {
	var parent = e.target.XMLNode;
	var newNode  = e.additionalInfo;
	if (newNode.nodeType == 11) {
		var child = newNode.firstChild;
		while (child) {
			this.appendChildIntern(child.XMLNode);
			child = child.nextSibling;
			
		}
	} else {
		newNode  = newNode.XMLNode;
		parent.appendChildIntern(newNode);
	}
	
}

function bxe_NodeRemovedChildOnly (e) {
	var parent = e.target.XMLNode;
	var oldNode  = e.additionalInfo.XMLNode;

	var div = oldNode.lastChild;
	if (oldNode.firstChild) {
		var child = oldNode.firstChild;
		while (child ) {
			child.parentNode = oldNode.parentNode;
			child = child.nextSibling;
		}
		oldNode.previousSibling.nextSibling = oldNode.firstChild;
		oldNode.nextSibling.previousSibling = oldNode.lastChild;
		oldNode.firstChild.previousSibling = oldNode.previousSibling;
		oldNode.lastChild.nextSibling = oldNode.nextSibling;
		
	} else {
		oldNode.previousSibling.nextSibling = old.nextSibling;
		oldNode.nextSibling.previousSibling = old.previousSibling;
	}
	if (parent.firstChild == oldNode) {
		parent.firstChild = oldNode.nextSibling;
	}
	if (parent.lastChild == oldNode) {
		parent.lastChild = oldNode.previousSibling;
	}
	//oldNode.unlink();

	
}
function bxe_ContextPopup(e) {
	try {
	var node = e.target.XMLNode;
	var popup = e.additionalInfo;
	
	//return on xmlBridge Root nodes
	if (node.xmlBridge) {
		return 
	}
	if (node.nodeType == Node.ATTRIBUTE_NODE) {
		node = node.parentNode;
	}

	if (node.vdom && node.vdom.hasAttributes ) {
		
		var menui = popup.addMenuItem("Edit " + node.vdom.bxeName  + " Attributes", mozilla.getWidgetGlobals().EditAttributes.popup);
		menui.MenuPopup._node = node._node;
	}

	popup.addMenuItem("Copy "  + node.vdom.bxeName  + " Element", function (e) {
		var widget = e.currentTarget.Widget;
		var delNode = widget.MenuPopup.MainNode;
		delNode.copy();
	});
	var clip = mozilla.getClipboard();
	
	if (clip._clipboard) {
		var _clipboardNodeName = "";
		var _clipboardNamespaceUri = "";
		if (clip._clipboard.firstChild) {
			_clipboardNodeName = clip._clipboard.firstChild.nodeName;
			_clipboardNamespaceUri = clip._clipboard.firstChild.namespaceURI;
		} else {
			_clipboardNodeName = clip._clipboard.nodeName;
			_clipboardNamespaceUri = XHTMLNS;
		}
		if (!_clipboardNamespaceUri) { _clipboardNamespaceUri = ""};
		if (node.parentNode.isAllowedChild(_clipboardNamespaceUri, _clipboardNodeName)) {
			
			
			popup.addMenuItem("Append " + _clipboardNodeName + " from Clipboard", function (e) {
				var widget = e.currentTarget.Widget;
				var appNode = widget.MenuPopup.MainNode;
				var clip = mozilla.getClipboard();
				var clipNode = clip.getData(MozClipboard.TEXT_FLAVOR);
				eDOMEventCall("appendNode",document,{"appendToNode":appNode, "node": clipNode})
				
			});
		}
	}
	
	popup.addMenuItem("Delete "  + node.vdom.bxeName  + " Element", function (e) {
		var widget = e.currentTarget.Widget;
		var delNode = widget.MenuPopup.MainNode;
		//delNode.unlink();
		bxe_history_snapshot();
		
		var par = delNode.parentNode
		delNode.unlink();
		bxe_Transform(false,false,par);
	},"Deletes the Element and all its children");
	
	if (node.parentNode.canHaveText) {
		popup.addMenuItem("Clean ", function (e) {
			var widget = e.currentTarget.Widget;
			var delNode = widget.MenuPopup.MainNode;
			
			bxe_history_snapshot();
			
			var par = delNode.parentNode;
			delNode._node.removeElementOnly();
			bxe_Transform(false,false,par);
		}, "Removes the Element, but not its children");
	}

	if (node.previousSibling) {
		popup.addMenuItem("Move up", function (e) {
			var widget = e.currentTarget.Widget;
			var appNode = widget.MenuPopup.MainNode;
			var prevSibling = appNode.previousSibling;
			while (prevSibling && prevSibling._node.nodeType != 1 ) {
				if (prevSibling._node.nodeType == 3 && !prevSibling._node.isWhitespaceOnly) {
					break;
				}
				prevSibling = prevSibling.previousSibling;
			}
			if (prevSibling) {
				appNode.parentNode.insertBefore(appNode._node, prevSibling._node);
			}
			bxe_Transform(false,false,appNode.parentNode);
		});
	}
	
	if (node.nextSibling) {
		popup.addMenuItem("Move down", function (e) {
			var widget = e.currentTarget.Widget;
			var appNode = widget.MenuPopup.MainNode;
			var nextSibling = appNode.nextSibling;
			while (nextSibling && nextSibling._node.nodeType != 1) {
				if (nextSibling._node.nodeType == 3 && !nextSibling._node.isWhitespaceOnly) {
					break;
				}
				nextSibling = nextSibling.nextSibling;
			}
			if (nextSibling) {
				appNode.parentNode.insertAfter(appNode._node, nextSibling._node);
			}
			bxe_Transform(false,false,appNode.parentNode);
		});
	}
	

	if (node.vdom.bxeMenuentry) {
		popup.addSeparator();
		var _entries = node.vdom.bxeMenuentry;
		for (var i in _entries) {
			
			var n = popup.addMenuItem(_entries[i]['name'], function (e) {
				var widget = e.currentTarget.Widget;
				var appNode = widget.MenuPopup.MainNode;
				if (widget.bxeType && widget.bxeType == "popup") {
					var pop = window.open(widget.bxeCall,"foobar","width=600,height=600,resizable=yes,scrollbars=yes");
					window.bxe_lastNode = appNode;
					pop.focus();
				} else {
					return eval(widget.bxeCall + "(appNode)") ;
				}
			})
			n.bxeCall = _entries[i]['call'];
			n.bxeType = _entries[i]['type'];
			
		}
		
	}


	if (node.localName == "td" || node.localName == "th") {
			popup.addSeparator();
		// merge right
	//	popup.addSeparator();
		
		
		//split
		if (node._node.getAttribute("colspan") > 1) {
		var menui = popup.addMenuItem("Split right", function(e) {
			var widget = e.currentTarget.Widget;
			var _par = widget.MenuPopup.MainNode._node.parentNode;
			widget.MenuPopup.MainNode._node.TableCellSplitRight();
			bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
			
		});
		}
		
		if (node._node.getAttribute("rowspan") > 1) {
			
			var menui = popup.addMenuItem("Split down", function(e) {
				var widget = e.currentTarget.Widget;
				var _par = widget.MenuPopup.MainNode._node.parentNode;
				widget.MenuPopup.MainNode._node.TableCellSplitDown();
				bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
				//_par.updateXMLNode();
				
			});
		}
		
		
		var nextSibling = node.nextSibling;
		while (nextSibling && nextSibling.nodeType != 1) {
			nextSibling = nextSibling.nextSibling;
		}
		if (nextSibling && (nextSibling.localName == "td" || nextSibling.localName == "th")) {
			var menui = popup.addMenuItem("Merge right", function(e) {
				var widget = e.currentTarget.Widget;
				var _par = widget.MenuPopup.MainNode._node.parentNode;
				widget.MenuPopup.MainNode._node.TableCellMergeRight();
				bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
				//_par.updateXMLNode();
			});
		}
	
		var _par = node.parentNode;
		while(_par && _par.localName != "tr") {
			 _par = _par.parentNode;
		}
		
		/*if (_par && _par.localName == "tr") {
			var _parNext = _par.nextSibling;
			while (_parNext && _parNext.nodeType != 1) {
				_parNext = _parNext.nextSibling;
			}
			 
			if (_parNext && _parNext.localName == "tr" ) {
				
				var menui = popup.addMenuItem("Merge down", function(e) {
					var widget = e.currentTarget.Widget;
					var _par = widget.MenuPopup.MainNode._node.parentNode;
					widget.MenuPopup.MainNode._node.TableCellMergeDown();
					bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
					//_par.updateXMLNode();
				});
			}
		}*/
		var menui = popup.addMenuItem("Merge down", function(e) {
					var widget = e.currentTarget.Widget;
					var _par = widget.MenuPopup.MainNode._node.parentNode;
					widget.MenuPopup.MainNode._node.TableCellMergeDown();
					bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
					//_par.updateXMLNode();
				});
		
		var menui = popup.addMenuItem("Append Row", function(e) {
			var widget = e.currentTarget.Widget;
			
			widget.MenuPopup.MainNode._node.TableAppendRow();
			bxe_Transform(false,false,widget.MenuPopup.MainNode.parentNode);
			
		});
		var menui = popup.addMenuItem("Append Col", function(e) {
			var widget = e.currentTarget.Widget;
			var _par = widget.MenuPopup.MainNode._node.parentNode.parentNode;
			widget.MenuPopup.MainNode._node.TableAppendCol();
			bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
			
		});
		var menui = popup.addMenuItem("Remove Row", function(e) {
			var widget = e.currentTarget.Widget;
			var _par = widget.MenuPopup.MainNode._node.parentNode.parentNode;
			widget.MenuPopup.MainNode._node.TableRemoveRow();
			bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
			
		});
		
		var menui = popup.addMenuItem("Remove Col", function(e) {
			var widget = e.currentTarget.Widget;
			var _par = widget.MenuPopup.MainNode._node.parentNode.parentNode;
			widget.MenuPopup.MainNode._node.TableRemoveCol();
			bxe_Transform(true,false,widget.MenuPopup.MainNode.parentNode);
			
		});
		
		
	}
	popup.MainNode = node;
	} catch (e) { bxe_catch_alert(e);}
}

function bxe_NodeChanged(e) {

	var newNode = e.target;
	var oldNode = e.additionalInfo.XMLNode;
	newNode.XMLNode = bxe_XMLNodeInit(newNode);
	newNode.XMLNode.previousSibling = oldNode.previousSibling;
	newNode.XMLNode.nextSibling = oldNode.nextSibling;
	newNode.XMLNode.parentNode = oldNode.parentNode;
	newNode.XMLNode.firstChild = oldNode.firstChild;
	newNode.XMLNode.lastChild = oldNode.lastChild;

	if (!newNode.XMLNode.previousSibling ) {
		newNode.XMLNode.parentNode.firstChild = newNode.XMLNode;
	} else {
		newNode.XMLNode.previousSibling.nextSibling = newNode.XMLNode;
	}
	if (!newNode.XMLNode.nextSibling ) {
		newNode.XMLNode.parentNode.lastChild = newNode.XMLNode;
	} else {
		newNode.XMLNode.nextSibling.previousSibling = newNode.XMLNode;
	}
		
	oldNode.unlink();
	
}

function bxe_NodeInsertedBefore(e) {
	/*try {
		var oldNode = e.target.XMLNode;
		var newNode = e.additionalInfo;
		newNode.XMLNode =  bxe_XMLNodeInit(newNode);
		if (oldNode.parentNode) {
			oldNode.parentNode.insertBeforeIntern(newNode.XMLNode, oldNode);
		}
		if (newNode.firstChild ) {
			newNode.updateXMLNode();
		}
		if (oldNode.firstChild ) {
			oldNode.unlinkChildren();
			oldNode._node.updateXMLNode();
		}
	}
	catch(e) { 
		bxe_catch_alert(e);
	}*/
	

}

function bxe_appendNode(e) {
	var aNode = e.additionalInfo.appendToNode;
	bxe_history_snapshot();
	
	if (e.additionalInfo.node) {
		/*var cb = bxe_getCallback(e.additionalInfo.node.localName, e.additionalInfo.node.namespaceURI);
		if (cb ) {
			if (bxe_doCallback(cb, aNode)) {
				return;
			}
		}
		*/
		//var newNode = e.additionalInfo.node.init();
		var newNode = e.additionalInfo.node;
		if (newNode.nodeType == 11) {
			while (newNode.lastChild) {
				if (newNode.lastChild.nodeType == 1) {
					newNode.lastChild.setBxeIds(true);
				}
				newXMLNode= aNode.parentNode.insertBeforeIntern(newNode.lastChild,aNode._node.nextSibling);
			}
		} else {
			newXMLNode= aNode.parentNode.insertBeforeIntern(newNode,aNode._node.nextSibling);
			
		}
		newXMLNode.parentNode.isNodeValid(true,2);
		
		bxe_Transform();
	} else {

		/*ar cb = bxe_getCallback(e.additionalInfo.localName,e.additionalInfo.namespaceURI);
		
		if (cb ) {
			bxe_doCallback(cb, aNode);
			return;
		}*/
		var newNode =  bxe_config.xmldoc.createElementNS(e.additionalInfo.namespaceURI,e.additionalInfo.localName, 1 ) ;
		
		newNode = aNode.parentNode.insertAfter(newNode,aNode._node);
		
		var _id = newNode._node.setBxeId();
		//FIXME: double validity check...
		newNode.parentNode.isNodeValid(true,2);
		
		if( !newNode.makeDefaultNodes(e.additionalInfo.noPlaceholderText)) {
			
			if (!e.additionalInfo.noTransform) {
				dump("HHHHH\n");
				bxe_Transform(_id,"select",newNode.parentNode);
			}
		}
	}

	/*
	if (newNode._node.firstChild) {
		var sel = window.getSelection();
		var startip = newNode._node.firstInsertionPoint();
		var lastip = newNode._node.lastInsertionPoint();
		sel.collapse(startip.ipNode, startip.ipOffset);
		sel.extend(lastip.ipNode, lastip.ipOffset);
		
	}*/
		
}


function bxe_appendChildNode(e) {
		var aNode = e.additionalInfo.appendToNode;
		bxe_history_snapshot();
		var newNode = bxe_createXMLNode(e.additionalInfo.namespaceURI,e.additionalInfo.localName) ;
		if (e.additionalInfo.atStart && aNode.firstChild) {
			var _child = aNode.firstChild._node;
			while (_child) {
				newNode = aNode.insertBefore(newNode._node,_child);
				if (!newNode.parentNode.isNodeValid(true,2,true)) {
					
					aNode.removeChild(newNode);
					_child = _child.nextSibling;
				} else {
					_child = null;
					break;
				}
			}
		} else {
			newNode = aNode.appendChild(newNode);
		}
		newNode.parentNode.isNodeValid(true,2);
		//dump("JJJJJJJJJ\n");
		//debug("valid? : " + newNode.isNodeValid());
		var cb = bxe_getCallback(e.additionalInfo.localName, e.additionalInfo.namespaceURI);
		if (cb ) {
			bxe_doCallback(cb, newNode);
		} else {
			if( !newNode.makeDefaultNodes(e.additionalInfo.noPlaceholderText)) {
				
				if (!e.additionalInfo.noTransform) {
					bxe_Transform(false,false,newNode.parentNode);
				}
			}
		}
}



function bxe_changeLinesContainer(e) {
	bxe_history_snapshot();
	var nodeParts = e.additionalInfo.split("=");
	if (nodeParts.length < 2 ) {
		nodeParts[1] = null;
	}
	
	var cssr=window.getSelection().getEditableRange();
	var node = cssr.startContainer.getBlockParentFromXML();
	if (node) {
		var newContainer = node._node.changeElementName(nodeParts[1],nodeParts[0]);
		bxe_Transform(newContainer.getAttribute("__bxe_id"),cssr.startOffset,newContainer.parentNode.XMLNode);
	}
}



/* end mode toggles */

/* area mode stuff */

function bxe_getAllEditableAreas() {
	var nsResolver = new bxe_nsResolver(document.documentElement);
	var result = document.evaluate("/html/body//*[@bxe_xpath]", document.documentElement,nsResolver, 0, null);
	var node = null;
	var nodes = new Array();
	node = result.iterateNext()
	while (node) {
		//dump(result2.snapshotItem(result2.snapshotLength -1).saveXML() + "\n");
		nodes.push(node);
		node = result.iterateNext()
		
	}
	return nodes;
}

function bxe_getAll_bxeId() {
	var nsResolver = new bxe_nsResolver(document.documentElement);
	var result = document.evaluate("/html/body//*[@__bxe_id]", document.documentElement,nsResolver, 0, null);
	var node = null;
	var nodes = new Array();
	node = result.iterateNext()
	while (node) {
		//dump(result2.snapshotItem(result2.snapshotLength -1).saveXML() + "\n");
		nodes.push(node);
		node = result.iterateNext()
		
	}
	return nodes;
}

function bxe_alignAllAreaNodes() {
	var nodes = bxe_getAllEditableAreas();
	for (var i = 0; i < nodes.length; i++) {
		bxe_alignAreaNode(nodes[i].parentNode,nodes[i]);
	}
}

function bxe_alignAreaNode(menuNode,areaNode) {
	if (areaNode.display == "block") {
		menuNode.position("-8","5");
	} else {
		menuNode.position("0","0");
	}
	menuNode.draw();
}

/* debug stuff */
function BX_debug(object)
{
    var win = window.open("","debug");
	bla = "";
    for (b in object)
    {

        bla += b;
        try {

            bla +=  ": "+object.eval(b) ;
        }
        catch(e)
        {
            bla += ": NOT EVALED";
        };
        bla += "\n";
    }
    win.document.innerHTML = "";

    win.document.writeln("<pre>");
    win.document.writeln(bla);
    win.document.writeln("<hr>");
}

function BX_showInWindow(string)
{
    var win = window.open("","debug");

    win.document.innerHTML = "";
	win.document.writeln("<html>");
	win.document.writeln("<body>");

    win.document.writeln("<pre>");
	if (typeof string == "string") {
		win.document.writeln(string.replace(/</g,"&lt;"));
	}
	win.document.writeln("</pre>");
	win.document.writeln("</body>");
	win.document.writeln("</html>");
}

function bxe_about_box_fade_out (e) {
	bxe_about_box.node.style.display = "none";
	window.status = null;
}

function bxe_draw_widgets() {
	
	
	// make menubar
	bxe_menubar = new Widget_MenuBar();
	var img = document.createElement("img");
	img.setAttribute("src",mozile_root_dir + "images/bxe.png");
	
	img.setAttribute("align","right");
	bxe_menubar.node.appendChild(img);
	var submenu = new Array("Save",function() {eDOMEventCall("DocumentSave",document);});
	submenu.push("Save & Exit",function() {eDOMEventCall("DocumentSave",document,{"exit": true});});
	submenu.push("Exit",function() {eDOMEventCall("Exit",document);});
	bxe_menubar.addMenu("File",submenu);

	var submenu2 = new Array("Undo",function() {eDOMEventCall("Undo",document);},"Redo",function () {eDOMEventCall("Redo",document)});
	bxe_menubar.addMenu("Edit",submenu2);
	
	var submenu3 = new Array();
	submenu3.push("Show XML Document",function(e) {BX_showInWindow(bxe_getXmlDocument(true));})
	submenu3.push("Show RNG Document",function(e) {BX_showInWindow(bxe_getRelaxNGDocument());})
	
	bxe_menubar.addMenu("Debug",submenu3);
	
	
	var submenu4 = new Array();
	
	submenu4.push("About Bitflux Editor",function(e) { 
		bxe_about_box.setText("");
		bxe_about_box.show(true);
		
	});
	
	submenu4.push("Help",function (e) { 
		bla = window.open("http://wiki.bitfluxeditor.org","help","width=800,height=600,left=0,top=0");
		bla.focus();
	
	});

	submenu4.push("BXE Website",function (e) { 
		bla = window.open("http://www.bitfluxeditor.org","help","width=800,height=600,left=0,top=0");
		bla.focus();
	
	});

	submenu4.push("Show System Info", function(e) {
		var modal = new Widget_ModalBox();
		modal.node = modal.initNode("div","ModalBox");
		modal.Display = "block";
		modal.node.appendToBody();
		modal.position(100,100,"absolute");
		modal.initTitle("System Info");
		modal.initPane();
		var innerhtml =  "<br/>BXE Version: " + BXE_VERSION  + "<br />";
		innerhtml += "BXE Build Date: " + BXE_BUILD + "<br/>";
		innerhtml += "BXE Revision: " + BXE_REVISION + "<br/><br/>";
		innerhtml += "User Agent: " + navigator.userAgent + "<br/><br/>";
		modal.PaneNode.innerHTML = innerhtml;
		modal.draw();
		var subm = document.createElement("input");
		subm.setAttribute("type","submit");
		subm.setAttribute("value","OK");
		subm.onclick = function(e) {
			var Widget = e.target.parentNode.parentNode.Widget;
			e.target.parentNode.parentNode.style.display = "none";
		}
		modal.PaneNode.appendChild(subm);
		
	});

	submenu4.push("Report Bug",function(e) { 
		bla = window.open("http://bugs.bitfluxeditor.org/enter_bug.cgi?product=Editor&version="+BXE_VERSION+"&priority=P3&bug_severity=normal&bug_status=NEW&assigned_to=&cc=&bug_file_loc=http%3A%2F%2F&short_desc=&comment=***%0DVersion: "+BXE_VERSION + "%0DBuild: " + BXE_BUILD +"%0DUser Agent: "+navigator.userAgent + "%0D***&maketemplate=Remember+values+as+bookmarkable+template&form_name=enter_bug","help","");
		bla.focus();
		
	});
	
	
	bxe_menubar.addMenu("Help",submenu4);
	
	bxe_menubar.draw();
	
	//make toolbar
	
	bxe_toolbar = new Widget_ToolBar();
	bxe_format_list = new Widget_MenuList("m",function(e) {eDOMEventCall("changeLinesContainer",document,this.value)});

	bxe_toolbar.addItem(bxe_format_list);
	
	bxe_toolbar.addButtons(bxe_config.getButtons());
	
	
	bxe_toolbar.draw();

	bxe_status_bar = new Widget_StatusBar();
	/* var ea = bxe_getAll_bxeId();
	for (var i = 0; i < ea.length; i++) {
		
	ea[i].addEventListener("click",MouseClickEvent,false);
	} */

	// if not content editable and ptb is enabled then hide the toolbar (watch out
	// for selection within the toolbar itself though!)
	
	
	window.setTimeout(bxe_about_box_fade_out, 1000);
}

function MouseClickEvent(e) {
	e.stopPropagation();
	var target = e.currentTarget;
	
	
	if(target.userModifiable && bxe_editable_page) {
		return bxe_updateXPath(e.currentTarget);
	}
	return true;
}

function bxe_updateXPath(e) {
	var sel = window.getSelection();
	var cssr = sel.getRangeAt(0);
	if (e && e.localName == "TEXTAREA") {
		bxe_format_list.removeAllItems();
		bxe_format_list.appendItem("-Source Mode-","");
		bxe_status_bar.buildXPath(e.parentNode);
		
	}
	else if (cssr) {
		/*if ( cssr.top._SourceMode) {
			//clear list
			bxe_format_list.removeAllItems();
			bxe_format_list.appendItem("-Source Mode-","");
			bxe_status_bar.buildXPath(cssr.top);

		} else */{
			if (e) {
				bxe_status_bar.buildXPath(e);
			} else {
				bxe_status_bar.buildXPath(sel.anchorNode);
			}
			
			
			bxe_format_list.removeAllItems();
			var block = cssr.startContainer.getBlockParentFromXML();
			if (block  ) {
				var thisNode = block;
				if (!thisNode) {
					bxe_format_list.appendItem("no block found","");
					return false;
				}
				
				var ac = thisNode.parentNode.allowedChildren;
				
				var menuitem;
				var thisLocalName = thisNode.localName;
				var thisNamespaceURI = thisNode.namespaceURI;
				
				for (i = 0; i < ac.length; i++) {
					if (ac[i].nodeType != 3 && !ac[i].bxeDontshow && ac[i].vdom.canHaveChildren)  {
						menuitem = bxe_format_list.appendItem(ac[i].vdom.bxeName, ac[i].localName + "=" + ac[i].namespaceURI);
						if (ac[i].localName == thisLocalName &&  ac[i].namespaceURI == thisNamespaceURI) {
							menuitem.selected=true;
						}
					}
				}
				
				
			} else {
				bxe_format_list.appendItem("no block found","");
			}
		}
	}
}

function bxe_delayedUpdateXPath() {
	if (bxe_delayedUpdate) {
		window.clearTimeout(bxe_delayedUpdate);
	}
	bxe_delayedUpdate = window.setTimeout("bxe_updateXPath()",100);
}

function bxe_ContextMenuEvent(e) {

	//var sel = window.getSelection();
	//var cssr = sel.getEditableRange();
	var node = e.target.getParentWithXMLNode();
	if (!node) {
		return true;
	}
	
	if (node.XMLNode.nodeType != Node.ATTRIBUTE_NODE && node.XMLNode.vdom.bxeNoteditable) {
		return false;
	}
	/*if (node != e.target) {
		node = e.target;
	}
	var _n = node;
	while(_n.nodeType == 1) {
		if (_n == cssr.top) {
			break;
		}
		_n = _n.parentNode;
	}
	if (_n != cssr.top) {
		return false;
	}*/
	bxe_context_menu.show(e,node);
	e.stopPropagation();
	e.returnValue = false;
	e.preventDefault();
	return false;
}

function bxe_UnorderedList() {
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	var lines = window.getSelection().toggleListLines("ul", "ol");
	lines[0].container.updateXMLNode();
	var li = lines[0].container;
	while (li ) {
		if (li.nodeName == "li") {
			li.XMLNode.namespaceURI = XHTMLNS;
		}
		var attr = li.XMLNode.attributes;
		for (var i in attr) {
			if (! li.XMLNode.isAllowedAttribute(attr[i].nodeName)) {
				li.XMLNode.removeAttribute(attr[i].nodeName);
			}
		}

		li = li.nextSibling;
	}
	lines[0].container.parentNode.setAttribute("class","type1");
	bxe_updateXPath();
}

function bxe_OrderedList() {
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var lines = window.getSelection().toggleListLines("ol", "ul");

	lines[0].container.updateXMLNode();
	
	var li = lines[0].container;
	while (li ) {
		if (li.nodeName == "li") {
			li.XMLNode.namespaceURI = XHTMLNS;
		}
		var attr = li.XMLNode.attributes;
		for (var i in attr) {
			if (! li.XMLNode.isAllowedAttribute(attr[i].nodeName)) {
				li.XMLNode.removeAttribute(attr[i].nodeName);
			}
		}
		li = li.nextSibling;
	}
	
	// needed by unizh
	lines[0].container.parentNode.setAttribute("class","type1");
	bxe_updateXPath();
}

function bxe_InsertObject() {
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	var object = documentCreateXHTMLElement("object");
	
	sel.insertNode(object);
}

function bxe_InsertAsset() {
	//this code is quite lenya specific....
	// especially the unizh: check
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var cssr = sel.getEditableRange();
	var pN = cssr.startContainer.parentNode;
	//FIXME: unizh code should be outsourced...
	if ((pN.XMLNode.localName == "highlight-title" && pN.XMLNode.namespaceURI == "http://unizh.ch/doctypes/elements/1.0") ||
	(pN.XMLNode.localName == "asset" && pN.XMLNode.namespaceURI == "http://apache.org/cocoon/lenya/page-envelope/1.0")) {
		alert("Asset is not allowed here");
		return false;
	}
	
	if (!bxe_checkIsAllowedChild("http://apache.org/cocoon/lenya/page-envelope/1.0","asset",sel, true) && !bxe_checkIsAllowedChildOfNode("http://apache.org/cocoon/lenya/page-envelope/1.0","asset",pN.parentNode, true)) {
		alert ("Asset is not allowed here");
		return false;
	}
	var object = document.createElementNS("http://apache.org/cocoon/lenya/page-envelope/1.0","asset");
	var cb = bxe_getCallback("asset","http://apache.org/cocoon/lenya/page-envelope/1.0");
	if (cb ) {
		bxe_doCallback(cb, object);
	} 
	else {
	
		sel.insertNode(object);
	}
}

function bxe_InsertImage() {
	
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var mod = mozilla.getWidgetModalBox("Enter the image url or file name:", function(values) {
		if(values.imgref == null) // null href means prompt canceled
			return;
		if(values.imgref == "") 
			return; // ok with no name filled in

		
		var img = documentCreateXHTMLElement("img");
		img.firstChild.setAttribute("src",values.imgref);
		sel.insertNode(img);
		img.updateXMLNode();
		img.setAttribute("src",values.imgref);
	});
	
	mod.addItem("imgref", "", "textfield","Image URL:");
	mod.show(100,50,"fixed");
	
}

function bxe_checkForSourceMode(sel) {
	if (bxe_format_list.node.options.length == 1 && bxe_format_list.node.options.selectedIndex == 0) {
		if ( bxe_format_list.node.options[0].text == "-Source Mode-") {
			alert("You're in Source Mode. Not possible to use this button");
			return true;
		}
	}
	// the following is legacy code. actually not needed anymore, AFAIK..
	var cssr = sel.getEditableRange();
	if (cssr && cssr.top._SourceMode) {
		alert("You're in Source Mode. Not possible to use this button");
		return true;
	}
	return false;
}

function bxe_checkIsAllowedChild(namespaceURI, localName, sel, noAlert) {
	if (!sel) {
		sel = window.getSelection();
	}
	
	var cssr = sel.getEditableRange();
	var parentnode = null;
	if (cssr.startContainer.nodeType != 1) {
		parentnode = cssr.startContainer.parentNode;
	} else {
		parentnode = cssr.startContainer;
	}
	return bxe_checkIsAllowedChildOfNode(namespaceURI,localName, parentnode, noAlert);
	
}

function bxe_checkIsAllowedChildOfNode(namespaceURI,localName, node, noAlert) {
	if (localName == "#text") {
		localName = null;
	}
	if (localName == null || node.XMLNode.isAllowedChild(namespaceURI, localName) ) {
		return true;
	} else {
		if (!noAlert) {
			alert (localName + " is not allowed as child of " + node.XMLNode.localName);
		}
		return false;
	}
}

function bxe_InsertTable() {
	var sel = window.getSelection();
	var cssr = sel.getEditableRange();
	
	if (!bxe_checkIsAllowedChild("","table",sel, true) &&  !bxe_checkIsAllowedChildOfNode("","table",cssr.startContainer.parentNode.parentNode, true)) {
		alert ("Table is not allowed here");
		return false;
	}

	var object = documentCreateXHTMLElement("table");
	//sel.insertNode(object);
	window.bxe_ContextNode = BXE_SELECTION;
	bxe_InsertTableCallback();
}


function bxe_InsertTableCallback(replaceNode) {
	
	var sel = window.getSelection();

	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var mod = mozilla.getWidgetModalBox("Create Table", function(values) {
		var te = documentCreateTable(values["rows"], values["cols"]);
		if(!te) {
			alert("Can't create table: invalid data");
		}
		else if (window.bxe_ContextNode == BXE_SELECTION) {
			//te.setAttribute("class", bxe_config.options[OPTION_DEFAULTTABLECLASS]);

			var sel = window.getSelection(); 	
			if (!bxe_checkIsAllowedChild("","table",sel, true)) {
				var xmlnode = bxe_splitAtSelection();
				xmlnode.parentNode.insertBefore(te,xmlnode);
			} else {
			
				sel.insertNodeRaw(te, true);
			}
			bxe_Transform();
		/*} else if (window.bxe_ContextNode){
			var newNode = te.init();
			window.bxe_ContextNode.parentNode.insertAfter(newNode, window.bxe_ContextNode);
			debug("valid? : " + newNode.isNodeValid());*/
		} else if (replaceNode) {
			replaceNode._node.parentNode.replaceChild(te, replaceNode._node);
			bxe_Transform();
		}
	});
	mod.addItem("rows",2,"textfield","number of rows");
	mod.addItem("cols",2,"textfield","number of cols");
	mod.show(100,50, "fixed");
	
}

function bxe_CleanInline(e) {
	bxe_CleanInlineIntern();
}

function bxe_CleanInlineIntern(localName, namespaceUri) {
	var sel = window.getSelection();
	var doitagain = 0;
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var cssr = sel.getEditableRange();
	if(cssr.collapsed)
		return;
 
	// go through all text nodes in the range and link to them unless already set to cssr link
	var textNodes = cssr.textNodes;
	for(i=0; i<textNodes.length; i++) {
		// figure out cssr and then it's on to efficiency before subroutines ... ex of sub ... 
		// try text nodes returning one node ie/ node itself! could cut down on normalize calls ...
		if (textNodes[i].parentNode.XMLNode) {
		var textContainer = textNodes[i].parentNode.XMLNode._node;
		if (textNodes[i].parentNode && textNodes[i].parentNode.getCStyle("display") == "inline") {
			if (localName) {
				if (textContainer.parentNode && textContainer.parentNode.firstChild == textContainer) {
					textNodes.push(textContainer);
				}
				if(!(textContainer.XMLNode.localName == localName &&
				 textContainer.XMLNode.namespaceURI == namespaceUri)) {
					 continue;
				}
			}
			if(textContainer.childNodes.length > 1) {
				var siblingHolder;
				
				// leave any nodes before or after cssr one with their own copy of the container
				if(textNodes[i].previousSibling) {
					if (textNodes[i].previousSibling.nodeType == 3) {
						var siblingHolder = textContainer.cloneNode(false);
						textContainer.parentNode.insertBefore(siblingHolder, textContainer);
						siblingHolder.appendChild(textNodes[i].previousSibling);
					}
				}
				
				if(textNodes[i].nextSibling) { 
					if (textNodes[i].nextSibling.nodeType == 3) {
						var siblingHolder = textContainer.cloneNode(false);
						if(textContainer.nextSibling) {
							textContainer.parentNode.insertBefore(siblingHolder, textContainer.nextSibling);
						} else {  
							textContainer.parentNode.appendChild(siblingHolder);
						}
						siblingHolder.appendChild(textNodes[i].nextSibling);
					} else {
						textContainer.split(1);
					}
					
				}
			}
			// rename it to span and remove its href. If span is empty then delete span
			if (textContainer.parentNode) {
				doitagain++;
				textContainer.parentNode.removeChildOnly(textContainer);
			} 
				
		}
}
	}
	
	
	
	if (doitagain > 1 || (!localName && cssr.startContainer.parentNode.getCStyle("display") == "inline")) {
		bxe_CleanInlineIntern(localName,namespaceUri);
	} else {
		bxe_Transform();
	}
}


function bxe_DeleteLink(e) {
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	
	var cssr = sel.getEditableRange();
	
	var textContainer = sel.anchorNode.parentNode;
	
	if(textContainer.nodeNamed("span") && textContainer.getAttribute("class") == "a" )
	{
		textContainer.parentNode.removeChildOnly(textContainer);
		
	}
	
	
	
	sel.selectEditableRange(cssr);
	
	
	sel.anchorNode.updateXMLNode();
}


function bxe_InsertLink(e) {
	
	var sel = window.getSelection();
	if (bxe_checkForSourceMode(sel)) {
		return false;
	}
	var aValue = "";
	if (sel.anchorNode.parentNode.XMLNode.localName == "a") {
		aValue = sel.anchorNode.parentNode.getAttribute("href");
	}
	else if(sel.isCollapsed) { // must have a selection or don't prompt
		return;
	}
	
	if (!bxe_checkIsAllowedChild(XHTMLNS,"a",sel)) {
		return false;
	}
	
	
	var mod = mozilla.getWidgetModalBox("Enter a URL:", function(values) {
		var href = values["href"];
		if(href == null) // null href means prompt canceled - BUG FIX FROM Karl Guertin
			return;
		var sel = window.getSelection();
		if (sel.anchorNode.parentNode.XMLNode.localName == "a") {
		 sel.anchorNode.parentNode.setAttribute("href", href);
		 return true;
		}
		if(href != "") 
			sel.linkText(href);
		else
			sel.clearTextLinks();
		
		sel.anchorNode.parentNode.updateXMLNode();
	}
	);
		
	
	mod.addItem("href",aValue,"textfield","Enter a URL:");
	mod.show(100,50, "fixed");
	
	
	return;
}

function bxe_insertLibraryLink() {
	drawertool.cssr = window.getSelection().getEditableRange();
	drawertool.openDrawer( 'liblinkdrawer' );
	return;

}

function bxe_catch_alert(e ) {
	
	alert(bxe_catch_alert_message(e));
}

function bxe_catch_alert_message(e) {
	var mes = "ERROR in Bitflux Editor:\n"+e.message +"\n";
	try
	{
		if (e.filename) {
			mes += "In File: " + e.filename +"\n";
		} else {
			mes += "In File: " + e.fileName +"\n";
		}
		
	}
	catch (e)
	{
		mes += "In File: " + e.fileName +"\n";
	}
	try
	{
		mes += "Linenumber: " + e.lineNumber + "\n";
	}
	catch(e) {}
	
	mes += "Type: " + e.name + "\n";
	mes += "Stack:" + e.stack + "\n";
	return mes;
}

function bxe_exit(e) {
	if (bxe_checkChangedDocument()) {
		if (confirm( "You have unsaved changes.\n Click cancel to return to the document.\n Click OK to really leave to page.")) {
			bxe_lastSavedXML = bxe_getXmlDocument();
			window.location = bxe_config.exitdestination;
		}
	} else {
		bxe_lastSavedXML = bxe_getXmlDocument();
		window.location = bxe_config.exitdestination;
	}
	
}

function bxe_checkChangedDocument() {
	var xmlstr = bxe_getXmlDocument();
	if (bxe_editable_page && xmlstr && xmlstr != bxe_lastSavedXML) {
		return true;
	} else {
		return false;
	}
}

function bxe_not_yet_implemented() {
	alert("not yet implemented");
}


/* bxe_nsResolver */

function bxe_nsResolver (node) {
	this.metaTagNSResolver = null;
	this.metaTagNSResolverUri = null;
	
	//this.htmlDocNSResolver = null;
	this.xmlDocNSResolver = null;
	this.node = node;
	
	
}

bxe_nsResolver.prototype.lookupNamespaceURI = function (prefix) {
	var url = null;
	// if we never checked for meta bxeNS tags, do it here and save the values in an array for later reusal..
	if (!this.metaTagNSResolver) {
		var metas = document.getElementsByName("bxeNS");
		this.metaTagNSResolver = new Array();
		for (var i=0; i < metas.length; i++) {
			if (metas[i].localName.toLowerCase() == "meta") {
				var ns = metas[i].getAttribute("content").split("=");
				this.metaTagNSResolver[ns[0]] = ns[1]
			}
		}
	}
	//check if the prefix was there and return it
	if (this.metaTagNSResolver[prefix]) {
		return this.metaTagNSResolver[prefix];
	}
	/* there are no namespaces in even xhtml documents (or mozilla discards them somehow or i made a stupid mistake
	therefore no NS-lookup in document. */
	/*
	if (! this.htmlDocNSResolver) {
		this.htmlDocNSResolver = document.createNSResolver(document.documentElement);
	}
	url = this.htmlDocNSResolver.lookupNamespaceURI(prefix);
	if (url) {
		return url;
	}
	*/
	
	//create NSResolver, if not done yet
	if (! this.xmlDocNSResolver) {
		this.xmlDocNSResolver = this.node.ownerDocument.createNSResolver(this.node.ownerDocument.documentElement);
	}
	
	//lookup the prefix
	url = this.xmlDocNSResolver.lookupNamespaceURI(prefix);
	if (url) {
		return url;
	}
	// if still not found and we want the bxe prefix.. return that
	if (prefix == "bxe") {
		return BXENS;
	}
	
	if (prefix == "xhtml") {
		return XHTMLNS;
	}
	
	//prefix not found
	return null;
}

bxe_nsResolver.prototype.lookupNamespacePrefix = function (uri) {
	
	if (!this.metaTagNSResolverUri) {
		var metas = document.getElementsByName("bxeNS");
		this.metaTagNSResolverUri = new Array();
		for (var i=0; i < metas.length; i++) {
			if (metas[i].localName.toLowerCase() == "meta") {
				var ns = metas[i].getAttribute("content").split("=");
				this.metaTagNSResolverUri[ns[1]] = ns[0]
			}
		}
	}
	//check if the prefix was there and return it
	if (this.metaTagNSResolverUri[uri]) {
		return this.metaTagNSResolverUri[uri];
	}
	return null;
}
// replaces the function from mozile...
documentCreateXHTMLElement = function (elementName,attribs) {

	var newNode = document.createElementNS(null, elementName);
	if (attribs) {
		for (var i = 0; i < attribs.length ;  i++) {
			newNode.setAttributeNS(attribs[i].namespaceURI, attribs[i].localName,attribs[i].value);
		}
	}
	return newNode;
	
	
}

function bxe_InternalChildNodesAttrChanged(e) {
	var node = e.target;
	var attribs = node.attributes;
	//we have to replace the old internalnode, redrawing of new object-sources seem not to work...
	var newNode = document.createElementNS(node.InternalChildNode.namespaceURI, node.InternalChildNode.localName);
	for (var i = 0; i < attribs.length ;  i++) {
		var prefix = attribs[i].localName.substr(0,5);
		if (prefix != "_edom" && prefix != "__bxe") {
			newNode.setAttributeNS(attribs[i].namespaceURI,attribs[i].localName,attribs[i].value);
		}
	}
	node.replaceChild(newNode,node.InternalChildNode);
	newNode.setAttribute("_edom_internal_node","true");
	node.InternalChildNode = newNode;
	createTagNameAttributes(node,true)
	
	
	
	
}

function bxe_registerKeyHandlers() {
	if (bxe_editable_page) {
		document.addEventListener("keypress", keyPressHandler, true);
//key up and down handlers are needed for interapplication copy/paste without having native-methods access
//if you're sure you have native-methods access you can turn them off
		document.addEventListener("keydown", keyDownHandler, true);
		document.addEventListener("keyup", keyUpHandler, true);
	}
}

function bxe_disableEditablePage() {
	
	bxe_deregisterKeyHandlers();
	bxe_editable_page = false;
	document.removeEventListener("contextmenu",bxe_ContextMenuEvent, false);
	
}

function bxe_deregisterKeyHandlers() {
	document.removeEventListener("keypress", keyPressHandler, true);
//key up and down handlers are needed for interapplication copy/paste without having native-methods access
//if you're sure you have native-methods access you can turn them off
	document.removeEventListener("keydown", keyDownHandler, true);
	document.removeEventListener("keyup", keyUpHandler, true);
}

function bxe_insertContent(content, replaceNode, options) {
	window.setTimeout(function() {bxe_insertContent_async(content,replaceNode,options);},1);
}
// (string || node) node, (node) replaceNode
function bxe_insertContent_async(node,replaceNode, options) {
	var docfrag;
	if (typeof node == "string") {
        docfrag = node.convertToXML()
	} else {
		docfrag = node;
	}
	var oldStyleInsertion = false;
	try {
	if (replaceNode == BXE_SELECTION) {
		//FIXME 2.0 doesn't work yet
		var sel = window.getSelection();
		;
		
		//var _node = _currentNode.prepareForInsert();
		if (options & BXE_SPLIT_IF_INLINE) {
			var  _currentNode = docfrag.lastChild
			while (_currentNode && _currentNode.nodeType == 3) {
				_currentNode = _currentNode.previousSibling;
			}
			if (!_currentNode) {
				_currentNode = docfrag.lastChild;
			}
			if (!bxe_checkIsAllowedChild(_currentNode.namespaceURI,_currentNode.localName,sel, true)) {
				var cssr = sel.getEditableRange();
				var textNode = sel.anchorNode.XMLNode._node;
				textNode.splitText(cssr.startOffset);
				var _position = bxe_getChildPosition(textNode);
				var lala = sel.anchorNode.parentNode.XMLNode._node;
				lala.split(_position);
				
			    lala.parentNode.insertBefore(docfrag,lala.nextSibling);
				bxe_Transform();
				return ;
			}
		}
		sel.insertNodeRaw(docfrag);
		bxe_Transform();
		return ;
	} else if (replaceNode) {
		
		//var newNode = docfrag.firstChild.init();
		newNode = docfrag.firstChild;
		replaceNode.parentNode.replaceChild(newNode,replaceNode);
		//newNode._node.updateXMLNode();
		//debug("valid? : " + newNode.getXMLNode().isNodeValid());
		bxe_Transform(false,false,replaceNode.parentNode);
	} else {
		//FIXME 2.0
		//docfrag.firstChild.init();
		var sel= window.getSelection();
		var cssr =sel.getEditableRange();
		eDOMEventCall("appendNode",document,{"appendToNode":cssr.startContainer.parentNode.XMLNode, "node": docfrag.firstChild})
	}
		} catch(e) {
		bxe_catch_alert(e);
	}
}

String.prototype.convertToXML = function() {
	var BX_parser = new DOMParser();
	var content = this.toString();
	if (content.indexOf("<") >= 0) {
		
		content = BX_parser.parseFromString("<?xml version='1.0'?><rooot>"+content+"</rooot>","text/xml");
		content = content.documentElement;
		
		BX_tmp_r1 = document.createRange();
		
		BX_tmp_r1.selectNodeContents(content);
		content = BX_tmp_r1.extractContents();
		
	} else {
		content = document.createTextNode(content);
	}
	return content;
	
}

function bxe_getCallback (nodeName, namespaceURI) {
	
	if (bxe_config.callbacks[namespaceURI + ":" + nodeName]) {
		return bxe_config.callbacks[namespaceURI + ":" + nodeName];
	} else {
		return null;
	}
}

function bxe_doCallback(cb, node ) {
	window.bxe_ContextNode = node;
	//this is for prechecking, if an eventual popup should be called at all
	if (cb["precheck"]) {
		if (!(eval(cb["precheck"] +"(node)"))) {
			return false;
		} 
	}
	if (cb["type"] == "popup") {
		
		
		var pop = window.open(cb["content"],"popup","width=600,height=600,resizable=yes,scrollbars=yes");
		pop.focus();
		
	} else if (cb["type"] == "function") {
		return eval(cb["content"] +"(node)");
	}
}
		
function bxe_checkIfNotALink (node) {
	var sel = window.getSelection();
	if (sel.anchorNode.parentNode.XMLNode.localName == "a" || sel.focusNode.parentNode.XMLNode.localName == "a") {
		alert("There is already a link here, please use the \"Edit Attributes\" function, to edit the link.");
		return false;
	}
	return true;
}

function bxe_alert(text) {
	var widg = mozilla.getWidgetModalBox("Alert");
	widg.addText(text);
	widg.show(100,50, "fixed");
}

function bxe_validationAlert(messages) {
	var widg = mozilla.getWidgetModalBox("Validation Alert");
	for (i in messages) {
		widg.addText( messages[i]["text"] );
	}
	widg.show((window.innerWidth- 500)/2,50, "fixed");
	
}
function bxe_getDirPart(path) {
	
	return path.substring(0,path.lastIndexOf("/") + 1);
}

function bxe_nodeSort(a,b) {
	if (a.nodeName > b.nodeName) {
		return 1;
	} else {
		return -1;
	}
}

function bxe_showImageDrawer() {
	drawertool.cssr = window.getSelection().getEditableRange();
	drawertool.openDrawer('imagedrawer');
}

function bxe_ShowAssetDrawer() {
    drawertool.cssr = window.getSelection().getEditableRange();
    if (drawertool.cssr) {
        drawertool.openDrawer('assetdrawer');
    }
}

function bxe_start_plugins () {
	
	var ps = bxe_config.getPlugins();
	
	if (ps.length > 0) {
		for (var i = 0; i < ps.length; i++) {
			var p = bxe_plugins[ps[i]];
			if (p.start) {
				p.start(bxe_config.getPluginOptions(ps[i]));
			}
		}
	}
}


function bxe_Transform_async() {
	window.setTimeout("bxe_Transform()",10);
}
	

function bxe_Transform(xpath, position, validateNode) {
	startTimer = new Date();
	dump("TRANSORM\n");
	var xml = bxe_config.xmldoc;
	
	
	var node = xml.documentElement;
	var sel = window.getSelection();
	if (sel && sel.anchorNode) {
		var _topId = bxe_getBxeId(sel.anchorNode.parentNode);
	}
	var _childPosition = bxe_getChildPosition(sel.anchorNode);
	var _offset =sel.anchorOffset;
	
	
	bxe_config.xmldoc.documentElement.init();
	
	dump ("getDomDocument " + (new Date() - startTimer)/1000 + " sec\n");
	/*var processor = new XSLTProcessor();
	dump ("New Processor " + (new Date() - startTimer)/1000 + " sec\n"); 
	processor.importStylesheet(bxe_config.xsldoc);
	dump ("New importStylesheet " + (new Date() - startTimer)/1000 + " sec\n");*/
	var xmldoc = bxe_config.processor.transformToFragment(xml,document);
	dump ("transformToFragment " + (new Date() - startTimer)/1000 + " sec\n");
	
	var bxe_area = document.getElementById("bxe_area");
	
	bxe_area.removeAllChildren();
	bxe_area.style.display="none";
	bxe_area.appendChild(xmldoc);
	dump ("remove and Append " + (new Date() - startTimer)/1000 + " sec\n");
	
	bxe_init_serverIncludes(bxe_area);
	dump ("serverIncludes " + (new Date() - startTimer)/1000 + " sec\n");
	
	bxe_area.style.display="block";
	dump ("display=block " + (new Date() - startTimer)/1000 + " sec\n");
	
	bxe_init_htmldocument();
	
	//status bar neu positionieren
	bxe_status_bar.positionize();
	
	dump ("bxe_init_htmldocument  " + (new Date() - startTimer)/1000 + " sec\n");
	var valid = false;
	if (validateNode) {
		if (valid = validateNode.isNodeValid(true)) {
			dump ("node is valid \n");
		} else {
			dump("node is not valid \n");
		}
	} else {
		 valid = xml.XMLNode.validateDocument();
	}
	dump ("validateDocument " + (new Date() - startTimer)/1000 + " sec\n");
	if (!valid) {
		dump("Document not valid. Do it again...\n");
		
		return bxe_history_undo();
		
	}
	
	
	
	
	//bxe_config.xmldoc.insertIntoHTMLDocument();
	//dump ("insertIntoHTMLDocument " + (new Date() - startTimer)/1000 + " sec\n");
	if (_topId) {
		var _topNode = bxe_getHTMLNodeByBxeId(_topId);
	}
	//var ip = documentCreateInsertionPoint(_topNode, _topNode.childNodes[_childPosition], _offset);
		
	if (typeof xpath == "string") {
		var _node = bxe_getHTMLNodeByBxeId(xpath);
		sel = window.getSelection();
		
		// the selection stuff does not always work
		if (_node) {
			try {
				if (position == "select") {
					sel.collapse(_node.firstChild,0);
					sel.extend(_node.firstChild,_node.firstChild.length);
				} else {
					sel.collapse(_node.firstChild,position);
				}
			} catch(e) {
				dump("Cursor selection didn't work (somehow expected behaviour). Exception dump: \n");
				dump(e);
				dump("\n");
			}
		}
		
		/*die();
		*/
		/*var ip = documentCreateInsertionPoint(_node.parentNode, _node, position);
		ip.forwardOne();*/
		//dump(bxe_getHTMLNodeByBxeId(xpath).firstChild.nodeValue + "\n");
		//sel.extend(bxe_getHTMLNodeByBxeId(xpath),2);
		
	}
	else if (!_topNode) {
		
	}
	else if (xpath) {
		if (_topNode.nextSibling && _topNode.nextSibling.firstChild) {
			sel.collapse(_topNode.nextSibling.firstChild,0);//childNodes[_childPosition], _offset);
		} else if (_topNode.nextSibling) {
			sel.collapse(_topNode.nextSibling,0);
		}
	} else {
		try {
		sel.collapse(_topNode.childNodes[_childPosition], _offset);
		} catch(e) {
			//didn't work
		}
	}
	
	dump ("cursor selection " + (new Date() - startTimer)/1000 + " sec\n");
	
	bxe_history_snapshot();
	dump ("history snapshot " + (new Date() - startTimer)/1000 + " sec\n");
}

function bxe_getXMLNodeByHTMLNode(node) {

	return node.XMLNode._node;

}

function bxe_getBxeId(node) {
	return node.getAttribute("__bxe_id");
}

function bxe_getHTMLNodeByBxeId(bxe_id) {
	return document.evaluate("//*[@__bxe_id = '" + bxe_id + "']", document, null, XPathResult.ANY_UNORDERED_NODE_TYPE,null).singleNodeValue;
}

function bxe_getChildPosition(node) {
	if (!node) {
		return 0;
	}
	if (node._childPosition) {
		return node._childPosition;
	}
	var z = 0;
	var textNode = node.previousSibling;
	while (textNode) {
		textNode = textNode.previousSibling;
		z++;
	}
	node._childPosition = z;
	return z;
}

function bxe_init_htmldocument() {
	//	if (bxe_config.options['serverIncludes']) {
			
	//	}
		// init root 
		var nodes = bxe_getAll_bxeId();
	 	for (var i in nodes) {
			var node = nodes[i];
			var  _existingNode =  bxe_xml_nodes[node.getAttribute("__bxe_id")];
			if (_existingNode) {
				if (node.hasAttribute("__bxe_attribute")) {
					node.XMLNode = _existingNode.getAttributeNode(node.getAttribute("__bxe_attribute")).getXMLNode();
				} else {
					node.XMLNode = _existingNode.getXMLNode();
				}
				node.XMLNode._htmlnode = node;
				if (node.XMLNode.vdom && node.XMLNode.vdom.bxeNoteditable) {
					node.removeAttribute("__bxe_id");
				} else {
					var _child = node.firstChild;
					var _z = 0;
					while (_child) {
						if (_child.nodeType == 3) {
							if (_existingNode.childNodes.item(_z)) {
								_child.XMLNode = _existingNode.childNodes.item(_z).getXMLNode();
							}
						}
						_z++;
						_child = _child.nextSibling;
					}
					node.addEventListener("click",MouseClickEvent,false);
					
				}
				
			}
			
			
		}
}

function bxe_init_serverIncludesCallback(e) {
		if (e.document.documentElement.localName == "html4") {
			bxe_config.serverIncludes[e.td.url] = e.document.documentElement.firstChild.nodeValue;
		} else {
			bxe_config.serverIncludes[e.td.url] = e.document.documentElement.saveXML();
		}
		  
		  bxe_init_serverIncludesReplaceNode(e.td.url, e.td.htmlNode);
}



function bxe_init_serverIncludes(ctx) { 
	var includeName = bxe_config.options['serverIncludeElement'];
	if (!includeName) {
		return false;
	}
	var includeFunction = bxe_config.options['serverIncludeFunction'];
	if (!bxe_config.serverIncludes) {
		bxe_config.serverIncludes = new Array();
	}
	var res = document.evaluate("//"+ includeName, ctx,  null,     XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);
	for (var i = 0; i < res.snapshotLength; i++) {
		var node = res.snapshotItem(i);
		var url = eval(includeFunction + "(node)");
		if (!bxe_config.serverIncludes[url]) {
			var td = new mozileTransportDriver("http");
			td.htmlNode = node;
			td.url = url;
			var req =  td.load(url, bxe_init_serverIncludesCallback, true);
		} else {
			
			bxe_init_serverIncludesReplaceNode(url,node);
		}
	}
}

function bxe_init_serverIncludesReplaceNode(url, node) {
	//var inc = document.importNode(bxe_config.serverIncludes[url], true);
	var __bxe_id = node.getAttribute("__bxe_id");
	divnode = document.createElement("div");
	
	divnode.setAttribute("__bxe_id",__bxe_id);
	divnode.innerHTML = bxe_config.serverIncludes[url];
	
	//node.parentNode.removeChildOnly(node);
	var  _existingNode =  bxe_xml_nodes[__bxe_id];
	if (_existingNode) {
		divnode.XMLNode = _existingNode.getXMLNode();
		divnode.XMLNode._htmlnode = divnode;
	}
	
	node.parentNode.replaceChild(divnode,node);
	divnode.addEventListener("click",MouseClickEvent,false);
	
}


	
function bxe_createXMLNode(namespaceURI,localName) {
		
		var _new = bxe_config.xmldoc.createElementNS(namespaceURI,localName);
		
		_new.XMLNode = _new.getXMLNode();
		return _new.XMLNode;
}

function bxe_splitAtSelection(node) {
	
	var sel= window.getSelection();
	var cssr = sel.getEditableRange();
	var xmlnode = bxe_getXMLNodeByHTMLNode(cssr.startContainer.parentNode);
	xmlnode.betterNormalize();
	var _position = bxe_getChildPosition(cssr.startContainer);
	xmlnode.childNodes[_position].splitText(cssr.startOffset);
	if (xmlnode.childNodes[_position + 1].nodeValue == '') {
		xmlnode.appendChild(bxe_config.xmldoc.createTextNode(STRING_NBSP));
	}
	xmlnode.split(_position+1);
	if (xmlnode.nextSibling) {
		xmlnode.nextSibling.removeAttribute("__bxe_id");
		xmlnode.nextSibling.setBxeId();
	}
	if (node) {
		dump(xmlnode.localName + "\n");
		while (xmlnode && xmlnode != node) {
			_position = bxe_getChildPosition(xmlnode);
			dump(_position + "\n");
			dump(xmlnode.localName + "\n");
			xmlnode = xmlnode.parentNode
			xmlnode.split(_position + 1);
			if (xmlnode.nextSibling) {
				xmlnode.nextSibling.removeAttribute("__bxe_id");
				xmlnode.nextSibling.setBxeId();
			}
		}
	} 
	return xmlnode;
}

function bxe_insertAttributeValue(value) {
	window.bxe_lastAttributeNode.value = value;
}

