tinyMCE.importPluginLanguagePack('linkleplugin');

var TinyMCE_LinklePlugin = {
	getInfo : function() {
		return {
			longname : 'Linkle TinyMCE2 Plugin',
			author : 'Mike Lay, Michael Smit',
			authorurl : 'http://www.mikelay.com',
			version : "0.3"
		};
	},
	initInstance : function(inst) {
		inst.addShortcut('ctrl', 'L', 'lang_somename_desc', 'mceLinkleCommand');
	},
	removeInstance : function(inst) {
	},
	showInstance : function(inst) {
	},
	hideInstance : function(inst) {
	},
	getControlHTML : function(cn) {
		switch (cn) {
			case "linkleplugin":
				return tinyMCE.getButtonHTML(cn, 'lang_linkle_button_desc', '{$pluginurl}/../images/linkle.gif', 'mceLinkleCommand');
		}

		return "";
	},
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
			case "mceLinkleCommand":
				// Do your custom command logic here.
				var dialog = new Array();
				dialog['file']='../../../../../wp-content/plugins/linkle/tinymce/tinymce2/linkle.htm';
				dialog['width'] = 250;
				dialog['height'] = 165;
				tinyMCE.openWindow( dialog, {editor_id : tinyMCE.getWindowArg('editor_id'), inline : "yes", resizable : "yes", mce_replacevariables : false });
				return true;
		}
		return false;
	},
	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	},
	setupContent : function(editor_id, body, doc) {
	},
	onChange : function(inst) {
	},
	handleEvent : function(e) {
		return true;
	},
	cleanup : function(type, content, inst) {
		return content;
	},
	_someInternalFunction : function(a, b) {
		return 1;
	}
};

// Adds the plugin class to the list of available TinyMCE plugins
tinyMCE.addPlugin("linkleplugin", TinyMCE_LinklePlugin);
