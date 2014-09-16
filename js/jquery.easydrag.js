/**
* EasyDrag 1.4 - Drag & Drop jQuery Plug-in
*
* Thanks for the community that is helping the improvement
* of this little piece of code.
*
* For usage instructions please visit http://fromvega.com
*/

(function($){

	// to track if the mouse button is pressed
	var is_mouse_down = false;

	// to track the current element being dragged
	var cur_elm = null;
	var resize_elm = null;
	var inner_resize_elm = null;

	// callback holders
	var drop_callbacks = {};
	var drag_callbacks = {};
	var startdrag_callbacks = {};

	// global position records
	var top_side;
	var left_side;
	var last_mouse_x;
	var last_mouse_y;
	var last_elm_top;
	var last_elm_left;
	var resize_elem_width;
	var resize_elem_height;
	var resize_elem_left;
	var resize_elem_top;
	var last_span_x;
	var last_span_y;
	var last_document_right;
	var last_document_left;
	var last_document_top;
	var last_document_bottom;
	var new_resize_elem_height;

	// track element drag_status
	var drag_status = {};	

	// returns the mouse (cursor) current position
	$.get_mouse_position = function(e){
		var posx = 0;
		var posy = 0;

		if (!e) var e = window.event;

		if (e.pageX || e.pageY) {
			posx = e.pageX;
			posy = e.pageY;
		}
		else if (e.clientX || e.clientY) {
			posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			posy = e.clientY + document.body.scrollTop  + document.documentElement.scrollTop;
		}

		return { 'x': posx, 'y': posy };
	};

	// updates the position of the current element being dragged
	$.update_position = function(e) {
		var pos = $.get_mouse_position(e);

		var span_x = pos.x - last_mouse_x;
		var span_y = pos.y - last_mouse_y;

		if (last_elm_top + span_y + cur_elm.offsetHeight > last_document_top) {
			span_y = last_document_top - last_elm_top - cur_elm.offsetHeight;
		}

		if (last_elm_left + span_x + cur_elm.offsetWidth > last_document_right - (jQuery.browser.msie ? 0 : 17)) {
			span_x = last_document_right - (jQuery.browser.msie ? 0 : 17) - last_elm_left - cur_elm.offsetWidth;
		}

		if (last_elm_top + span_y < last_document_bottom) {
			span_y = last_document_bottom - last_elm_top;
		}

		if (last_elm_left + span_x < last_document_left) {
			span_x = last_document_left - last_elm_left;
		}

		if (drag_status[cur_elm.id] == 'on') {
			$(cur_elm).css("top",  (last_elm_top + span_y));
			$(cur_elm).css("left", (last_elm_left + span_x));

		}
		if (drag_status[cur_elm.id] == 'resize_v' || drag_status[cur_elm.id] == 'resize_c') {
			if (top_side) {
				span_y = - span_y;
			}
			if (resize_elem_height + span_y >= 140) {
				if (top_side) {
					$(resize_elm).css('top', resize_elem_top - span_y);
				}
				$(resize_elm).css('height', resize_elem_height + span_y);
				if (inner_resize_elm) {
					new_resize_elem_height = resize_elem_height + span_y - last_span_y;
				}
			}
		}
		if (drag_status[cur_elm.id] == 'resize_h' || drag_status[cur_elm.id] == 'resize_c') {
			if (left_side) {
				span_x = - span_x;
			}
			if (resize_elem_width + span_x >= 250) {
				if (left_side) {
					$(resize_elm).css('left', resize_elem_left - span_x);
				}
				$(resize_elm).css('width', resize_elem_width + span_x);
			}
		}
	};

	$.update_viewport = function() {
		var w = jQuery.get_window_sizes();
		last_document_right = w.offset_x + w.view_width;
		last_document_left = w.offset_x;
		last_document_top = w.offset_y + w.view_height;
		last_document_bottom = w.offset_y;
		return w;
	};

	// when the mouse is moved while the mouse button is pressed
	$(document).mousemove(function(e){
		if(is_mouse_down && cur_elm && drag_status[cur_elm.id]){
			// update the position and call the registered function
			$.update_position(e);
			if(drag_status[cur_elm.id] == 'on' && drag_callbacks[cur_elm.id] != undefined){
				drag_callbacks[cur_elm.id](e, cur_elm);
			}

			return false;
		}
	});

	// when the mouse button is released
	$(document).mouseup(function(e){
		if(is_mouse_down && cur_elm && drag_status[cur_elm.id]){
			is_mouse_down = false;
			var elm = drag_status[cur_elm.id] == 'on' ? cur_elm : resize_elm;
			if(drop_callbacks[elm.id] != undefined){
				drop_callbacks[elm.id](e, elm);
			}
			drag_status[cur_elm.id] = '';
			if (inner_resize_elm && new_resize_elem_height) {
				inner_resize_elm.css('height', new_resize_elem_height);
			}
			if (resize_elm && jQuery.browser.msie && jQuery.ua.version == '6.0') {
				$('.cm-popup-hor-resizer', resize_elm).css('height', $(resize_elm).height() + 'px');
			}

			$('body').removeAttr('onSelectStart');
			$('body').removeAttr('onmousedown');

			return false;
		}
	});

	$(window).scroll(function () { 
		if(is_mouse_down && cur_elm && drag_status[cur_elm.id] == 'on'){
			$.update_viewport();
		}
	});
	
	// register the function to be called when element start dragg
	$.fn.startdrag = function(callback){
		return this.each(function(){
			startdrag_callbacks[this.id] = callback;
		});
	};

	// register the function to be called while an element is being dragged
	$.fn.ondrag = function(callback){
		return this.each(function(){
			drag_callbacks[this.id] = callback;
		});
	};

	// register the function to be called when an element is dropped
	$.fn.ondrop = function(callback){
		return this.each(function(){
			drop_callbacks[this.id] = callback;
		});
	};

	$.fn.easydrag = function(){

		return this.each(function(){

			// if no id is defined assign a unique one
			if(undefined == this.id || !this.id.length) this.id = "easydrag"+(new Date().getTime());

			// when an element receives a mouse press
			$(this).mousedown(function(e){

				// update track variables
				is_mouse_down = true;

				// retrieve positioning properties
				var pos = $.get_mouse_position(e);
				last_mouse_x = pos.x;
				last_mouse_y = pos.y;

				var w = $.update_viewport();

				if($(this).css('position') == 'fixed') {
					last_mouse_x -= w.offset_x;
					last_mouse_y -= w.offset_y;
				}

				if (($(e.target).parents('.cm-popup-content-header').length || $(e.target).hasClass('cm-popup-content-header')) && e.target.nodeName.toLowerCase() != 'img' && e.target.nodeName.toLowerCase() != 'a') {
					// set drag_status 
					drag_status[this.id] = "on";
					cur_elm = this;

					last_elm_top = this.offsetTop;
					last_elm_left = this.offsetLeft;
					inner_resize_elm = null;

					$.update_position(e);
					
					if(startdrag_callbacks[cur_elm.id] != undefined){
						startdrag_callbacks[cur_elm.id](e, cur_elm);
					}

				} else if ($(this).hasClass('cm-popup-vert-resizer') || $(this).hasClass('cm-popup-hor-resizer') || $(this).hasClass('cm-popup-corner-resizer')) {
					drag_status[this.id] = $(this).hasClass('cm-popup-vert-resizer') ? 'resize_v' : ($(this).hasClass('cm-popup-hor-resizer') ? 'resize_h' : 'resize_c');
					cur_elm = this;

					resize_elm = $(this).parents('.cm-picker').get(0);

					last_elm_top  = this.offsetTop + resize_elm.offsetTop;
					last_elm_left = this.offsetLeft + resize_elm.offsetLeft;
					resize_elem_top = resize_elm.offsetTop;
					resize_elem_left = resize_elm.offsetLeft;
					resize_elem_width = resize_elm.offsetWidth - 6;
					resize_elem_height = resize_elm.offsetHeight - 6;
					inner_resize_elm = $(resize_elm).data('scroll_elm') ? $(resize_elm).data('scroll_elm') : ($('iframe', resize_elm).length ? $('iframe', resize_elm) : null);
					new_resize_elem_height = 0;
					if (inner_resize_elm) {
						last_span_x = resize_elem_width - inner_resize_elm.width();
						last_span_y = resize_elem_height - inner_resize_elm.height();
					}

					top_side = $(this).hasClass('cm-top-resizer') || $(this).hasClass('cm-nw-resizer') || $(this).hasClass('cm-ne-resizer') ? true : false;
					left_side = $(this).hasClass('cm-left-resizer') || $(this).hasClass('cm-nw-resizer') || $(this).hasClass('cm-sw-resizer') ? true : false;

					if(startdrag_callbacks[resize_elm.id] != undefined){
						startdrag_callbacks[resize_elm.id](e, resize_elm);
					}
				}
				if (drag_status[this.id]) {
					$('body').attr('onSelectStart', 'return false;');
					$('body').attr('onmousedown', 'return false;');
				}

				return true;
			});
		});
	};
})(jQuery);