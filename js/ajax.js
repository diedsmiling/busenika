//
// $Id: ajax.js 10441 2010-08-17 14:09:10Z alexions $
//

jQuery.extend({
	ajaxRequest: function(url, params)
	{

		params = params || {};
		params.method = params.method || 'get';
		params.callback = params.callback || {};
		params.pre_processing = params.pre_processing || {};
		params.data = params.data || {};
		params.message = params.message || lang.loading;
		params.caching = params.caching || false;
		params.hidden = params.hidden || false;
		params.low_priority = params.low_priority || false;
		params.force_exec = params.force_exec || false;
		var QUERIES_LIMIT = 1;

		if (jQuery.active_queries >= QUERIES_LIMIT) { // if we have queries in the queue, push request to it
			if (params.low_priority == true) {
				jQuery.queries_stack.push(function() {
					jQuery.ajaxRequest(url, params);
				});
			} else {
				jQuery.queries_stack.unshift(function() {
					jQuery.ajaxRequest(url, params);
				});
			}

			return true;
		}

		if (params.preload_obj && params.caching) {
			if (params.preload_obj.data('is_loaded')) {
				return true;
			}
		}

		// If query is not hidden, display loading box
		if (params.hidden == false) {
			jQuery.toggleStatusBox('show', params.message);
		}

		if (jQuery.ajax_cache[jQuery.last_hash]) {
			for (var id in jQuery.ajax_cache[jQuery.last_hash].data.html) {
				jQuery.ajax_cache[jQuery.last_hash].data.html[id] = $('#' + id).html();
			}
		}

		var hash = '';
		if (params.caching == true) {
			hash = jQuery.crc32(url + jQuery.param(params.data));
			jQuery.last_hash = hash;
		}

		if (!hash || !jQuery.ajax_cache[hash]) {
			url = fn_query_remove(url, 'result_ids');

			// Add result IDs to data
			if (params.result_ids) {
				params.data.result_ids = params.result_ids;
			}

			if (params.caching && params.store_init_content && !jQuery.ajax_cache.init_content) {
				jQuery.ajax_cache.init_content = {};
				if (params.result_ids) {
					jQuery.ajax_cache.init_content.data = {};
					jQuery.ajax_cache.init_content.data.html = {};
					var ids = params.result_ids.split(',');
					for (var k = 0; k < ids.length; k++) {
						elm = $('#' + ids[k]);
						if (elm.length) {
							jQuery.ajax_cache.init_content.data.html[ids[k]] = elm.html();
						}
					}
				}
			}

			if (url) {
				jQuery.active_queries++;
				jQuery.ajax({
					type: params.method,
					url: url,
					dataType: 'json',
					cache: true,
					data: params.data,
					success: function(data, textStatus) {

						if (params.preload_obj) {
							if (params.preload_obj.data('is_loaded') && params.caching) {
								return false;
							}
							params.preload_obj.data('is_loaded', true);
						}

						if (hash) { // cache response
							jQuery.ajax_cache[hash] = data;
						}

						jQuery.ajaxResponse(data, params);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {

						// Hide loading box
						jQuery.toggleStatusBox('hide');

						// If query is not hidden, display error notice
						if (params.hidden == false) {
							var err_msg = lang.error_ajax.str_replace('[error]', (textStatus ? textStatus : lang.error));
							jQuery.showNotifications({'notification': {'type': 'E', 'title': lang.error, 'message': err_msg, 'save_state': false}});
						}
					},
					complete: function(XMLHttpRequest, textStatus) {

						jQuery.active_queries--;
						if (jQuery.queries_stack.length) {
							var f = jQuery.queries_stack.shift();
							f();
						}
					}
				});
			}

		} else if (hash && jQuery.ajax_cache[hash]) {
			jQuery.ajaxResponse(jQuery.ajax_cache[hash], params);
		}
	},

	ajaxSubmit: function(form, elm)
	{
		var callback = 'fn_form_post_' + form.attr('name');
		var f_callback = window[callback] || null;
		var REQUEST_XML = 1;
		var REQUEST_IFRAME = 2;

		if (form.attr('enctype') == 'multipart/form-data' && form.hasClass('cm-ajax')) {
			if (!$('iframe[name=upload_iframe]').length) {
				$('<iframe name="upload_iframe" src="javascript: false;" class="hidden"></iframe>').appendTo('body');
				$('iframe[name=upload_iframe]').load(function() {
					eval('var response = ' + $(this).contents().find('textarea').val());
					jQuery.ajaxResponse(response, {callback: f_callback});
				});
			}

			form.append('<input type="hidden" name="is_ajax" value="' + REQUEST_IFRAME + '" />');
			form.attr('target', 'upload_iframe');
			jQuery.ajaxRequest('', null);

			return true;
		} else {
			var hash = $(':input', form).serializeArray();

			// Send name/value of clicked button
			hash.push({name: elm.attr('name'), value: elm.val()});

			jQuery.ajaxRequest(form.attr('action'), {
				method: form.attr('method'),
				data: hash,
				callback: f_callback,
				force_exec: form.hasClass('cm-ajax-force') ? true : false
			});

			return false;
		}
	},

	ajaxResponse: function(response, params)
	{
		params = params || {};
		params.force_exec = params.force_exec || false;
		params.callback = params.callback || {};
		params.pre_processing = params.pre_processing || {};

		var regex_all = new RegExp('<script[^>]*>([\u0001-\uFFFF]*?)</script>', 'img');
		var matches = [];
		var match = '';
		var elm;
		var data = response.data || {};

		// If pre processing function passed, run it
		if (params.pre_processing) {
			if (typeof(params.pre_processing) == 'function') { // call ordinary function
				params.pre_processing(data, params);
			} else if (params.pre_processing[1]) { // call object method [obj, 'meth']
				params.pre_processing[0][params.pre_processing[1]](data, response.text, params);
			}
		}

		if (!jQuery.loaded_scripts) {
			jQuery.loaded_scripts = [];
			$('script').each(function() {
				var _src = $(this).attr('src');
				if (_src) {
					jQuery.loaded_scripts.push(jQuery.getBaseName(_src));
				}
			})
		}

		// Ajax request forces browser to redirect
		if (data.force_redirection) {
			jQuery.redirect(data.force_redirection);
		}

		// Data returned that should be inserted to DOM
		if (data.html) {
			for (var k in data.html) {
				elm = $('#' + k);
				if (elm.length != 1) {
					continue;
				}

				matches = data.html[k].match(regex_all);
				elm.html(matches ? data.html[k].replace(regex_all, '') : data.html[k]);
				
				// Display/hide hidden block wrappers
				if (jQuery.trim(elm.html())) {
					elm.parents('.hidden.cm-hidden-wrapper').removeClass('hidden');
				} else {
					elm.parents('.cm-hidden-wrapper').addClass('hidden');
				}

				// If returned data contains scripts, execute them
				if (matches) {
					for (var i = 0; i < matches.length; i++ ) {
						var m = $(matches[i]);

						// External script
						if (m.attr('src')) {
							var _src = jQuery.getBaseName(m.attr('src'));
							if (jQuery.inArray(_src, jQuery.loaded_scripts) == -1) {
								jQuery.loaded_scripts.push(_src);
								m.appendTo('body');
							}
						} else {
							var _hash = jQuery.crc32(m.html());
							if (!this.eval_cache[_hash] || params.force_exec || m.hasClass('cm-ajax-force')) {
								this.eval_cache[_hash] = true;
								if (window.execScript) {
									window.execScript(m.html());
								} else {
									window.eval(m.html());
								}
							}
						}
					}
				}


				$(".cm-j-tabs", elm).each(function(){ $(this).idTabs(); });

				// if returned data contains forms, process them
				if (data.html[k].indexOf('<form') != -1) {
					jQuery.processForms(elm);
					if (jQuery.area == 'A') {
						$('form[method=post]', elm).addClass('cm-check-changes');
					}
				}
				if (elm.parents('form').length) {
					elm.parents('form:first').highlightFields();
				}

				jQuery.loadTooltips();

				if (elm.data('callback')) {
					elm.data('callback')();
					elm.removeData('callback');
				}
			}
		}

		// Display notification
		if (data.notifications) {
			jQuery.showNotifications(data.notifications);
		}

		// If callback function passed, run it
		if (params.callback) {
			if (typeof(params.callback) == 'function') { // call ordinary function
				params.callback(data, params);
			} else if (params.callback[1]) { // call object method [obj, 'meth']
				params.callback[0][params.callback[1]](data, response.text, params);
			}
		}

		// Hide loading box
		jQuery.toggleStatusBox('hide');
	},

	objectSerialize: function (d, suff, l)
	{

		if (l == null) {
			l = 1;
		}
		if (suff == null) {
			suff = '';
		}

		var s = '';

		if (typeof(d) == "object") {
			for (var k in d) {
				var _suff = (l == 1 ? k : suff + "[" + k + "]");
				s += this.objectSerialize(d[k], _suff, l + 1);
			}
		} else {
			s += suff + "=" + d + "&";
		}

		return s;
	},

	getBaseName: function (path)
	{
		return path.split('/').pop();
	},
	
	loadAjaxLinks: function(elms, high_priority)
	{
		elms.each(function() {
			var a = $(this);
			var p = !(high_priority || false);
			if (a.data('is_loaded') || a.parents(':hidden').length) { // do no load hidden links
				return false;
			}

			var _form = a.parents('form');

			var _obj = $('#' + a.attr('id').str_replace('opener_', ''));

			if (_form.length == 1) {
				_form.before(_obj);
			} else {
				$('body').append(_obj);
			}

			var params = {
				caching: true,
				hidden: p,
				result_ids: a.attr('rev'),
				low_priority: p,
				preload_obj: a
			};

			jQuery.ajaxRequest(a.attr('href'), params);
		});
	},

	ajax_cache: {},
	queries_stack: [],
	active_queries: 0,
	eval_cache: {},
	last_hash: ''
});

$(window).load(function(){
	if (typeof(disable_ajax_preload) == 'undefined' || !disable_ajax_preload) {
		jQuery.loadAjaxLinks($('a.cm-ajax-update'));
	}
});

function fn_query_remove(query, vars)
{
	if (typeof(vars) == 'undefined') {
		return query;
	}
	if (typeof vars == 'string') {
		vars = [vars];
	}
	var start = query;
	if (query.indexOf('?') >= 0) {
		start = query.substr(0, query.indexOf('?'));
		var search = query.substr(query.indexOf('?'));
		var srch_array = search.split("&");
		var temp_array = new Array();
		var concat = true;
		var amp = '';
		for (var i = 0; i < srch_array.length; i++) {
			temp_array = srch_array[i].split("=");
			concat = true;
			for (var j = 0; j < vars.length; j++) {
				if (vars[j] == temp_array[0] || temp_array[0].indexOf(vars[j]+'[') != -1) {
					concat = false;
					break;
				}
			}
			if (concat == true) {
				start += amp + temp_array[0] + '=' + temp_array[1]
			}
			amp = '&';
		}
	}
	return start;
};