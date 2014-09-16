// $Id: node_cloning.js 9041 2010-03-10 11:57:19Z lexa $

jQuery.fn.extend({
	//
	// Adds the tag
	//
	// @level - level in variable name that should be replaced
	// @clone - if set, the field values will be copied
	// E.g. (replace on '30')
	// level = 1, varname = data[20][sub][50] - after replacement data[30][sub][50]
	// level = 3, varname = data[20][sub][50] - after replacement data[20][sub][30]

	cloneNode: function(level, clone, before)
	{
		var before = before || false;
		var clone = clone || false;

		var self = $(this);
		var regex = new RegExp('((?:\\[\\w+\\]){' + (level - 1) + '})\\[(\\d+)\\]');
		var image_regex = new RegExp('((?:\\[\\w+\\]){0})\\[(\\d+)\\]');

		if (window['_counter']) {
			window['_counter']++;
		} else {
			window['_counter'] = 1;
		}
		new_id = self.attr('id') + '_' + window['_counter'];

		var new_node = self.clone();
		new_node.attr('id', new_id);

		$('select', new_node).each(function(ind) { // copy values of selectboxes
			$(this).val($('select', self).eq(ind).val());
		});

		$('textarea', new_node).each(function(ind) { // copy values of textareas
			$(this).val($('textarea', self).eq(ind).val());
		});

		// Remove all script tags
		$('script', new_node).remove();

		// Remove all picker tags
		$('.cm-picker', new_node).remove();

		// Correct Ids
		$('[id],[for]', new_node).each(function() {
			var self = $(this);
			var attr = self.attr('id') ? 'id' : 'for';
			var id = self.attr(attr);
			if (self.is('select') && clone == true) { // hm, for some reason, selectbox value doesn't clone in FF
				self.val($('#' + id).val());
			}
			// calendar cloning
			if (self.is('.cm-calendar-value') && self.attr('id') && typeof(calendars_list) != 'undefined' && calendars_list[self.attr('id')]) {
				var cur_cal = {};
				for (var i in calendars_list[self.attr('id')]) {
					if (!jQuery.isFunction(calendars_list[self.attr('id')][i])) {
						cur_cal[i] = calendars_list[self.attr('id')][i];
					}
				};
				cur_cal.date_id += '_' + window['_counter'];
				cur_cal.button_id += '_' + window['_counter'];
				var cur_picker = $('#' + cur_cal.id).clone().appendTo(document.body);
				cur_cal.id += '_' + window['_counter'];
				cur_picker.attr('id', cur_cal.id);
				calendars_list[cur_cal.date_id] = new ccal(cur_cal);
			}
			self.attr(attr, id + '_' + window['_counter']);
		});

		// Update elements
		$('[name]', new_node).each(function() {
			var self = $(this);
			var name = self.attr('name');
			var it = 0;
			var matches = name.match(/(\[\d+\]+)/g);

			// Increment array index
			if (matches) {
				name = name.replace(self.hasClass('cm-image-field') ? image_regex : regex, '$1[#HASH#]'); // Magic... parseInt does not work for $2 in replace method...
				self.attr('name', name.str_replace('#HASH#', parseInt(RegExp.$2) + window['_counter']));
			}

			// Set default values
			if (clone == false) {
				if (self.is(':checkbox,:radio')) {
					self.attr('checked', self.get(0).defaultChecked ? 'checked' : '');
				} else if (self.is(':input') && self.attr('type') != 'hidden') {
					if (self.attr('name') != 'submit') {
						self.val('');
					}
				}
			}

			// Display enabled remove button
			if (name == 'remove') {
				self.addClass('hidden');
				self.next().removeClass('hidden');
			}
		});

		// magic increment for checkbox element classes like add-0 -> add-1 (to fix check_all microformat work)
		$(':checkbox[class]', new_node).each(function() {
			if (this.name == 'check_all') {
				var m = this.className.match(/cm-check-items-([\w]*)-(\d+)/);
				$(this).removeClass('cm-check-items-' + m[1] + '-' + m[2]).addClass('cm-check-items-' + m[1] + '-' + (parseInt(m[2]) + window['_counter']));

				$(':checkbox.cm-item-' + m[1] + '-' + m[2], new_node).each(function() {
					$(this).removeClass('cm-item-' + m[1] + '-' + m[2]).addClass('cm-item-' + m[1] + '-' + (parseInt(m[2]) + window['_counter']));
				});

				return false;
			}
		});

		// Insert node into the document
		if (before == true) {
			self.before(new_node);
		} else {
			self.after(new_node);
		}

		// if node has file uploader, process it
		$('[id^=clean_selection]', new_node).each(function() {

			var type_id = this.id.str_replace('clean_selection', 'type');
			if ($('#' + type_id).val() == 'local' || clone == false){
				fileuploader.clean_selection(this.id);
			}
		});

		return new_id;
	},

	//
	// Remove the tag
	//
	removeNode: function()
	{
		var self = $(this);
		if (!self.prev().length || self.hasClass('cm-first-sibling')) {
			return false;
		}

		self.remove();
	}
});
