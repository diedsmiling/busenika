/*
 * jQuery history plugin
 *
 * Copyright (c) 2006 Taku Sano (Mikage Sawatari)
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Modified by Lincoln Cooper to add Safari support and only call the callback once during initialization
 * for msie when no initial hash supplied.
 * API rewrite by Lauris Bukðis-Haberkorns
 */

(function($) {

function History()
{
	this._curHash = '';
	this._callback = function(hash){};
	this._stack = {};
	this._location_stack = [];
	this._init_page = false;
	this._hash_prefix = '';
};

jQuery.extend(History.prototype, {

	init: function(callback) {
		this._callback = callback;
		this._curHash = location.hash;
		this._stack = fn_get_js_session('history_ajax_stack');
		if (!this._stack) {
			this._stack = {};
		}

		var href = location.href + (this._curHash == '' && jQuery.browser.msie && parseInt(jQuery.ua.version) < 8 ? '#' : '');
		this._location_stack = fn_get_js_session('history_stack');
		if (!this._location_stack) {
			this._location_stack = [];
			this._location_stack.push([href, true]);
		} else if (!this._location_stack.length || this._location_stack[this._location_stack.length - 1][0] != href) {
			this._location_stack.push([href, true]);
		}

		this._hash_prefix = jQuery.crc32(location.search);

		if(jQuery.browser.msie && parseInt(jQuery.ua.version) < 8) {
			// To stop the callback firing twice during initilization if no hash present
			if (this._curHash == '') {
				this._curHash = '#';
			}
			// add hidden iframe for IE
			$("body").prepend('<iframe id="jQuery_history" style="display: none;"></iframe>');
			var iframe = $("#jQuery_history")[0].contentWindow.document;
			iframe.open();
			iframe.close();
			iframe.location.hash = this._curHash;
		}

		jQuery.history._run_callback(this._curHash.replace(/^#/, ''));
		setInterval(this._check, 100);

		$(window).unload(function() {
			jQuery.history._location_stack.pop();
			fn_set_js_session('history_stack', jQuery.history._location_stack);
			fn_set_js_session('history_ajax_stack', jQuery.history._stack);
		});
	},

	_update_location_stack: function() {
		if (jQuery.history._location_stack[jQuery.history._location_stack.length - 2] && jQuery.history._location_stack[jQuery.history._location_stack.length - 2][0] == location.href) {
			jQuery.history._location_stack.pop();
		} else {
			jQuery.history._location_stack.push([location.href, false]);
		}
	},

	_run_callback: function (hash) {
		var data = null;
		if (jQuery.history._stack['url_' + jQuery.history._hash_prefix + hash]) {
			data = {url: jQuery.history._stack['url_' + jQuery.history._hash_prefix + hash], result_ids: jQuery.history._stack['result_ids_' + jQuery.history._hash_prefix + hash], callback: jQuery.history._stack['callback_' + jQuery.history._hash_prefix + hash]};
		} else if (jQuery.history._location_stack[jQuery.history._location_stack.length - 1][0] == location.href && jQuery.history._location_stack[jQuery.history._location_stack.length - 1][1]) {
			jQuery.history._init_page = true;
		}
		jQuery.history._callback(data);
		jQuery.history._init_page = false;
	},

	_check: function() {
		if(jQuery.browser.msie && parseInt(jQuery.ua.version) < 8) {
			// On IE, check for location.hash of iframe
			var ihistory = $("#jQuery_history")[0];
			var iframe = ihistory.contentDocument || ihistory.contentWindow.document;
			var current_hash = iframe.location.hash;
			if(current_hash != jQuery.history._curHash) {
				location.hash = current_hash;
				jQuery.history._update_location_stack();
				jQuery.history._curHash = current_hash;
				jQuery.history._run_callback(current_hash.replace(/^#/, ''));
			}
		} else {
			// otherwise, check for location.hash
			var current_hash = location.hash;
			if(current_hash != jQuery.history._curHash) {
				jQuery.history._update_location_stack();
				jQuery.history._curHash = current_hash;
				jQuery.history._run_callback(current_hash.replace(/^#/, ''));
			}
		}
	},

	load: function(hash, data) {
		var newhash;

		newhash = '#' + hash;
		location.hash = newhash;
		this._update_location_stack();

		this._curHash = newhash;
		this._stack['url_' + this._hash_prefix + hash] = data.url;
		this._stack['result_ids_' + this._hash_prefix + hash] = data.result_ids;
		this._stack['callback_' + this._hash_prefix + hash] = data.callback;

		if (jQuery.browser.msie && parseInt(jQuery.ua.version) < 8) {
			var ihistory = $("#jQuery_history")[0];
			var iframe = ihistory.contentWindow.document;
			iframe.open();
			iframe.close();
			iframe.location.hash = newhash;
		}
	}
});

$(document).ready(function() {
	jQuery.history = new History(); // singleton instance
	jQuery.history.init(fn_history_callback);
});

})(jQuery);
