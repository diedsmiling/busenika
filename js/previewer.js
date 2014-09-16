// $Id: previewer.js 10020 2010-07-08 12:39:11Z lexa $

var bottom_height = 145;
var enable_animation = false;
var img_preloader = [];

$(document).bind('click', function(e)
{
	return fn_previewer_init(e);
});

//add previewer to href & area elements that have a class of .previewer
function fn_previewer_init(e)
{
	var jelm = $(e.target);

	if ((e.type == 'click' || e.type == 'mousedown') && jQuery.browser.mozilla && e.which != 1) {
		return true;
	}

	// Dispatch click event
	if (e.type == 'click') {
		if (jelm.hasClass('cm-thumbnails')) {
			var link = jelm.parent('a');
			if (link.length) {
				var a = $(link).attr('href') || link.alt;
				var t = $(link).attr('rev') || '';
				fn_previewer_show(t, a, jelm);
				jelm.blur();
			}

			return false;

		} else if (jelm.hasClass('cm-thumbnails-mini') || jelm.parents('a:first').hasClass('cm-thumbnails-mini')) {
			jelm = jelm.parents('a:first').hasClass('cm-thumbnails-mini') ? jelm.parents('a:first') : jelm;
			var img_elm = $('img:not(.cm-template-icon)', jelm);

			$('.cm-cur-item').removeClass('cm-cur-item');
			jelm.addClass('cm-cur-item');
			$('.cm-thumbnails').hide();
			var dest_elm = $('#' + img_elm.attr('id').str_replace('_mini', '') + '.cm-thumbnails');
			dest_elm.show();
			$('.cm-thumbnails-opener').parent().hide();
			if ($('.cm-thumbnails-opener').eq($('.cm-thumbnails').index(dest_elm)).attr('href')) {
				$('.cm-thumbnails-opener').parent().eq($('.cm-thumbnails').index(dest_elm)).show();
			}
			jelm.blur();
			return false;

		} else if (jelm.hasClass('cm-thumbnails-opener')) {
			$('.cm-thumbnails:visible', jelm.parent().parent()).click();
			jelm.blur();
			return false;
		}
	}
}

//function called when the user clicks on a previewer link
function fn_previewer_show(caption, url, owner, thumb_action)
{
	try {
		if (!$('#previewer_window').length) {
			if (typeof document.body.style.maxHeight == 'undefined') {//if IE 6
				var trl_shadows = '<div class="w-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_w.png, sizingMethod=scale);"></div><div class="e-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_e.png, sizingMethod=scale);"></div><div class="nw-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_nw.png, sizingMethod=scale);"></div><div class="ne-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_ne.png, sizingMethod=scale);"></div><div class="sw-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_sw.png, sizingMethod=scale);"></div><div class="se-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_se.png, sizingMethod=scale);"></div><div class="n-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_n.png, sizingMethod=scale);"></div>';
				var b_shadow = '<div class="s-shadow" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_s.png, sizingMethod=scale);"></div>';
				if (!$('#previewer_hide_select').get(0)) {//iframe to hide select elements in ie6
					$('body').append('<iframe id="previewer_hide_select"></iframe><div id="previewer_overlay"></div><div id="previewer_window"></div>');
					$('#previewer_overlay').click(fn_previewer_remove);
				}
			} else {//all others
				var trl_shadows = '<div class="w-shadow"></div><div class="e-shadow"></div><div class="nw-shadow"></div><div class="ne-shadow"></div><div class="sw-shadow"></div><div class="se-shadow"></div><div class="n-shadow"></div>';
				var b_shadow = '<div class="s-shadow"></div>';
				if (!$('#previewer_overlay').get(0)) {
					$('body').append('<div id="previewer_overlay"></div><div id="previewer_window"></div>');
					$('#previewer_overlay').click(fn_previewer_remove);
				}
			}

			var previewer_thumbs = '';
			if (!owner.hasClass('cm-single')) {
				var previewer_thumbs_elm = $("img.cm-thumbnails").parent('a');
				if (previewer_thumbs_elm.length > 1) {
					previewer_thumbs_elm = $("img.cm-thumbnails");
					previewer_thumbs = '<div class="viewer-container"><table cellpadding="0" cellspacing="0" border="0"><tr><td><div id="prev_btn" class="prev-horizontal"></div></td><td><div id="viewer"><div id="thumbs_frame">';
					for (var i = 0; i < previewer_thumbs_elm.length; i++) {
						var thmb = previewer_thumbs_elm.eq(i);
						if (thmb.parent('a').length) {
							previewer_thumbs += '<img src="' + $('#' + thmb.attr('id') + '_mini').attr('src') + '" alt="' + thmb.attr('alt') + '" onclick="fn_previewer_show(\'' + escape(thmb.parent().attr('rev')) + '\', \'' + escape(thmb.parent().attr('href')) + '\', this, true); return false;" />';
						}
					}
					previewer_thumbs += '</div></div></td><td><div id="next_btn" class="next-horizontal"></div></td></tr></table></div>';
				}
			}

			P_HEIGHT = 500;
			P_WIDTH = 400;

			$('#previewer_window').append(trl_shadows + '<div class="popupbox-closer" id="previewer_close_button"><img src="' + images_dir + '/icons/close_popupbox.png" title="' + lang.close + '" alt="' + lang.close + '" /></div><a id="preview_right"><span class="preview-right"></span></a><a id="preview_left"><span class="preview-left"></span></a><div class="previewer-container"><div class="scroller-container"><div id="scroller_holder" style="width: 100%; height:' + P_WIDTH + 'px; overflow:hidden;"><div id="previewer_scroller" style="width: 100%; height:' + P_WIDTH + 'px; overflow:hidden;"></div></div></div><div class="clear-both"></div><table id="preview_control" border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="center">' + previewer_thumbs + '</td></tr></table></div>' + b_shadow);

			$('.preview-right,.preview-left').hide();
			$('#preview_right,#preview_left').click(function() {
				var thumbs_elms = $("img.cm-thumbnails").parent('a');
				for (var i = 0; i < thumbs_elms.length; i++) {
					if ($('#previewer_image').attr('src').indexOf(thumbs_elms.eq(i).attr('href')) >= 0) {
						thumbs_elms.eq(i + ($(this).is('#preview_right') ? 1 : -1)).contents('.cm-thumbnails').click();
						break;
					}
				}
			});
			$('#preview_right,#preview_left').mouseover(function() {
				$(this).contents('.preview-right,.preview-left').show();
			}).mouseout(function() {
				$(this).contents('.preview-right,.preview-left').hide();
			});

			if (previewer_thumbs) {
				$("#viewer").imageScroller({
					next: 'prev_btn',
					prev: 'next_btn',
					frame: 'thumbs_frame',
					child: 'img'
				});
			}

			$("#previewer_close_button").click(fn_previewer_remove);

			$(document).bind('keydown', function(e) {
				var char_code = (e.which) ? e.which : e.keyCode;
				if (char_code == 27) {
					if ($('#previewer_window:visible').length) {
						fn_previewer_remove();
					}
				}
			});
		}

		$('#thumbs_frame img').removeClass('cm-current-thumb');
		if (thumb_action) {
			$(owner).addClass('cm-current-thumb');
		}
		var thumbs_elms = $("img.cm-thumbnails").parent('a');
		if (thumbs_elms.length > 1) {
			for (var i = 0; i < thumbs_elms.length; i++) {
				if (thumbs_elms.eq(i).attr('href') == url) {
					if (!thumb_action && !owner.hasClass('cm-single')) {
						$('#thumbs_frame img').eq(i).addClass('cm-current-thumb');
					}
					$('#preview_right,#preview_left').hide();
					if (thumbs_elms.eq(i + 1).length) {
						$('#preview_right').show();
					}
					if (thumbs_elms.eq(i - 1).length) {
						$('#preview_left').show();
					}
					break;
				}
			}
		}

		var img_id = jQuery.crc32(url);
		if (!img_preloader[img_id]) {
			img_preloader[img_id] = $('<img id="previewer_image" src="' + url + '" alt="' + (thumb_action ? unescape(caption) : caption) + '"/>').load(function() {
				$(this).unbind('load');
        var self=this;
        setTimeout(function(){
				  fn_build_previewer($(self));
        }, 0);
			});
			if (jQuery.browser.opera) {
				fn_build_previewer(img_preloader[img_id]);
			}
		} else {
			fn_build_previewer(img_preloader[img_id]);
		}

	} catch(e) {
		//nothing here
	}
}

function fn_build_previewer(img_preloader)
{
	$('#previewer_scroller').empty();
	img_preloader.appendTo($('#previewer_scroller'));
	$('#previewer_scroller').get(0).scrollLeft = 0;
	$('#previewer_scroller').get(0).scrollTop = 0;

	$('#previewer_image').bind('mousedown', function(e) {
		e = (e) ? e : window.event;
		var elm = e.target;
		try {
			e.preventDefault();
		} catch(error) {
		}

		var start_x = e.clientX;
		var start_y = e.clientY;
		var orig_x = $('#previewer_scroller').get(0).scrollLeft;
		var orig_y = $('#previewer_scroller').get(0).scrollTop;

		elm.onselectstart = function()
		{
			return false;
		};

		$(document).bind('mouseup', elm, fn_previewer_reset_move_event);

		$(elm).mousemove(function(e)
		{
			e = (e) ? e : window.event;
			var k = 1.4;

			$('#previewer_scroller').get(0).scrollLeft = orig_x + (start_x - e.clientX) * k;
			$('#previewer_scroller').get(0).scrollTop = orig_y + (start_y - e.clientY) * k;

			return false;
		});

		return false;
	});

	$("#previewer_window, #previewer_overlay, #previewer_hide_select").show();
	fn_previewer_position(img_preloader.get(0));
}

function fn_previewer_reset_move_event(e)
{
	$(e.data).unbind('mousemove');
	$(document).unbind('mouseup', fn_previewer_reset_move_event);
}

function fn_previewer_init_control(skip_animation)
{
	var margin_top = $('#previewer_image').height() < $('#previewer_scroller').height() ? ($('#previewer_scroller').height() - $('#previewer_image').height()) / 2 : 0;
	if (skip_animation || !enable_animation) {
		$('#previewer_image').css('margin-top', margin_top + 'px');
	} else {
		$('#previewer_image').animate({marginTop: margin_top}, 200);
	}

	if ($('#previewer_scroller').height() >= $('#previewer_image').height() && $('#previewer_scroller').width() >= $('#previewer_image').width()) {
		$('#previewer_image').css('cursor', '');
	} else {
		$('#previewer_image').css('cursor', 'move');
	}
}

function fn_previewer_init_scroller(skip_animation)
{
	var size = $('#thumbs_frame img:first').outerWidth(true);
	var thumb_count = $('#thumbs_frame img').length;
	var vis_count = Math.floor((P_WIDTH - 2 * $('#prev_btn').width() - 100) / size);
	$('#thumbs_frame').width(size * thumb_count);
	var count = thumb_count;
	if (thumb_count > vis_count) {
		count = vis_count;
		$('#prev_btn').css('visibility', 'visible');
		$('#next_btn').css('visibility', 'visible');
	} else {
		$('#prev_btn').css('visibility', 'hidden');
		$('#next_btn').css('visibility', 'hidden');
	}
	if (skip_animation || !enable_animation) {
		$('#viewer').css('width', count * size + 'px');
	} else {
		$('#viewer').animate({width: count * size});
	}
}

function fn_previewer_remove()
{
	$('#previewer_window, #previewer_overlay, #previewer_hide_select').hide();

	return false;
}

function fn_previewer_position(img, amimate)
{
	var pagesize = jQuery.get_window_sizes();
	var padding_side = 100; // padding (sum) from screen top and bottom
	var max_previewer_width = 940; // maximum previewer width
	var min_width = pagesize.view_width < 400 ? pagesize.view_width - padding_side : 400; // minimum image holder width for large resolution
	var min_height = pagesize.view_height < 450 ? pagesize.view_height - padding_side : 450; // minimum image holder height for large resolution
	var h_diff = $('#previewer_window').height() - $('#scroller_holder').height();
	var w_diff = $('#previewer_window').width() - $('#scroller_holder').width();
	var h_padding_size = $('#previewer_window').outerHeight() - $('#previewer_window').height();

	P_HEIGHT = img.height + h_diff + padding_side > pagesize.view_height ? pagesize.view_height - padding_side : img.height + h_diff;
	P_WIDTH = img.width + w_diff > max_previewer_width ? max_previewer_width : img.width + w_diff;
	P_HEIGHT = min_height > P_HEIGHT ? min_height : P_HEIGHT;
	P_WIDTH = min_width > P_WIDTH ? min_width : P_WIDTH;
	P_WIDTH = pagesize.view_width < P_WIDTH ? pagesize.view_width - padding_side : P_WIDTH;

	if (amimate && enable_animation) {
		$('#scroller_holder').animate({height: P_HEIGHT - h_diff + 'px'}, 400);
		$('#previewer_scroller').animate({height: P_HEIGHT - h_diff + 'px'}, 400, function() {$("#previewer_scroller").css({overflow: "hidden"});});

		fn_previewer_init_scroller();

		var new_params = {width: P_WIDTH + 'px', height: P_HEIGHT + 'px'};
		if (!(jQuery.browser.msie && parseInt(jQuery.ua.version, 10) < 8)) {
			new_params['marginTop'] = '-' + parseInt(((h_padding_size + P_HEIGHT) / 2), 10) + 'px';
			new_params['marginLeft'] = '-' + parseInt((P_WIDTH / 2), 10) + 'px';
		}
		$('#previewer_window').animate(new_params, 400, fn_previewer_init_control);
		
	} else {
		$('#scroller_holder').height(P_HEIGHT - h_diff);
		$('#previewer_scroller').height(P_HEIGHT - h_diff);

		$('#previewer_window').css({width: P_WIDTH + 'px', height: P_HEIGHT + 'px'});
		if (!(jQuery.browser.msie && parseInt(jQuery.ua.version, 10) < 8)) {
			$('#previewer_window').css({marginLeft: '-' + parseInt((P_WIDTH / 2), 10) + 'px', marginTop: '-' + parseInt(((h_padding_size + P_HEIGHT) / 2), 10) + 'px'});
		}
		fn_previewer_init_scroller(true);
		fn_previewer_init_control(true);
	}
}

jQuery.fn.imageScroller = function(params){
	var p = params;
	var btn_next = $('#' + p.next);
	var btn_prev = $('#' + p.prev);
	var img_frame = $('#' + p.frame);
	var child = p.child;

	var turn_up = function(){
		btn_prev.unbind('click', turn_up);
		img_frame.animate({marginLeft: - img_frame.find(child + ':first').width()}, 'fast', '', function(){
			img_frame.css('marginLeft', 0);
			img_frame.find(child + ':first').clone().appendTo(img_frame).show();
			img_frame.find(child + ':first').remove();
			btn_prev.bind('click', turn_up);
		});
	};

	var turn_down = function(){
		btn_next.unbind('click', turn_down);
		img_frame.find(child + ':last').clone().show().prependTo(img_frame);
		img_frame.css('marginLeft', - img_frame.find(child + ':first').width());
		img_frame.animate({marginLeft: 0}, 'fast', '', function(){
			img_frame.find(child + ':last').remove();
			btn_next.bind('click', turn_down);
		});
	};

	btn_next.css('cursor', 'hand').click(turn_down);
	btn_prev.css('cursor', 'hand').click(turn_up);
};