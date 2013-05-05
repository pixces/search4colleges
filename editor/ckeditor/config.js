/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	CKEDITOR.config.toolbar_Full =
	[
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Styles','Format','Font','FontSize','-', 'SpellChecker']
	];
	CKEDITOR.config.resize_enabled = false;
	CKEDITOR.config.toolbarCanCollapse = false;
	CKEDITOR.config.height = 200;
	CKEDITOR.config.width = 550;
};