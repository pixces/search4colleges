/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.6.0
*/


/**
* @module menu
* @description <p>The Menu family of components features a collection of 
* controls that make it easy to add menus to your website or web application.  
* With the Menu Controls you can create website fly-out menus, customized 
* context menus, or application-style menu bars with just a small amount of 
* scripting.</p><p>The Menu family of controls features:</p>
* <ul>
*    <li>Keyboard and mouse navigation.</li>
*    <li>A rich event model that provides access to all of a menu's 
*    interesting moments.</li>
*    <li>Support for 
*    <a href="http://en.wikipedia.org/wiki/Progressive_Enhancement">Progressive
*    Enhancement</a>; Menus can be created from simple, 
*    semantic markup on the page or purely through JavaScript.</li>
* </ul>
* @title Menu
* @namespace YAHOO.widget
* @requires Event, Dom, Container
*/
(function () {

    var _DIV = "DIV",
    	_HD = "hd",
    	_BD = "bd",
    	_FT = "ft",
    	_LI = "LI",
    	_DISABLED = "disabled",
		_MOUSEOVER = "mouseover",
		_MOUSEOUT = "mouseout",
		_MOUSEDOWN = "mousedown",
		_MOUSEUP = "mouseup",
		_FOCUS = YAHOO.env.ua.ie ? "focusin" : "focus",		
		_CLICK = "click",
		_KEYDOWN = "keydown",
		_KEYUP = "keyup",
		_KEYPRESS = "keypress",
		_CLICK_TO_HIDE = "clicktohide",
		_POSITION = "position", 
		_DYNAMIC = "dynamic",
		_SHOW_DELAY = "showdelay",
		_SELECTED = "selected",
		_VISIBLE = "visible",
		_UL = "UL",
		_MENUMANAGER = "MenuManager",
    	
    
    	Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        Lang = YAHOO.lang;


    /**
    * Singleton that manages a collection of all menus and menu items.  Listens 
    * for DOM events at the document level and dispatches the events to the 
    * corresponding menu or menu item.
    *
    * @namespace YAHOO.widget
    * @class MenuManager
    * @static
    */
    YAHOO.widget.MenuManager = function () {
    
        // Private member variables
    
    
        // Flag indicating if the DOM event handlers have been attached
    
        var m_bInitializedEventHandlers = false,
    
    
        // Collection of menus

        m_oMenus = {},


        // Collection of visible menus
    
        m_oVisibleMenus = {},
    
    
        //  Collection of menu items 

        m_oItems = {},


        // Map of DOM event types to their equivalent CustomEvent types
        
        m_oEventTypes = {
            "click": "clickEvent",
            "mousedown": "mouseDownEvent",
            "mouseup": "mouseUpEvent",
            "mouseover": "mouseOverEvent",
            "mouseout": "mouseOutEvent",
            "keydown": "keyDownEvent",
            "keyup": "keyUpEvent",
            "keypress": "keyPressEvent",
            "focus": "focusEvent",
            "focusin": "focusEvent",
            "blur": "blurEvent",
            "focusout": "blurEvent"
        },


    	// The element in the DOM that currently has focus
    
		m_oFocusedElement = null,
    
    
        m_oFocusedMenuItem = null;
    
    
    
        // Private methods
    
    
        /**
        * @method getMenuRootElement
        * @description Finds the root DIV node of a menu or the root LI node of 
        * a menu item.
        * @private
        * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
        * level-one-html.html#ID-58190037">HTMLElement</a>} p_oElement Object 
        * specifying an HTML element.
        */
        function getMenuRootElement(p_oElement) {
        
            var oParentNode,
            	returnVal;
    
            if (p_oElement && p_oElement.tagName) {
            
                switch (p_oElement.tagName.toUpperCase()) {
                        
                case _DIV:
    
                    oParentNode = p_oElement.parentNode;
    
                    // Check if the DIV is the inner "body" node of a menu

                    if ((
                            Dom.hasClass(p_oElement, _HD) ||
                            Dom.hasClass(p_oElement, _BD) ||
                            Dom.hasClass(p_oElement, _FT)
                        ) && 
                        oParentNode && 
                        oParentNode.tagName && 
                        oParentNode.tagName.toUpperCase() == _DIV) {
                    
                        returnVal = oParentNode;
                    
                    }
                    else {
                    
                        returnVal = p_oElement;
                    
                    }
                
                    break;

                case _LI:
    
                    returnVal = p_oElement;
                    
                    break;

                default:
    
                    oParentNode = p_oElement.parentNode;
    
                    if (oParentNode) {
                    
                        returnVal = getMenuRootElement(oParentNode);
                    
                    }
                
                    break;
                
                }
    
            }
            
            return returnVal;
            
        }
    
    
    
        // Private event handlers
    
    
        /**
        * @method onDOMEvent
        * @description Generic, global event handler for all of a menu's 
        * DOM-based events.  This listens for events against the document 
        * object.  If the target of a given event is a member of a menu or 
        * menu item's DOM, the instance's corresponding Custom Event is fired.
        * @private
        * @param {Event} p_oEvent Object representing the DOM event object  
        * passed back by the event utility (YAHOO.util.Event).
        */
        function onDOMEvent(p_oEvent) {
    
            // Get the target node of the DOM event
        
            var oTarget = Event.getTarget(p_oEvent),
                
            // See if the target of the event was a menu, or a menu item
    
            oElement = getMenuRootElement(oTarget),
            sCustomEventType,
            sTagName,
            sId,
            oMenuItem,
            oMenu; 
    
    
            if (oElement) {
    
                sTagName = oElement.tagName.toUpperCase();
        
                if (sTagName == _LI) {
            
                    sId = oElement.id;
            
                    if (sId && m_oItems[sId]) {
            
                        oMenuItem = m_oItems[sId];
                        oMenu = oMenuItem.parent;
            
                    }
                
                }
                else if (sTagName == _DIV) {
                
                    if (oElement.id) {
                    
                        oMenu = m_oMenus[oElement.id];
                    
                    }
                
                }
    
            }
    
    
            if (oMenu) {
    
                sCustomEventType = m_oEventTypes[p_oEvent.type];
    
    
                // Fire the Custom Event that corresponds the current DOM event    
        
                if (oMenuItem && !oMenuItem.cfg.getProperty(_DISABLED)) {
    
                    oMenuItem[sCustomEventType].fire(p_oEvent);                   
    
                }
        
                oMenu[sCustomEventType].fire(p_oEvent, oMenuItem);
            
            }
            else if (p_oEvent.type == _MOUSEDOWN) {
    
                /*
                    If the target of the event wasn't a menu, hide all 
                    dynamically positioned menus
                */
                
                for (var i in m_oVisibleMenus) {
        
                    if (Lang.hasOwnProperty(m_oVisibleMenus, i)) {
        
                        oMenu = m_oVisibleMenus[i];

                        if (oMenu.cfg.getProperty(_CLICK_TO_HIDE) && 
                            !(oMenu instanceof YAHOO.widget.MenuBar) && 
                            oMenu.cfg.getProperty(_POSITION) == _DYNAMIC) {
        
                            oMenu.hide();
        
                        }
                        else {
                            
							if (oMenu.cfg.getProperty(_SHOW_DELAY) > 0) {
							
								oMenu._cancelShowDelay();
							
							}


							if (oMenu.activeItem) {
						
								oMenu.activeItem.blur();
								oMenu.activeItem.cfg.setProperty(_SELECTED, false);
						
								oMenu.activeItem = null;            
						
							}
        
                        }
        
                    }
        
                } 
    
            }
            else if (p_oEvent.type == _FOCUS) {
            
            	m_oFocusedElement = oTarget;
            
            }
            
        }
    
    
        /**
        * @method onMenuDestroy
        * @description "destroy" event handler for a menu.
        * @private
        * @param {String} p_sType String representing the name of the event 
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        * @param {YAHOO.widget.Menu} p_oMenu The menu that fired the event.
        */
        function onMenuDestroy(p_sType, p_aArgs, p_oMenu) {
    
            if (m_oMenus[p_oMenu.id]) {
    
                this.removeMenu(p_oMenu);
    
            }
    
        }
    
    
        /**
        * @method onMenuFocus
        * @description "focus" event handler for a MenuItem instance.
        * @private
        * @param {String} p_sType String representing the name of the event 
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */
        function onMenuFocus(p_sType, p_aArgs) {
    
            var oItem = p_aArgs[1];
    
            if (oItem) {
    
                m_oFocusedMenuItem = oItem;
            
            }
    
        }
    
    
        /**
        * @method onMenuBlur
        * @description "blur" event handler for a MenuItem instance.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */
        function onMenuBlur(p_sType, p_aArgs) {
    
            m_oFocusedMenuItem = null;
    
        }
    

        /**
        * @method onMenuHide
        * @description "hide" event handler for a Menu instance.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
		* @param <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
		* level-one-html.html#ID-58190037">p_oFocusedElement</a> The HTML element that had focus
		* prior to the Menu being made visible
        */    
    	function onMenuHide(p_sType, p_aArgs, p_oFocusedElement) {

			/*
				Restore focus to the element in the DOM that had focus prior to the Menu 
				being made visible
			*/

			if (p_oFocusedElement && p_oFocusedElement.focus) {
			
				try {
					p_oFocusedElement.focus();
				}
				catch(ex) {
				}
			
			}
			
    		this.hideEvent.unsubscribe(onMenuHide, p_oFocusedElement);
    	
    	}
    

        /**
        * @method onMenuShow
        * @description "show" event handler for a MenuItem instance.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */    	
    	function onMenuShow(p_sType, p_aArgs) {

			/*
				Dynamically positioned, root Menus focus themselves when visible, and will then, 
				when hidden, restore focus to the UI control that had focus before the Menu was 
				made visible
			*/ 

			if (this === this.getRoot() && this.cfg.getProperty(_POSITION) === _DYNAMIC) {
    	
				this.hideEvent.subscribe(onMenuHide, m_oFocusedElement);
				this.focus();
			
			}
    	
    	}    
    
    
        /**
        * @method onMenuVisibleConfigChange
        * @description Event handler for when the "visible" configuration  
        * property of a Menu instance changes.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */
        function onMenuVisibleConfigChange(p_sType, p_aArgs) {
    
            var bVisible = p_aArgs[0],
                sId = this.id;
            
            if (bVisible) {
    
                m_oVisibleMenus[sId] = this;
                
                YAHOO.log(this + " added to the collection of visible menus.", 
                	"info", _MENUMANAGER);
            
            }
            else if (m_oVisibleMenus[sId]) {
            
                delete m_oVisibleMenus[sId];
                
                YAHOO.log(this + " removed from the collection of visible menus.", 
                	"info", _MENUMANAGER);
            
            }
        
        }
    
    
        /**
        * @method onItemDestroy
        * @description "destroy" event handler for a MenuItem instance.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */
        function onItemDestroy(p_sType, p_aArgs) {
    
            removeItem(this);
    
        }


        /**
        * @method removeItem
        * @description Removes a MenuItem instance from the MenuManager's collection of MenuItems.
        * @private
        * @param {MenuItem} p_oMenuItem The MenuItem instance to be removed.
        */    
        function removeItem(p_oMenuItem) {

            var sId = p_oMenuItem.id;
    
            if (sId && m_oItems[sId]) {
    
                if (m_oFocusedMenuItem == p_oMenuItem) {
    
                    m_oFocusedMenuItem = null;
    
                }
    
                delete m_oItems[sId];
                
                p_oMenuItem.destroyEvent.unsubscribe(onItemDestroy);
    
                YAHOO.log(p_oMenuItem + " successfully unregistered.", "info", _MENUMANAGER);
    
            }

        }
    
    
        /**
        * @method onItemAdded
        * @description "itemadded" event handler for a Menu instance.
        * @private
        * @param {String} p_sType String representing the name of the event  
        * that was fired.
        * @param {Array} p_aArgs Array of arguments sent when the event 
        * was fired.
        */
        function onItemAdded(p_sType, p_aArgs) {
    
            var oItem = p_aArgs[0],
                sId;
    
            if (oItem instanceof YAHOO.widget.MenuItem) { 
    
                sId = oItem.id;
        
                if (!m_oItems[sId]) {
            
                    m_oItems[sId] = oItem;
        
                    oItem.destroyEvent.subscribe(onItemDestroy);
        
                    YAHOO.log(oItem + " successfully registered.", "info", _MENUMANAGER);
        
                }
    
            }
        
        }
    
    
        return {
    
            // Privileged methods
    
    
            /**
            * @method addMenu
            * @description Adds a menu to the collection of known menus.
            * @param {YAHOO.widget.Menu} p_oMenu Object specifying the Menu  
            * instance to be added.
            */
            addMenu: function (p_oMenu) {
    
                var oDoc;
    
                if (p_oMenu instanceof YAHOO.widget.Menu && p_oMenu.id && 
                    !m_oMenus[p_oMenu.id]) {
        
                    m_oMenus[p_oMenu.id] = p_oMenu;
                
            
                    if (!m_bInitializedEventHandlers) {
            
                        oDoc = document;
                
                        Event.on(oDoc, _MOUSEOVER, onDOMEvent, this, true);
                        Event.on(oDoc, _MOUSEOUT, onDOMEvent, this, true);
                        Event.on(oDoc, _MOUSEDOWN, onDOMEvent, this, true);
                        Event.on(oDoc, _MOUSEUP, onDOMEvent, this, true);
                        Event.on(oDoc, _CLICK, onDOMEvent, this, true);
                        Event.on(oDoc, _KEYDOWN, onDOMEvent, this, true);
                        Event.on(oDoc, _KEYUP, onDOMEvent, this, true);
                        Event.on(oDoc, _KEYPRESS, onDOMEvent, this, true);
    
						Event.onFocus(oDoc, onDOMEvent, this, true);
						Event.onBlur(oDoc, onDOMEvent, this, true);						
    
                        m_bInitializedEventHandlers = true;
                        
                        YAHOO.log("DOM event handlers initialized.", "info", _MENUMANAGER);
            
                    }
            
                    p_oMenu.cfg.subscribeToConfigEvent(_VISIBLE, onMenuVisibleConfigChange);
                    p_oMenu.destroyEvent.subscribe(onMenuDestroy, p_oMenu, this);
                    p_oMenu.itemAddedEvent.subscribe(onItemAdded);
                    p_oMenu.focusEvent.subscribe(onMenuFocus);
                    p_oMenu.blurEvent.subscribe(onMenuBlur);
                    p_oMenu.showEvent.subscribe(onMenuShow);
        
                    YAHOO.log(p_oMenu + " successfully registered.", "info", _MENUMANAGER);
        
                }
        
            },
    
        
            /**
            * @method removeMenu
            * @description Removes a menu from the collection of known menus.
            * @param {YAHOO.widget.Menu} p_oMenu Object specifying the Menu  
            * instance to be removed.
            */
            removeMenu: function (p_oMenu) {
    
                var sId,
                    aItems,
                    i;
        
                if (p_oMenu) {
    
                    sId = p_oMenu.id;
        
                    if ((sId in m_oMenus) && (m_oMenus[sId] == p_oMenu)) {

                        // Unregister each menu item

                        aItems = p_oMenu.getItems();

                        if (aItems && aItems.length > 0) {

                            i = aItems.length - 1;

                            do {

                                removeItem(aItems[i]);

                            }
                            while (i--);

                        }


                        // Unregister the menu

                        delete m_oMenus[sId];
            
                        YAHOO.log(p_oMenu + " successfully unregistered.", "info", _MENUMANAGER);
        

                        /*
                             Unregister the menu from the collection of 
                             visible menus
                        */

                        if ((sId in m_oVisibleMenus) && (m_oVisibleMenus[sId] == p_oMenu)) {
            
                            delete m_oVisibleMenus[sId];
                            
                            YAHOO.log(p_oMenu + " unregistered from the" + 
                                        " collection of visible menus.", "info", _MENUMANAGER);
       
                        }


                        // Unsubscribe event listeners

                        if (p_oMenu.cfg) {

                            p_oMenu.cfg.unsubscribeFromConfigEvent(_VISIBLE, 
                                onMenuVisibleConfigChange);
                            
                        }

                        p_oMenu.destroyEvent.unsubscribe(onMenuDestroy, 
                            p_oMenu);
                
                        p_oMenu.itemAddedEvent.unsubscribe(onItemAdded);
                        p_oMenu.focusEvent.unsubscribe(onMenuFocus);
                        p_oMenu.blurEvent.unsubscribe(onMenuBlur);

                    }
                
                }
    
            },
        
        
            /**
            * @method hideVisible
            * @description Hides all visible, dynamically positioned menus 
            * (excluding instances of YAHOO.widget.MenuBar).
            */
            hideVisible: function () {
        
                var oMenu;
        
                for (var i in m_oVisibleMenus) {
        
                    if (Lang.hasOwnProperty(m_oVisibleMenus, i)) {
        
                        oMenu = m_oVisibleMenus[i];
        
                        if (!(oMenu instanceof YAHOO.widget.MenuBar) && 
                            oMenu.cfg.getProperty(_POSITION) == _DYNAMIC) {
        
                            oMenu.hide();
        
                        }
        
                    }
        
                }        
    
            },


            /**
            * @method getVisible
            * @description Returns a collection of all visible menus registered
            * with the menu manger.
            * @return {Object}
            */
            getVisible: function () {
            
                return m_oVisibleMenus;
            
            },

    
            /**
            * @method getMenus
            * @description Returns a collection of all menus registered with the 
            * menu manger.
            * @return {Object}
            */
            getMenus: function () {
    
                return m_oMenus;
            
            },
    
    
            /**
            * @method getMenu
            * @description Returns a menu with the specified id.
            * @param {String} p_sId String specifying the id of the 
            * <code>&#60;div&#62;</code> element representing the menu to
            * be retrieved.
            * @return {YAHOO.widget.Menu}
            */
            getMenu: function (p_sId) {
                
                var returnVal;
                
                if (p_sId in m_oMenus) {
                
					returnVal = m_oMenus[p_sId];
				
				}
            
            	return returnVal;
            
            },
    
    
            /**
            * @method getMenuItem
            * @description Returns a menu item with the specified id.
            * @param {String} p_sId String specifying the id of the 
            * <code>&#60;li&#62;</code> element representing the menu item to
            * be retrieved.
            * @return {YAHOO.widget.MenuItem}
            */
            getMenuItem: function (p_sId) {
    
    			var returnVal;
    
    			if (p_sId in m_oItems) {
    
					returnVal = m_oItems[p_sId];
				
				}
				
				return returnVal;
            
            },


            /**
            * @method getMenuItemGroup
            * @description Returns an array of menu item instances whose 
            * corresponding <code>&#60;li&#62;</code> elements are child 
            * nodes of the <code>&#60;ul&#62;</code> element with the 
            * specified id.
            * @param {String} p_sId String specifying the id of the 
            * <code>&#60;ul&#62;</code> element representing the group of 
            * menu items to be retrieved.
            * @return {Array}
            */
            getMenuItemGroup: function (p_sId) {

                var oUL = Dom.get(p_sId),
                    aItems,
                    oNode,
                    oItem,
                    sId,
                    returnVal;
    

                if (oUL && oUL.tagName && oUL.tagName.toUpperCase() == _UL) {

                    oNode = oUL.firstChild;

                    if (oNode) {

                        aItems = [];
                        
                        do {

                            sId = oNode.id;

                            if (sId) {
                            
                                oItem = this.getMenuItem(sId);
                                
                                if (oItem) {
                                
                                    aItems[aItems.length] = oItem;
                                
                                }
                            
                            }
                        
                        }
                        while ((oNode = oNode.nextSibling));


                        if (aItems.length > 0) {

                            returnVal = aItems;
                        
                        }

                    }
                
                }

				return returnVal;
            
            },

    
            /**
            * @method getFocusedMenuItem
            * @description Returns a reference to the menu item that currently 
            * has focus.
            * @return {YAHOO.widget.MenuItem}
            */
            getFocusedMenuItem: function () {
    
                return m_oFocusedMenuItem;
    
            },
    
    
            /**
            * @method getFocusedMenu
            * @description Returns a reference to the menu that currently 
            * has focus.
            * @return {YAHOO.widget.Menu}
            */
            getFocusedMenu: function () {

				var returnVal;
    
                if (m_oFocusedMenuItem) {
    
                    returnVal = m_oFocusedMenuItem.parent.getRoot();
                
                }
    
    			return returnVal;
    
            },
    
        
            /**
            * @method toString
            * @description Returns a string representing the menu manager.
            * @return {String}
            */
            toString: function () {
            
                return _MENUMANAGER;
            
            }
    
        };
    
    }();

})();



(function () {

	var Lang = YAHOO.lang,

	// String constants
	
		_MENU = "Menu",
		_DIV_UPPERCASE = "DIV",
		_DIV_LOWERCASE = "div",
		_ID = "id",
		_SELECT = "SELECT",
		_XY = "xy",
		_Y = "y",
		_UL_UPPERCASE = "UL",
		_UL_LOWERCASE = "ul",
		_FIRST_OF_TYPE = "first-of-type",
		_LI = "LI",
		_OPTGROUP = "OPTGROUP",
		_OPTION = "OPTION",
		_DISABLED = "disabled",
		_NONE = "none",
		_SELECTED = "selected",
		_GROUP_INDEX = "groupindex",
		_INDEX = "index",
		_SUBMENU = "submenu",
		_VISIBLE = "visible",
		_HIDE_DELAY = "hidedelay",
		_POSITION = "position",
		_DYNAMIC = "dynamic",
		_STATIC = "static",
		_DYNAMIC_STATIC = _DYNAMIC + "," + _STATIC,
		_WINDOWS = "windows",
		_URL = "url",
		_HASH = "#",
		_TARGET = "target",
		_MAX_HEIGHT = "maxheight",
        _TOP_SCROLLBAR = "topscrollbar",
        _BOTTOM_SCROLLBAR = "bottomscrollbar",
        _UNDERSCORE = "_",
		_TOP_SCROLLBAR_DISABLED = _TOP_SCROLLBAR + _UNDERSCORE + _DISABLED,
		_BOTTOM_SCROLLBAR_DISABLED = _BOTTOM_SCROLLBAR + _UNDERSCORE + _DISABLED,
		_MOUSEMOVE = "mousemove",
		_SHOW_DELAY = "showdelay",
		_SUBMENU_HIDE_DELAY = "submenuhidedelay",
		_IFRAME = "iframe",
		_CONSTRAIN_TO_VIEWPORT = "constraintoviewport",
		_PREVENT_CONTEXT_OVERLAP = "preventcontextoverlap",
		_SUBMENU_ALIGNMENT = "submenualignment",
		_AUTO_SUBMENU_DISPLAY = "autosubmenudisplay",
		_CLICK_TO_HIDE = "clicktohide",
		_CONTAINER = "container",
		_SCROLL_INCREMENT = "scrollincrement",
		_MIN_SCROLL_HEIGHT = "minscrollheight",
		_CLASSNAME = "classname",
		_SHADOW = "shadow",
		_KEEP_OPEN = "keepopen",
		_HD = "hd",
		_HAS_TITLE = "hastitle",
		_CONTEXT = "context",
		_EMPTY_STRING = "",
		_MOUSEDOWN = "mousedown",
		_KEYDOWN = "keydown",
		_HEIGHT = "height",
		_WIDTH = "width",
		_PX = "px",
		_EFFECT = "effect",
		_MONITOR_RESIZE = "monitorresize",
		_DISPLAY = "display",
		_BLOCK = "block",
		_VISIBILITY = "visibility",
		_ABSOLUTE = "absolute",
		_ZINDEX = "zindex",
		_YUI_MENU_BODY_SCROLLED = "yui-menu-body-scrolled",
		_NON_BREAKING_SPACE = "&#32;",
		_SPACE = " ",
		_MOUSEOVER = "mouseover",
		_MOUSEOUT = "mouseout",
        _ITEM_ADDED = "itemAdded",
        _ITEM_REMOVED = "itemRemoved",
        _HIDDEN = "hidden",
        _YUI_MENU_SHADOW = "yui-menu-shadow",
        _YUI_MENU_SHADOW_VISIBLE = _YUI_MENU_SHADOW + "-visible",
        _YUI_MENU_SHADOW_YUI_MENU_SHADOW_VISIBLE = _YUI_MENU_SHADOW + _SPACE + _YUI_MENU_SHADOW_VISIBLE;


/**
* The Menu class creates a container that holds a vertical list representing 
* a set of options or commands.  Menu is the base class for all 
* menu containers. 
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source 
* for the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement 
* Object specifying the <code>&#60;select&#62;</code> element to be used as 
* the data source for the menu.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu. See configuration class documentation for 
* more details.
* @namespace YAHOO.widget
* @class Menu
* @constructor
* @extends YAHOO.widget.Overlay
*/
YAHOO.widget.Menu = function (p_oElement, p_oConfig) {

    if (p_oConfig) {

        this.parent = p_oConfig.parent;
        this.lazyLoad = p_oConfig.lazyLoad || p_oConfig.lazyload;
        this.itemData = p_oConfig.itemData || p_oConfig.itemdata;

    }


    YAHOO.widget.Menu.superclass.constructor.call(this, p_oElement, p_oConfig);

};



/**
* @method checkPosition
* @description Checks to make sure that the value of the "position" property 
* is one of the supported strings. Returns true if the position is supported.
* @private
* @param {Object} p_sPosition String specifying the position of the menu.
* @return {Boolean}
*/
function checkPosition(p_sPosition) {

	var returnVal = false;

    if (Lang.isString(p_sPosition)) {

        returnVal = (_DYNAMIC_STATIC.indexOf((p_sPosition.toLowerCase())) != -1);

    }

	return returnVal;

}


var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    Module = YAHOO.widget.Module,
    Overlay = YAHOO.widget.Overlay,
    Menu = YAHOO.widget.Menu,
    MenuManager = YAHOO.widget.MenuManager,
    CustomEvent = YAHOO.util.CustomEvent,
    UA = YAHOO.env.ua,
    
    m_oShadowTemplate,

	EVENT_TYPES = [
    
		["mouseOverEvent", _MOUSEOVER],
		["mouseOutEvent", _MOUSEOUT],
		["mouseDownEvent", _MOUSEDOWN],
		["mouseUpEvent", "mouseup"],
		["clickEvent", "click"],
		["keyPressEvent", "keypress"],
		["keyDownEvent", _KEYDOWN],
		["keyUpEvent", "keyup"],
		["focusEvent", "focus"],
		["blurEvent", "blur"],
		["itemAddedEvent", _ITEM_ADDED],
		["itemRemovedEvent", _ITEM_REMOVED]

	],

	VISIBLE_CONFIG =  { 
		key: _VISIBLE, 
		value: false, 
		validator: Lang.isBoolean
	}, 

	CONSTRAIN_TO_VIEWPORT_CONFIG =  {
		key: _CONSTRAIN_TO_VIEWPORT, 
		value: true, 
		validator: Lang.isBoolean, 
		supercedes: [_IFRAME,"x",_Y,_XY]
	}, 

	PREVENT_CONTEXT_OVERLAP_CONFIG =  {
		key: _PREVENT_CONTEXT_OVERLAP,
		value: true,
		validator: Lang.isBoolean,  
		supercedes: [_CONSTRAIN_TO_VIEWPORT]
	},

	POSITION_CONFIG =  { 
		key: _POSITION, 
		value: _DYNAMIC, 
		validator: checkPosition, 
		supercedes: [_VISIBLE, _IFRAME]
	}, 

	SUBMENU_ALIGNMENT_CONFIG =  { 
		key: _SUBMENU_ALIGNMENT, 
		value: ["tl","tr"]
	},

	AUTO_SUBMENU_DISPLAY_CONFIG =  { 
		key: _AUTO_SUBMENU_DISPLAY, 
		value: true, 
		validator: Lang.isBoolean,
		suppressEvent: true
	}, 

	SHOW_DELAY_CONFIG =  { 
		key: _SHOW_DELAY, 
		value: 250, 
		validator: Lang.isNumber, 
		suppressEvent: true
	}, 

	HIDE_DELAY_CONFIG =  { 
		key: _HIDE_DELAY, 
		value: 0, 
		validator: Lang.isNumber, 
		suppressEvent: true
	}, 

	SUBMENU_HIDE_DELAY_CONFIG =  { 
		key: _SUBMENU_HIDE_DELAY, 
		value: 250, 
		validator: Lang.isNumber,
		suppressEvent: true
	}, 

	CLICK_TO_HIDE_CONFIG =  { 
		key: _CLICK_TO_HIDE, 
		value: true, 
		validator: Lang.isBoolean,
		suppressEvent: true
	},

	CONTAINER_CONFIG =  { 
		key: _CONTAINER,
		suppressEvent: true
	}, 

	SCROLL_INCREMENT_CONFIG =  { 
		key: _SCROLL_INCREMENT, 
		value: 1, 
		validator: Lang.isNumber,
		supercedes: [_MAX_HEIGHT],
		suppressEvent: true
	},

	MIN_SCROLL_HEIGHT_CONFIG =  { 
		key: _MIN_SCROLL_HEIGHT, 
		value: 90, 
		validator: Lang.isNumber,
		supercedes: [_MAX_HEIGHT],
		suppressEvent: true
	},    

	MAX_HEIGHT_CONFIG =  { 
		key: _MAX_HEIGHT, 
		value: 0, 
		validator: Lang.isNumber,
		supercedes: [_IFRAME],
		suppressEvent: true
	}, 

	CLASS_NAME_CONFIG =  { 
		key: _CLASSNAME, 
		value: null, 
		validator: Lang.isString,
		suppressEvent: true
	}, 

	DISABLED_CONFIG =  { 
		key: _DISABLED, 
		value: false, 
		validator: Lang.isBoolean,
		suppressEvent: true
	},
	
	SHADOW_CONFIG =  { 
		key: _SHADOW, 
		value: true, 
		validator: Lang.isBoolean,
		suppressEvent: true,
		supercedes: [_VISIBLE]
	},
	
	KEEP_OPEN_CONFIG = {
		key: _KEEP_OPEN, 
		value: false, 
		validator: Lang.isBoolean
	};



YAHOO.lang.extend(Menu, Overlay, {


// Constants


/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the 
* menu's <code>&#60;div&#62;</code> element.
* @default "yuimenu"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuimenu",


/**
* @property ITEM_TYPE
* @description Object representing the type of menu item to instantiate and 
* add when parsing the child nodes (either <code>&#60;li&#62;</code> element, 
* <code>&#60;optgroup&#62;</code> element or <code>&#60;option&#62;</code>) 
* of the menu's source HTML element.
* @default YAHOO.widget.MenuItem
* @final
* @type YAHOO.widget.MenuItem
*/
ITEM_TYPE: null,


/**
* @property GROUP_TITLE_TAG_NAME
* @description String representing the tagname of the HTML element used to 
* title the menu's item groups.
* @default H6
* @final
* @type String
*/
GROUP_TITLE_TAG_NAME: "h6",


/**
* @property OFF_SCREEN_POSITION
* @description Array representing the default x and y position that a menu 
* should have when it is positioned outside the viewport by the 
* "poistionOffScreen" method.
* @default "-999em"
* @final
* @type String
*/
OFF_SCREEN_POSITION: "-999em",


// Private properties


/** 
* @property _bHideDelayEventHandlersAssigned
* @description Boolean indicating if the "mouseover" and "mouseout" event 
* handlers used for hiding the menu via a call to "YAHOO.lang.later" have 
* already been assigned.
* @default false
* @private
* @type Boolean
*/
_bHideDelayEventHandlersAssigned: false,


/**
* @property _bHandledMouseOverEvent
* @description Boolean indicating the current state of the menu's 
* "mouseover" event.
* @default false
* @private
* @type Boolean
*/
_bHandledMouseOverEvent: false,


/**
* @property _bHandledMouseOutEvent
* @description Boolean indicating the current state of the menu's
* "mouseout" event.
* @default false
* @private
* @type Boolean
*/
_bHandledMouseOutEvent: false,


/**
* @property _aGroupTitleElements
* @description Array of HTML element used to title groups of menu items.
* @default []
* @private
* @type Array
*/
_aGroupTitleElements: null,


/**
* @property _aItemGroups
* @description Multi-dimensional Array representing the menu items as they
* are grouped in the menu.
* @default []
* @private
* @type Array
*/
_aItemGroups: null,


/**
* @property _aListElements
* @description Array of <code>&#60;ul&#62;</code> elements, each of which is 
* the parent node for each item's <code>&#60;li&#62;</code> element.
* @default []
* @private
* @type Array
*/
_aListElements: null,


/**
* @property _nCurrentMouseX
* @description The current x coordinate of the mouse inside the area of 
* the menu.
* @default 0
* @private
* @type Number
*/
_nCurrentMouseX: 0,


/**
* @property _bStopMouseEventHandlers
* @description Stops "mouseover," "mouseout," and "mousemove" event handlers 
* from executing.
* @default false
* @private
* @type Boolean
*/
_bStopMouseEventHa