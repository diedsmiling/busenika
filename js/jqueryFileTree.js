// jQuery File Tree Plugin
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// Visit http://abeautifulsite.net/notebook.php?article=58 for more information
//
// Usage: $('.fileTreeDemo').fileTree( options, callback )
//
// Options:  root           - root folder to display; default = /
//           script         - location of the serverside AJAX file to use; default = jqueryFileTree.php
//           folderEvent    - event to trigger expand/collapse; default = click
//           loadMessage    - Message to display while initial tree loads (can be HTML)
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// TERMS OF USE
// 
// jQuery File Tree is licensed under a Creative Commons License and is copyrighted (C)2008 by Cory S.N. LaViska.
// For details, visit http://creativecommons.org/licenses/by/3.0/us/
//
if (jQuery) (function($) {
	
	$.extend($.fn, {
		file_tree: function(o, h, dbl_clk) {
			// Defaults
			if( !o ) var o = {};
			if( o.root == undefined ) o.root = '/';
			if( o.script == undefined ) o.script = 'file_browser.php';
			if( o.folder_event == undefined ) o.folder_event = 'click';
			if( o.load_message == undefined ) o.load_message = lang.loading;
			if( o.thumb_list_id == undefined ) o.thumb_list_id = null;
			if( o.select_mode_class == undefined ) o.select_mode_class = null;
			o.load_elm = '<ul class="cm-filetree cm-start"><li class="cm-wait">' + o.load_message + '<li></ul>';
			o.cur_elm = null;
			o.cur_effect = null;
			o.CPS = 500;
			
			$(this).each( function() {
				
				function show_tree(c, t, do_update) {
					$(c).addClass('cm-wait');
					o.cur_elm = $(c);
					o.cur_effect = do_update;

					var params = {data:{dir: t, do_update: do_update, view_type: (o.thumb_list_id ? 'dirs' : 'all')}, callback: show_tree_callback, hidden: true, method: 'post'};
					if (o.params) {
						for (var k in o.params) {
							params.data[k] = o.params[k];
						}
					}

					jQuery.ajaxRequest(o.script, params);
				}
				
				function show_tree_callback(data) {
					o.cur_elm.removeClass('cm-wait');
					if (o.cur_effect != 'remove') {
						o.cur_elm.find('.cm-start').html('');
						if (data.picker_id) {
							jQuery.show_picker(data.picker_id, '', '.object-container');
						}
						if (data.file_list != '') {
							o.cur_elm.append(data.file_list);
							bind_tree(o.cur_elm);
						}
						o.cur_elm.find('ul:hidden').eq(0).show();
					}
					if (o.thumb_list_id) {
						$('#' + o.thumb_list_id).html(o.load_elm);
						show_thumbnails($('#' + o.thumb_list_id), $('.cm-selected-mode').attr('rel'));
					}
				}

				function show_thumbnails(c, m) {
					o.cur_elm = $(c);
					jQuery.ajaxRequest(o.script, {data:{view_type: 'thumbs', view_mode: m}, callback: show_thumbnails_callback, hidden: true, method: 'post'});
				}
				
				function show_thumbnails_callback(data) {
					o.cur_elm.empty();
					o.cur_elm.append(data.file_list);
					bind_tree(o.cur_elm);
				}

				function bind_tree(t) {
					$(t).find('li').add($(t).find('li a')).add($(t).find('div a')).bind(o.folder_event, function() {
						if (this.tagName.toLowerCase() == 'li') {
							var cur_obj = $(this);
							var cur_link = $('a', this);
						} else {
							var cur_obj = $(this).parent();
							var cur_link = $(this);
						}
						if (cur_obj.hasClass('directory')) {
							// protect directory button from double click
							if (cur_obj.data('clicked') == true) {
								return false;
							}
							// set clicked flag
							cur_obj.data('clicked', true);
							// clean clicked flag
							setTimeout(function() { 
								cur_obj.data('clicked', false);
							}, o.CPS);
							if (cur_obj.hasClass('cm-collapsed')) {
								// Expand
								var do_update = cur_obj.find('ul').length ? '' : 'update';
								show_tree(cur_obj, escape(cur_link.attr('rel').match( /.*\// )), do_update);
								cur_obj.removeClass('cm-collapsed').addClass('cm-expanded');
							} else {
								// Collapse
								show_tree(cur_obj, escape(cur_link.attr('rel').match( /.*\// )), 'remove');
								cur_obj.find('ul').eq(0).hide();
								cur_obj.removeClass('cm-expanded').addClass('cm-collapsed');
							}
						}
						
						if (cur_obj.hasClass('cm-selectable')) {
							if (h) {
								h(cur_link.attr('rel'));
							}
							if (window['last_clicked_item']) {
								$(window['last_clicked_item']).removeClass('cm-clicked');
							}
							window['last_clicked_item'] = cur_link;
							cur_link.addClass('cm-clicked');
						}

						return false;
					});
					// Prevent A from triggering the # on non-click events
					if (o.folder_event.toLowerCase != 'click') {
						$(t).find('li').add($(t).find('li a')).add($(t).find('div a')).bind('click', function() { return false; });
					}
					if (dbl_clk) {
						$(t).find('li').add($(t).find('li a')).add($(t).find('div a')).bind('dblclick', function() {
							var cur_link = this.tagName.toLowerCase() == 'li' ? $('a', this) :$(this) ;
							dbl_clk(cur_link.attr('rel'));
						});
					}
				}
				// Loading message
				$(this).html(o.load_elm);
				// Get the initial file list
				show_tree($(this), escape(o.root), 'update');
				
				if (o.select_mode_class) {
					$('.' + o.select_mode_class).click(function() {
						$('#' + o.thumb_list_id).html(o.load_elm);
						$('.' + o.select_mode_class).removeClass('cm-selected-mode');
						$(this).addClass('cm-selected-mode');
						show_thumbnails($('#' + o.thumb_list_id), $(this).attr('rel'));
						return false;
					});
				}
			});
		}
	});
	
})(jQuery);