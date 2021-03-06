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
// $Id$

function Widget () {}

Widget.prototype.position = function (left, top, position) {
	if (position) {
		this.node.style.position = position;
	}
	this.node.style.left = left + "px";
	this.node.style.top = top + "px";
	

}



Widget.prototype.initNode = function (elementName,className, id) {
	 
	var node = document.createElement(elementName);
	node.setAttribute("class",className);
	if (id) {
		node.setAttribute("id",id);
	}
	node.style.display = "none";
	node.Widget = this;
	this.Display = "inline";
	return node;
}
Widget.prototype.draw = function (display) {
	if (display) {
		this.node.style.display = display;
	} else {
		this.node.style.display = this.Display;
	}
	this.fixOffscreenPosition();
	
}


Widget.prototype.fixOffscreenPosition = function() {
	var bottom = (this.node.offsetTop + this.node.offsetHeight);
	if (bottom > (window.innerHeight  + window.scrollY)) {
			this.node.style.top = (this.node.offsetTop - (bottom - (window.innerHeight + window.scrollY))) + "px";
	}
	var right = (this.node.offsetLeft + this.node.offsetWidth);
	if (right > (window.innerWidth  + window.scrollX)) {
			this.node.style.left = (this.node.offsetLeft - (right - (window.innerWidth + window.scrollX))) + "px";
	}

	
}

Widget.prototype.hide = function () {
	this.node.style.display = 'none';
}


Widget_AreaInfo.prototype = new Widget();

function Widget_AreaInfo (areaNode) {
	dump("JJJJJJ\n");
	/*
	this.node = this.initNode("span","AreaInfoPointer");

	var img = this.node.appendChild(document.createElement("img"));
	img.src = mozile_root_dir + "images/triangle.png";
	if (areaNode.display == "block") {
		this.Display = 'block';
	} else {
		this.Display = 'inline';
	}
	this.node.style.position = 'relative';
	this.node.style.width = "0px";
	this.node.style.height = "0px";
	areaNode.parentNode.insertBefore(this.node,areaNode);
	this.MenuPopup = new Widget_MenuPopup("XPath not defined yet");
	var doo = this.MenuPopup.addMenuItem("View",null);
	var submenu = new Widget_MenuPopup();
	this.NormalModeMenu = submenu.addMenuItem("Normal",function(e) {eDOMEventCall("toggleNormalMode",e.target.Widget.AreaNode )});
	this.NormalModeMenu.AreaNode = areaNode;
	this.NormalModeMenu.Checked = true;
	this.TagModeMenu = submenu.addMenuItem("Tag", function(e) {eDOMEventCall("toggleTagMode",e.target.Widget.AreaNode )});
	this.TagModeMenu.AreaNode = areaNode;
	this.SourceModeMenu = submenu.addMenuItem("Source",function(e) {eDOMEventCall("toggleSourceMode",e.target.Widget.AreaNode )});
	this.SourceModeMenu.AreaNode = areaNode;
	doo.addMenu(submenu);
	this.node.addEventListener("contextmenu" , Widget_AreaInfo_eventHandler, false);
	this.node.addEventListener("click" , Widget_AreaInfo_eventHandler, false);
	*/
}


	

function Widget_AreaInfo_eventHandler(e) {
	this.Widget.MenuPopup.position(e.pageX,e.pageY,"absolute");
	this.Widget.MenuPopup.draw();
	//this.Widget.MnuPopup.MenuItems[0].Label = areaNode.XMLNode._xmlnode.getXPathString();
	e.preventDefault(); 
	e.stopPropagation();
}


function Widget_MenuPopup(title) {
	this.node = this.initNode("div","MenuPopup");
	if (title) {
		this.initTitle(title)
	}
	document.getElementsByTagName("body")[0].appendChild(this.node);
	this.Display = "block";
	this.MenuItems = new Array();
}

Widget_MenuPopup.prototype = new Widget();

Widget_MenuPopup.prototype.initTitle = function (title) {
	var titleNode = this.node.insertBefore(document.createElement("div"),this.node.firstChild);
	titleNode.setAttribute("class","MenuPopupTitle");
	titleNode.appendChild(document.createTextNode(title));
	this.titleSet = true;
}
Widget_MenuPopup.prototype.setTitle = function (title) {
	if (!this.titleSet) {
		this.initTitle(title);
	} else {
		this.node.firstChild.firstChild.data = title;
	}
}
	

Widget_MenuPopup.prototype.draw = function() {
	
	var glob = mozilla.getWidgetGlobals();
	glob.addHideOnClick(this);
	this.node.style.display = this.Display;
	this.fixOffscreenPosition()


}

Widget_MenuPopup.prototype.addSeparator = function (title) {
	if (!title) {title = ""}
	//don't do a separator, if there are no elements in the popup
	if (this.MenuItems.length > 0) {
		// don't add a menusitemseparator if "preVsibling" is already one
		if (this.node.lastChild.getAttribute("class")!= "MenuItemSeparator") {
		
			var sep  = document.createElement("div");
			sep.setAttribute("class","MenuItemSeparator");
			sep.appendChild(document.createTextNode("--"+title+"--"));
		
			this.node.appendChild(sep);
		}
	}
}

Widget_MenuPopup.prototype.addMenuItem = function 	(label,action,helptext) {
	var menuitem = new Widget_MenuItem(label,action,helptext);
	menuitem.MenuPopup = this;
	this.MenuItems.push(menuitem);
	this.node.appendChild(menuitem.node);
	return menuitem;
}

Widget_MenuPopup.prototype.removeAllMenuItems = function () {
	this.titleSet = false;
	this.node.removeAllChildren();
	this.MenuItems = new Array();
}

function Widget_MenuItem(label, action, helptext) {
	this.node = this.initNode("div","MenuItem");
	this.node.style.display = "block";
	if (helptext) {
		this.node.setAttribute("title", helptext);
	}

	this.node.appendChild(document.createTextNode(label));
	this.node.onclick = action;
	this.node.Action = action;
}

Widget_MenuItem.prototype = new Widget();

Widget_MenuItem.prototype.__defineSetter__(
	"Label",
	function(label)
	{
		this.node.firstChild.nodeValue = label;
	}
);

Widget_MenuItem.prototype.__defineGetter__(
	"Label",
	function()
	{
		return this.node.firstChild.nodeValue;
	}
);

Widget_MenuItem.prototype.__defineSetter__(
	"Disabled",
	function(disabled)
	{
		if (disabled) {
			this.node.onclick = null;
			this.node.setAttribute("class","MenuItemDisabled");
		} else {
			this.node.onclick = this.node.Action;
			this.node.setAttribute("class","MenuItem");
		}
	}
);

Widget_MenuItem.prototype.__defineGetter__(
	"Disabled",
	function()
	{ 
		if (this.node.GetAttribute("class") == "MenuItemDisabled") {
			return true;
		} else {
			return false;
		}
	}
);

Widget_MenuItem.prototype.__defineSetter__(
	"Checked",
	function(checked)
	{
		if (checked) {
			this.node.setAttribute("class","MenuItemChecked");
		} else {
			this.node.setAttribute("class","MenuItem");
		}
	}
);

Widget_MenuItem.prototype.__defineGetter__(
	"Checked",
	function()
	{ 
		if (this.node.GetAttribute("class") == "MenuItemChecked") {
			return true;
		} else {
			return false;
		}
	}
);

Widget_MenuItem.prototype.addMenu = function (menu,onmouseover) {
	var img = this.node.appendChild(document.createElement("img"));
	img.src = mozile_root_dir+ "images/triangle.png";
	img.setAttribute("align","right");
	this.node.insertBefore(img,this.node.firstChild);
	this.SubMenu = menu;
	if (onmouseover) {
		this.node.addEventListener("mouseover",onmouseover, false);
	}

	this.node.addEventListener("mouseover",Widget_MenuItem_showSubmenu, false);
	this.node.addEventListener("mouseout",Widget_MenuItem_hideSubmenu, false);
		
}

function Widget_MenuItem_showSubmenu(e) {
	var widget = this.Widget;
	//this.position
	var offset = 0;
	if (widget.SubMenu.titleSet) {
		offset = -14;
	}
	widget.SubMenu.position(widget.node.offsetParent.offsetLeft + widget.node.offsetLeft + (widget.node.offsetWidth/2) , widget.node.offsetParent.offsetTop +widget.node.offsetTop + offset  ,"absolute");
	
	//widget.SubMenu.position(e.pageX +20, widget.node.offsetParent.offsetTop +widget.node.offsetTop    ,"absolute");

	widget.SubMenu.draw();
	if (widget.SubMenu.OpenSubMenu) {
		widget.SubMenu.OpenSubMenu.hide();
	}
	if (widget.MenuPopup.OpenSubMenu && widget.MenuPopup.OpenSubMenu  != widget.SubMenu) {
		widget.MenuPopup.OpenSubMenu.hide();
	}
	widget.MenuPopup.OpenSubMenu = widget.SubMenu;
	
}

function Widget_MenuItem_hideSubmenu() {
	//this.Widget.SubMenu.hide();
	
		
	
}

function Widget_Globals () {
	this.EditAttributes = new Widget_ModalAttributeBox();

}



Widget_Globals.prototype.addHideOnClick = function (widget,id) {
	if (!this.HideOnClick) {
		this.HideOnClick = new Array();
	}
	/*
	if (id) {
		this.HideOnClick.push(id);
	} else {
		this.HideOnClick.push("0");
	}*/
	this.HideOnClick.push(widget);
	
	document.addEventListener("click", Widget_Globals_doHideOnClick , true);
	document.addEventListener("contextmenu", Widget_Globals_doHideOnClick ,true);
}



Widget_Globals_doHideOnClick = function(e) {
	var glob = mozilla.getWidgetGlobals();
	document.removeEventListener("click", Widget_Globals_doHideOnClick ,false);
	document.removeEventListener("contextmenu", Widget_Globals_doHideOnClick ,true);
	if( glob.HideOnClick && glob.HideOnClick.length > 0) {
		var newHideOnClick = new Array();
		var widget = null;
		while (widget = glob.HideOnClick.pop()) 
		{ 
			widget.hide();
		}
		glob.HideOnClick = newHideOnClick;
	}
}


function Widget_MenuBar()  {
	this.node = this.initNode("div","MenuBar");
	document.getElementsByTagName("body")[0].appendChild(this.node);
	this.position(0,0,"fixed");
	this.draw();
}

Widget_MenuBar.prototype = new Widget();

Widget_MenuBar.prototype.addMenu = function (label, submenu) {
	var menu = new Widget_Menu(label);
	this.node.appendChild(menu.node);
	menu.draw();
	menu.addMenuPopup(submenu);
	

}

function Widget_Menu (label, submenu) {
	this.node = this.initNode("span","Menu");
	this.node.appendChild(document.createTextNode(label));
}

Widget_Menu.prototype = new Widget();

Widget_Menu.prototype.addMenuPopup = function(submenus) {
	var submenu = new Widget_MenuPopup();
	if (submenus) {
		var label = null;
		while (label = submenus.shift() ) {
			submenu.addMenuItem(label, submenus.shift());
		}
	}
	submenu.position(this.node.offsetLeft + 5, this.node.offsetTop + this.node.offsetHeight   ,"fixed");
	this.MenuPopup = submenu;
	this.node.addEventListener("click", function(e) {
		this.Widget.MenuPopup.draw();
		var glob = mozilla.getWidgetGlobals();
	}, false );
}
//widgets global holder

Moz.prototype.getWidgetGlobals = function () {
	if (this.WidgetGlobals) {
		return this.WidgetGlobals;
	}
	this.WidgetGlobals = new Widget_Globals();
	return this.WidgetGlobals;
}

Moz.prototype.getWidgetModalBox = function (title, callback) {
	if (this.ModalBox) {
		this.ModalBox.reset(title,callback);
	} else {
		this.ModalBox = new Widget_ModalBox(title,callback);
	}
	return this.ModalBox;
}

function Widget_ToolBar () {
 this.node = this.initNode("div","ToolBar");
 var table = document.createElement("table");

 this.TableRow =table.appendChild(document.createElement("tr"));
 this.node.appendChild(table);
 this.Display = "block";
 this.node.appendToBody();
 
}

Widget_ToolBar.prototype = new Widget();

Widget_ToolBar.prototype.addButtons = function ( buttons) {
	for (but in buttons) {
		if (but != "Dimension"  && but != "_location") {
			var button = new Widget_ToolBarButton(but,buttons[but]['ns']);
			this.addItem(button);	
		}
	}
	
	
}
Widget_ToolBar.prototype.addItem = function(item) {
	var td = document.createElement("td");
	td.appendChild(item.node);
	this.TableRow.appendChild(td);
	item.draw();
	}
	
	

function Widget_MenuList(id, event) {
	this.node= document.createElement("select");
	this.node.setAttribute("class","MenuList");
	if (event) {
		this.node.addEventListener("change", event, false);
	}
	this.Display="block";
}

Widget_MenuList.prototype = new Widget();

Widget_MenuList.prototype.removeAllItems = function() {
	this.node.options.length=0;
	//this.node.removeAllChildren();
}
Widget_MenuList.prototype.appendItem = function(label, value) {
	var option = new Option(label,value);
	this.node.options[this.node.options.length] = option;
	return option;
}
	

function Widget_ToolBarButton (id,namespaceURI) {
	this.node = this.initNode("div","ToolBarButton",id);
	this.node.setAttribute("title",id);
	this.Display = "block";
	var buttons = bxe_config.getButtons();
	var col =  buttons[id]['col'];
	var row =  buttons[id]['row'];
	
		
	var clipoffset = 
	    [buttons['Dimension'][2]*col, // left
	     buttons['Dimension'][3]*row]; //top

	this.node.style.setProperty("background-image","url("+buttonImgLoc+")","");
	this.node.style.setProperty("background-position","-"+clipoffset[0]+"px -"+clipoffset[1]+"px","");
	this.node.addEventListener("mousedown",function(e) {this.style.border="solid 1px"}, false);
	this.node.addEventListener("mouseup",function(e) {this.style.border="dotted 1px"}, false);
	this.node.addEventListener("mouseout",function(e) {this.style.border="dotted 1px #C0C0C0"}, false);
	this.node.addEventListener("mouseover",function(e) {this.style.border="dotted 1px"}, false);
	this.node.ElementNamespaceURI = namespaceURI;
	if (buttons[id]['type'] == "function") {
		this.node.addEventListener("click", function(e) { eval(buttons[id]['data']+"(e)") }, false);
	} else if (buttons[id]['type'] == "insertElement" || buttons[id]['type'] == "InsertElement") {
			this.node.addEventListener("click",function(e) { var sel = window.getSelection();
			if (bxe_checkForSourceMode(sel)) {
				return false;
			}
			var object = bxe_Node_createNS(1, e.target.ElementNamespaceURI, buttons[id]['data']);
			sel.insertNode(object);}, false);
	} else if (buttons[id]['type'] == "event") {
		this.node.addEventListener("click",function(e) { 
			eDOMEventCall(buttons[id]['data'],document,{"localName":this.getAttribute("title"),"namespaceURI":e.target.ElementNamespaceURI})}, 
		false);
	} else {
	this.node.addEventListener("click",function(e) { 
	eDOMEventCall(buttons[id]['action'],document,{"localName":this.getAttribute("title"),"namespaceURI":e.target.ElementNamespaceURI})}, 
		false);
	}
}	

Widget_ToolBarButton.prototype = new Widget();

Element.prototype.appendToBody = function() {
	document.getElementsByTagName("body")[0].appendChild(this);
}

function Widget_AboutBox( ) {
	var width = "400";
	var height = "180";
	this.node = this.initNode("div","AboutBox");
	this.Display = "block";
	this.node.appendToBody();
	this.node.style.width = width + "px";
	//this.node.style.height = height + "px";
	this.position((window.innerWidth- width)/2,(window.innerHeight-height)/3,"fixed");
	this.node.onclick = function(e) { this.style.display = 'none';}
	var htmltext = "<a href='http://bitfluxeditor.org' target='_new'>http://bitfluxeditor.org</a> <br/> Version: " + BXE_VERSION + "/" + BXE_BUILD + "/" + BXE_REVISION;
	htmltext += '<br/><br/>';
	htmltext += "<table>";
	htmltext += "<tr><td>Credits:</td></tr>";
	htmltext += '<tr><td><a href="http://bitflux.ch">Bitflux GmbH</a> </td><td> (Main Development) </td></tr>';
	htmltext += '<tr><td><a href="http://playsophy.com">Playsophy</a> </td><td> (<a href="http://mozile.mozdev.org">Mozile/eDOM</a> Development) </td></tr>';
	htmltext += '<tr><td><a href="http://twingle.mozdev.org">Twingle</a>/Stephan Richter &nbsp;</td><td> (jsdav.js library) </td></tr>';
	htmltext += '<tr><td><a href="http://kupu.oscom.org">Kupu</a> &nbsp;</td><td> (ImageDrawer) </td></tr>';
	
	htmltext += '<tr id="okButton" style="display: none" ><td> </td><td><p/><input type="submit" value="OK"/></td></tr>';
	htmltext += '<tr ><td colspan="2" id="AboutBoxScroller" > </td></tr>';

	htmltext += '</table>';
	
	var abouttext = this.node.innerHTML = htmltext;
	//var textdiv = document.createElement("div");
	var textdiv = document.getElementById("AboutBoxScroller")
	this.TextNode = document.getElementById("AboutBoxScroller").firstChild;

	textdiv.style.paddingTop = "20px";
	
	
}
Widget_AboutBox.prototype = new Widget();
Widget_AboutBox.prototype.show = function (okButton, showSplash) {
	this.showSplash = showSplash;
	if (showSplash != "false") {
		this.node.style.overflow = "visible";
		this.node.style.MozOpacity = 1;
		if (okButton) { 
			document.getElementById('okButton').style.display = "table-row";
		}
		this.draw();
	}
}



Widget_AboutBox.prototype.setText = function(text) {
	if (text == "") {
		this.TextNode.parentNode.parentNode.style.display = "none";
	}
	this.TextNode.data = text;
}

Widget_AboutBox.prototype.addText = function(text) {
	this.TextNode.data =this.TextNode.data + " " + text;
		if (this.showSplash != "false") {
		if ( this.TextNode.data.length  > 120) {
			this.TextNode.data = "..." + this.TextNode.data.substr(this.TextNode.data.length - 120);
		}
		dump(text + "\n");
	}
	window.status = this.TextNode.data;
}

function Widget_StatusBar_Message (statusbarNode) {
	this.statusbarNode = statusbarNode
}
	
Widget_StatusBar_Message.prototype = new Widget();

Widget_StatusBar_Message.prototype.showMessage = function(text) {
	if (!(this.node && this.node.parentNode )) {
		this.node= this.initNode("div","StatusBarMessage","StatusBarMessage");
		this.statusbarNode.appendChild(this.node);
	}
	this.node.removeAllChildren();
	this.node.style.display="inline";
	this.node.appendChild(document.createTextNode(text));
}
function Widget_StatusBar () {
	
	this.node = this.initNode("div","StatusBar","StatusBar");
	this.node.appendToBody();
	this.positionize();
	window.onresize = this.positionize;
	this.Display  = "block";
	this.subPopup = new Widget_MenuPopup();
	this.buildXPath(bxe_config.xmldoc.documentElement);
	
	this.draw();	
}

Widget_StatusBar.prototype = new Widget();

Widget_StatusBar.prototype.showMessage = function (text) {
	if (!this.messageArea) {
		this.messageArea = new Widget_StatusBar_Message(this.node);
	} 
	this.messageArea.showMessage(text);
	
}
	

Widget_StatusBar.prototype.positionize = function (e) {
	// it's an event, do nothing...
	if (e) {
		
	} else {
		target = this;
	}
	target.position(0,document.documentElement.clientHeight - 35,"fixed");
	this.Popup = new Widget_MenuPopup();
}

Widget_StatusBar.prototype.buildXPath = function (node) {
	if (!node) {
		this.node.removeAllChildren();
		return true;
	}
	if (node.nodeType == Node.TEXT_NODE) {
		node = node.parentNode;
	} 
	
	if (this.lastDefaultContent != node && node.getAttribute("__bxe_defaultcontent") == "true" ) {
		if (node.edited) {
			node.removeAttribute("__bxe_defaultcontent");
			node.XMLNode._node.removeAttribute("__bxe_defaultcontent");
			this.lastDefaultContent = false;
		} else {
			var sel = window.getSelection();
			
			sel.collapse(node.firstChild,0);
			sel.extend(node.firstChild,node.firstChild.length);
			this.lastDefaultContent = node;
		}
		
		
	} else {
		this.lastDefaultContent = false;
	}
	
	node = node.XMLNode;
	if (node && node.nodeType == Node.ATTRIBUTE_NODE) {
		node = node.parentNode;
	}
	this.node.removeAllChildren();
	this.Popup.position(0,0,"absolute");
	this.Popup.StatusBar = this;
	/*dump (node._node.getAttribute("__bxe_id") + "\n");
	dump (node + "\n");
	*/
	while(node && node.nodeType == 1) {
	
		var rootNode = document.createElement("span");
		try {
			rootNode.appendChild(document.createTextNode(node.vdom.bxeName.replace(/ /g,STRING_NBSP)));
		} catch(e) { 
			rootNode.appendChild(document.createTextNode(node.localName));
		}
		this.node.insertBefore(rootNode,this.node.firstChild);
		if (node._node) {
			rootNode._node = node._node;
			rootNode.addEventListener("mouseover",Widget_XPathMouseOver,false);
			rootNode.addEventListener("mouseout",Widget_XPathMouseOut,false);
		}
		rootNode.Widget = this;
		rootNode.addEventListener("click", function(e) {
			this.Widget.buildPopup(this);
		}, false );
		rootNode.XMLNode = node;
		
		node = node.parentNode;
		
	}

}

Widget_StatusBar.prototype.buildPopup = function (node) {
		this.Popup.removeAllMenuItems();
		this.Popup.initTitle(node.XMLNode.vdom.bxeName);

		eDOMEventCall("ContextPopup",node,this.Popup);
		
		this.Popup.addSeparator(" Append ");
		this.Popup.appendAllowedSiblings(node,this.subPopup);
		
		this.Popup.draw();
		this.Popup.position(node.offsetParent.offsetLeft +node.offsetLeft,  node.offsetParent.offsetTop + node.offsetTop -this.Popup.node.offsetHeight ,"fixed");
		
		this.Popup.draw();
		this.Popup._node = node;
}

function Widget_ContextMenu () {
	this.Popup = new Widget_MenuPopup();
	this.Popup.position(0,0,"absolute");
	this.Popup.ContextMenu = this;
	this.subPopup = new Widget_MenuPopup();
	this.subPopup.subPopup = new Widget_MenuPopup();
	//this.Popup.EditAttributes = new Widget_ModalAttributeBox();
	
}

Widget_ContextMenu.prototype = new Widget();

Widget_ContextMenu.prototype.show = function(e,node) {
	this.buildPopup(e,node);
}

Widget_ContextMenu.prototype.buildElementChooserPopup = function (node, ac ) {
	this.Popup.removeAllMenuItems();
	this.Popup.initTitle("Choose Subelement of " + node.vdom.bxeName);
	ac.sort(bxe_nodeSort);
	for (i = 0; i < ac.length; i++) {
				if (ac[i].nodeType != 3 && !(ac[i].vdom.bxeDontshow) &&  !bxe_config.dontShowInContext[ac[i].namespaceURI + ":" +ac[i].localName] &&  ac[i].vdom.canHaveChildren ) {
					var menui =this.Popup.addMenuItem( ac[i].vdom.bxeName, function(e) { 
						var widget = e.currentTarget.Widget;
						eDOMEventCall("appendChildNode",document,{"appendToNode": widget.AppendToNode, "localName":widget.InsertLocalName,"namespaceURI":widget.InsertNamespaceURI});
					});
					menui.InsertLocalName = ac[i].localName;
					menui.InsertNamespaceURI = ac[i].namespaceURI;
					menui.AppendToNode = node;
				}
			}
	this.Popup.draw();
	this.Popup._node = node;

}

Widget_ContextMenu.prototype.buildPopup = function (e,node) {
		
	
	this.Popup.removeAllMenuItems();
	if (node.XMLNode.nodeType == Node.ATTRIBUTE_NODE) {
		var nodeX = node.XMLNode.parentNode;
	} else {
		var nodeX = node.XMLNode;
	}
	this.Popup.initTitle(nodeX.vdom.bxeName);
	/* currently not working */
	/*if (node.XMLNode.vdom.hasAttributes && this.Popup.EditAttributes) {
		var menui = this.Popup.addMenuItem("Edit Attributes..", this.Popup.EditAttributes.popup);
		menui.Modal = this.Popup.EditAttributes;
		menui.MenuPopup._node = node;
	}*/
	eDOMEventCall("ContextPopup", node, this.Popup);
	var sel  = window.getSelection();
	var cssr = sel.getEditableRange();
	//var ip = documentCreateInsertionPoint(cssr.top, cssr.startContainer, cssr.startOffset);
	var selNode = cssr.startContainer.XMLNode;
	if (!(sel.isCollapsed) && node.getAttribute("__bxe_defaultcontent") != "true") {
			var ac = nodeX.allowedChildren;
			
			for (i = 0; i < ac.length; i++) {
				if (ac[i].nodeType != 3) {
				if (!(ac[i].vdom.bxeDontshow) && !bxe_config.dontShowInContext[ac[i].namespaceURI + ":" +ac[i].localName] &&  ac[i].vdom.canHaveChildren ) {
					var menui =this.Popup.addMenuItem( ac[i].vdom.bxeName, function(e) { 
						var widget = e.currentTarget.Widget;
						var sel = window.getSelection();
						sel.removeAllRanges();
						var rng = widget.Cssr.cloneRange();
						sel.addRange(rng);
						eDOMEventCall("toggleTextClass",document,{"localName":widget.InsertLocalName,"namespaceURI":widget.InsertNamespaceURI})
					});
					menui.InsertLocalName = ac[i].localName;
					menui.InsertNamespaceURI = ac[i].namespaceURI;
					menui.AppendToNode = nodeX;
					menui.Cssr = cssr;
				}
				}
			}
	} 
	
	this.Popup._node = node;
	this.Popup.addSeparator(" Append to ");
	node = nodeX;
	
	//parent nodes
	while(node && node.nodeType == 1) {
		
		//if (node.isInHTMLDocument && node.isInHTMLDocument() && node._node != cssr.top) {
			var ele = this.Popup.addMenuItem(node.vdom.bxeName);
			ele.node.XMLNode = node;
			ele.node.addEventListener("mouseover",Widget_XPathMouseOver,false);
			ele.node.addEventListener("mouseout",Widget_XPathMouseOut,false);
			ele.AppendToNode = node;
			ele.SubPopup = this.subPopup;
			ele.addMenu(this.subPopup,function(e) {
				var sub = e.currentTarget.Widget.SubPopup;
				sub.removeAllMenuItems();
				sub.setTitle("Append");
				var newNode = e.currentTarget.Widget.AppendToNode._node;
				/*if (newNode.XMLNode.vdom.hasAttributes && e.currentTarget.Widget.EditAttribute) {
						var menui = sub.addMenuItem("Edit Attributes..", sub.EditAttributes.popup);
						menui.Modal = e.currentTarget.Widget.EditAttributes;
						menui.MenuPopup._node = newNode;
				}*/
				eDOMEventCall("ContextPopup",newNode,sub);
				sub.appendAllowedSiblings(e.currentTarget.Widget.AppendToNode._node,sub.subPopup);
				
			}
			);
			
		//} 
		
		node = node.parentNode;
	} 
	
	
	this.Popup.position(e.pageX, e.pageY, "absolute");
	this.Popup.draw();
	this.Popup._node = node;
}



Widget_MenuPopup.prototype.appendAllowedSiblings = function( node, parentSub) {
	var ac = node.XMLNode.allowedNextSiblingsSorted;
	
	for (i = 0; i < ac.length; i++) {
		if (ac[i].nodeType != 3 &&  !ac[i].vdom.bxeDontshow   ) { // && !bxe_config.dontShowInContext[ac[i].namespaceURI + ":" +ac[i].localName]
				if (i == 0 || ac[i].vdom != ac[i-1].vdom) {
				var menui = this.addMenuItem(ac[i].vdom.bxeName.replace(/ /g,STRING_NBSP), function(e) { 
					var widget = e.currentTarget.Widget;
					eDOMEventCall("appendNode",document,{"appendToNode": widget.AppendToNode, "localName":widget.InsertLocalName,"namespaceURI":widget.InsertNamespaceURI})
				
				});
				menui.InsertLocalName = ac[i].localName;
				menui.InsertNamespaceURI = ac[i].namespaceURI;
				menui.AppendToNode = node.XMLNode;
				}
		}
	}
	var ac = node.XMLNode.allowedChildren;

	if (ac.length > 1 || (ac.length == 1 && ac[0].nodeType != 3) ) {
	
	this.addSeparator();
	var ele = this.addMenuItem("Insert ...");
	ele.node.XMLNode = node;
	ele.node.addEventListener("mouseover",Widget_XPathMouseOver,false);
	ele.node.addEventListener("mouseout",Widget_XPathMouseOut,false);
	ele.AppendToNode = node;
	ele.SubPopup = parentSub;
	ele.addMenu(parentSub, function (e) {
		var widget = e.currentTarget.Widget;
		var sub = widget.SubPopup;
		sub.removeAllMenuItems();
		sub.setTitle("Insert");
		sub.appendAllowedChildren(widget.AppendToNode);
	});
	}
	
}


Widget_MenuPopup.prototype.appendAllowedChildren = function( node) {
	var ac = node.XMLNode.allowedChildren;
	function nodeSort(a,b) {
		if (a.nodeName > b.nodeName) {
			return 1;
		} else {
			return -1;
		}
	}
	
	ac.sort(nodeSort);
	var _helptext = false;
	for (i = 0; i < ac.length; i++) {
		if (ac[i].nodeType != 3 && !ac[i].vdom.bxeDontshow ) { // && !bxe_config.dontShowInContext[ac[i].namespaceURI + ":" +ac[i].localName] ) {
				
				if (i == 0 || ac[i].vdom != ac[i-1].vdom) {
					if (ac[i].vdom.bxeHelptext) {
						_helptext = ac[i].vdom.bxeHelptext;
					} else {
						_helptext = false;
					}
					
				var menui = this.addMenuItem(ac[i].vdom.bxeName.replace(/ /g,STRING_NBSP), function(e) { 
					var widget = e.currentTarget.Widget;
					eDOMEventCall("appendChildNode",document,{"atStart": true, "appendToNode": widget.AppendToNode, "localName":widget.InsertLocalName,"namespaceURI":widget.InsertNamespaceURI})
				
				}, _helptext);
				menui.InsertLocalName = ac[i].localName;
				menui.InsertNamespaceURI = ac[i].namespaceURI;
				menui.AppendToNode = node.XMLNode;
				}
		}
		}
}


Widget_ModalBox.prototype = new Widget();

function Widget_ModalBox (title, callback) {
	this.setup(title,callback);
}

Widget_ModalBox.prototype.setup = function (title, callback ) {
	this.doCancel = false;

	this.node = this.initNode("div","ModalBox");
	this.Display = "block";
	this.node.appendToBody();
	if (title) {
		this.initTitle(title);
	}
	this.initPane();
	this.reset(title, callback);
}

Widget_ModalBox.prototype.reset = function (title, callback) {
	this.doCancel = false;
	if (this.PaneNode.hasChildNodes()) {
		this.PaneNode.removeAllChildren();
	}
	this.hasTable = false;
	this.hasForm = false;
	this.callback = callback;
	if (title) {
		this.setTitle(title);
	}
}

Widget_ModalBox.prototype.addItem = function (name, value, type, description, options, useKeyAsDisplayName) {
	this.doCancel = true;
	switch (type) {
		case "textfield":
			var td = this.addFormEntry(name, description);
			var inputfield = document.createElement("input");
			inputfield.value = value;
			inputfield.name = name;
			td.appendChild(inputfield);
		break;
		
		case "textarea":
			var td = this.addFormEntry(name, description);
			var inputfield = document.createElement("textarea");
			inputfield.setAttribute("cols","50");
			inputfield.setAttribute("rows","30");
			var text = document.createTextNode(value);
			
			inputfield.name = name;
			inputfield.appendChild(text);
			td.appendChild(inputfield);
		break;
		
		case "select":
			var td = this.addFormEntry(name, description);
			var inputfield = document.createElement("select");
			inputfield.setAttribute("name", name);
			var choosefield;
			for (var j in options) {
				choosefield = document.createElement("option");
				
				if ( useKeyAsDisplayName) {
					choosefield.appendChild(document.createTextNode(j));
				} else {
					choosefield.appendChild(document.createTextNode(options[j]));
				}
				choosefield.setAttribute("value", options[j]);
				
				if (options[j] == value) {
					choosefield.setAttribute("selected","selected");
				}
				inputfield.appendChild(choosefield);
			}
			td.appendChild(inputfield);
		break;
		case "noteditable":
			var td = this.addFormEntry(name, description);
			td.appendChild(document.createTextNode(value));
		break;
		case "selectorPopup":
		case "selectorPopupFunction":
			var td = this.addFormEntry(name, description);
			var inputfield = document.createElement("input");
			inputfield.setAttribute("disabled","disabled");
			inputfield.value = value;
			inputfield.name = name;
			td.appendChild(inputfield);
			var inputfield = document.createElement("input");
			//inputfield.appendChild (document.createTextNode("..."));
			inputfield.type = "button";
			inputfield.value = "...";
			inputfield.name = "__select" + name;
			inputfield.setAttribute("class","attributeSelector");
			if (type == "selectorPopup") {
				inputfield.setAttribute("onclick","Widget_openAttributeSelector('"+options+"',this.previousSibling)");
			} else {
				inputfield.setAttribute("onclick","Widget_openAttributeSelectorFunc('"+options+"',this.previousSibling)");
			}
			
			td.appendChild(inputfield);
		break;
		default:
			td.appendChild(document.createTextNode("type " + type + " not defined"));
	}
}

Widget_ModalBox.prototype.createTable = function() {
	if (!this.hasTable) {
		var form = document.createElement("form");
		form.Widget = this;
		var table = document.createElement("table");
		this.PaneNode.appendChild(form);
		form.appendChild(table);
		this.hasTable = table;
	}
	return this.hasTable;
}

Widget_ModalBox.prototype.addFormEntry = function(title, description) {
	var table = this.createTable();
	var tr = document.createElement("tr");
	table.appendChild(tr);
	var td = document.createElement("td");
	
	if (description && description['helptext']) {
		td.setAttribute("title",description['helptext']);
	}
	if (description && description['displayName']) {
		td.appendChild(document.createTextNode(description['displayName']));
	} else {
		td.appendChild(document.createTextNode(title));
	}
	tr.appendChild(td);
	var tdt = document.createElement("td");
	tr.appendChild(tdt);
	return tdt;
}

Widget_ModalBox.prototype.addText = function(text,html) {
	var table = this.createTable();
	var tr = document.createElement("tr");
	table.appendChild(tr);
	var td = document.createElement("td");
	td.setAttribute("colspan",2);
	if (text.length>1000) {
		text=text.substring(0,1000)+" ...";
	}
	if (html) {
		td.innerHTML = text;
	} else {
		td.appendChild(document.createTextNode(text));
	}
	
	tr.appendChild(td);
}

Widget_ModalBox.prototype.show = function(x,y, position) {
	try {
	if (this.hasTable) {
		var subm = document.createElement("input");
		subm.setAttribute("type","submit");
		subm.name="__submit";
		subm.setAttribute("value","OK");
		this.hasTable.parentNode.addEventListener("submit", function(e) {
			var Widget = e.currentTarget.Widget;
			e.preventDefault();
			Widget.hide();
			bxe_registerKeyHandlers();
			var elem = e.target.elements;
			var returnValues = new Array();
			for (var i =0; i < elem.length; i++ in elem) {
				if (elem[i].name.substr(0,2) != "__") {
					returnValues[elem[i].name.replace(/ +\*/,"")] = elem[i].value;
				}
			}
			var sel = window.getSelection();
			sel.selectEditableRange(Widget.cssr);
			if (Widget.callback) {
				Widget.callback(returnValues);
			}
			e.preventDefault();
			e.stopPropagation();
		}, false);
		if (this.doCancel) {
			var cancel = document.createElement("input");
			cancel.setAttribute("type","reset");
			cancel.setAttribute("value","Cancel");
			cancel.name="__cancel";
			this.hasTable.parentNode.addEventListener("reset", function(e) { 
				bxe_registerKeyHandlers(); 
				e.target.Widget.hide();
			}, false);
			this.hasTable.parentNode.appendChild(cancel);
		}
		this.hasTable.parentNode.appendChild(subm);
		this.cssr = window.getSelection().getEditableRange();
		subm.focus();
	}
	if (!position) { position = "absolute";};
	this.position(x,y, position);
	this.draw();
	}
	catch (e) { bxe_catch_alert(e);} 
	bxe_deregisterKeyHandlers();
}
Widget_ModalBox.prototype.initTitle = function(title) {
	var titeldiv = document.createElement("div");
	titeldiv.setAttribute("class","ModalBoxTitle");
	titeldiv.appendChild(document.createTextNode(title));
	this.node.appendChild(titeldiv);
	this.TitleNode = titeldiv;
}

Widget_ModalBox.prototype.initPane = function() {
	var panenode = document.createElement("div");
	panenode.setAttribute("class","ModalBoxPane");
	this.node.appendChild(panenode);
	this.PaneNode = panenode;
}

Widget_ModalBox.prototype.setTitle = function(text) {
	this.TitleNode.firstChild.data = text;
	
}


function Widget_ModalAttributeBox() {
	this.setup("Edit Attributes");
}

Widget_ModalAttributeBox.prototype = new Widget_ModalBox();

Widget_ModalAttributeBox.prototype.popup = function(e) {
	try {
		
	var box = mozilla.getWidgetGlobals().EditAttributes;
	var xmlnode = e.target.Widget.MenuPopup.MainNode;
	box.reset("Edit Attributes of " + xmlnode.vdom.bxeName, function(values) {
		
		this.setAttributes(values);
	}
	);
	box.RefXMLNode = xmlnode;
	box.drawAttributes(xmlnode);
	//box.show(0,0 ,"absolute");
	
	box.show(e.pageX ,e.pageY ,"absolute");
	} catch(e) {bxe_catch_alert(e);}
}

Widget_ModalAttributeBox.prototype.drawAttributes = function(xmlnode) {
	var attr = xmlnode.vdom.attributes;
	
	var text = "";
	var description = new Array();
	for (var i in attr) {
		if (!attr[i].bxeDontshow && !bxe_config.dontShowInAttributeDialog[attr[i].name]) {
			
			if (! (attr[i].name == "class" && xmlnode.getAttribute(attr[i].name) == xmlnode.localName)) {
				if (attr[i].bxeHelptext) {
					description['helptext'] = attr[i].bxeHelptext;
				} else {
					description['helptext'] = false;
				}
				if (attr[i]._bxeName) {
					description['displayName'] = attr[i]._bxeName;
				} else {
					description['displayName']= false;
				}
				
				if (attr[i].bxeNoteditable) {
					this.addItem(attr[i].name,xmlnode.getAttribute(attr[i].name),"noteditable",description);
				} else if (i == "__bxe_choices") {
					for (var j in attr[i]) {
						for (var k in attr[i][j]) {
							this.addAttributeItem(attr[i][j][k],xmlnode.getAttribute(attr[i][j][k].name),true, description);
						}
					}
			
				} else {
					this.addAttributeItem(attr[i],xmlnode.getAttribute(attr[i].name),false, description);
				}
			}
		}
		
	}
}

Widget_ModalAttributeBox.prototype.addAttributeItem = function (attr, value,choice, helptext) {
	var text = attr.name;
	if (choice) {
		text = text + "";
	}
	else if (!attr.optional) {
		text = text + " *";
	}
	if (attr.dataType == "choice") {
		this.addItem(text,value,"select",null,attr.choices);
		
	} else if (attr.bxeSelector) {
		if (attr.bxeSelectorType == "popup") {
			this.addItem(text,value,"selectorPopup", helptext, attr.bxeSelector );
			/*var pop = window.open(attr[i].bxeSelector,"popup","width=600,height=600,resizable=yes,scrollbars=yes");
			pop.focus();*/
		} else {
			this.addItem(text,value,"selectorPopupFunction", helptext, attr.bxeSelector );
		}
	} else {
		this.addItem(text,value,"textfield", helptext);
	}
	
}

Widget_ModalAttributeBox.prototype.setAttributes = function(values) {
	var xmlnode = this.RefXMLNode;
	try {
	for (var attrName in values) {
		attrValue = values[attrName];
		if (attrValue) {
			xmlnode.setAttribute(attrName, attrValue);
		} else {
			xmlnode.removeAttribute(attrName);
		}
	}
	} catch (e) { bxe_catch_alert(e);}
	bxe_Transform(false,false,xmlnode);
}

function Widget_XPathMouseOver (e) {	//dump(e.currentTarget.XMLNode._htmlnode.getAttribute("__bxe_id") + "\n");;
	var _h = e.currentTarget.XMLNode._htmlnode;
	if (_h) {
		_h.setAttribute("__bxe_highlight","true");
	}
}


function Widget_XPathMouseOut (e) {
	var _h = e.currentTarget.XMLNode._htmlnode;
	if (_h) {
		_h.removeAttribute("__bxe_highlight");
	}

}

function Widget_openAttributeSelector(url,inputfield) {
	window.bxe_lastAttributeNode = inputfield;
	var pop = window.open(url ,"popup","width=600,height=600,resizable=yes,scrollbars=yes");
	pop.focus();
}
function Widget_openAttributeSelectorFunc (func,inputfield) {
	window.bxe_lastAttributeNode = inputfield;
	var value =  eval(func + "(inputfield)");
	if (value) {
		inputfield.value = value;
	}
	
}