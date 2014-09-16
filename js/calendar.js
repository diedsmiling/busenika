// $Id: calendar.js 8453 2009-12-23 10:07:49Z lexa $

function ccal(config)
{
	this.init(config);
}

ccal.prototype = {
	get_date: function(date)
	{
		return {
			month: date.getMonth(),
			year: date.getFullYear(),
			day: date.getDate(),
			wday: (date.getDay() == 0) ? 7 : date.getDay(),
			days: this.get_days(date.getMonth(), date.getFullYear()),
			first_wday: new Date(date.getFullYear(), date.getMonth(), 1).getDay()
		}
	},

	get_months: function(date)
	{
		this.current_month = this.get_date(date);
		this.previous_month = this.get_date(new Date(this.current_month.month == 0 ? this.current_month.year - 1 : this.current_month.year, this.current_month.month == 0 ? 11 : this.current_month.month - 1));
		this.next_month = this.get_date(new Date(this.current_month.month == 11 ? this.current_month.year + 1 : this.current_month.year, this.current_month.month == 11 ? 0 : this.current_month.month + 1));
	},

	handle_events: function(e)
	{
		var elm = $(e.target);

		if (elm.attr('id') == this.button_id) {
			if (elm.hasClass('cm-combo-on')) {
				this.parse_date($('#' + this.date_id).val());
			}
			return true;
		}

		if (elm.parents('#'+this.id).length) {
			if (elm.hasClass('cm-months-list')) {
				this.choose(this.current_month.day, elm.attr('rev'));

			} else if (elm.hasClass('cm-previous-month')) {
				this.choose(this.current_month.day, '-');

			} else if (elm.hasClass('cm-next-month')) {
				this.choose(this.current_month.day, '+');

			} else if (elm.hasClass('cm-previous-days')) {
				this.choose(elm.text(), '-');

			} else if (elm.hasClass('cm-next-days')) {
				this.choose(elm.text(), '+');

			} else if (elm.hasClass('cm-current-days')) {
				this.choose(elm.text(), this.current_month.month, true);
			}
		}
	},

	init: function(config)
	{
		for (var k in config) {
			this[k] = config[k];
		}

		var obj = this;

		$(document).ready(function(){
			$('#' + obj.id).appendTo(document.body);
		});

		// Attach click event collector
		$(document).bind('mouseup', function(e) {obj.handle_events(e); return false;});
	},

	parse_date: function(date)
	{
		var a = date.split('/');
		var m, d, y;
		if (a.length == 3) {
			m = this.month_first ? a[0] : a[1];
			m = (m > 0 && m < 13) ? (m - 1) : 0;

			d = this.month_first ? a[1] : a[0];
			d = (d > 0 && d < 32) ? d : 1;

			y = parseInt((a[2] > 1900 && a[2] < 3000) ? a[2] : 2008);
			if (jQuery.inArray(y, this.years) == -1) {
				var ly = this.years.pop();
				this.years = [];
				for (var i = y; i <= ly; i++) {
					this.years.push(i);
				}
			}

			this.get_months(new Date(y, m, d));
		} else {
			this.get_months(new Date());
		}

		this.display();
	},

	generate_date: function(date)
	{
		var d = date.day < 10 ? '0' + date.day : date.day;
		var m = date.month + 1 < 10 ? '0' + (date.month + 1) : date.month + 1;
		return (this.month_first ? (m + '/' + d) : (d + '/' + m)) + '/' + date.year;
	},

	choose: function(d, m, r)
	{
		if (m == '+') {
			if (this.next_month.year > this.years[this.years.length - 1]) {
				this.years.push(this.next_month.year);
			}
			this.get_months(new Date(this.next_month.year, this.next_month.month, d > this.next_month.days ? this.next_month.days : d));
		} else if (m == '-') {
			if (this.previous_month.year < this.years[0]) {
				this.years.unshift(this.previous_month.year);
			}
			this.get_months(new Date(this.previous_month.year, this.previous_month.month, d > this.previous_month.days ? this.previous_month.days : d));
		} else if (m == 'y+') {
			this.current_month.year++;
			var new_month = this.get_date(new Date(this.current_month.year, this.current_month.month, 1));
			this.get_months(new Date(this.current_month.year, this.current_month.month, d > new_month.days ? new_month.days : d));
		} else if (m == 'y-') {
			this.current_month.year--;
			var new_month = this.get_date(new Date(this.current_month.year, this.current_month.month, 1));
			this.get_months(new Date(this.current_month.year, this.current_month.month, d > new_month.days ? new_month.days : d));
		} else if (m == 'cy') {
			var new_month = this.get_date(new Date(this.current_month.year, d, 1));
			this.current_month.year = d;
			this.get_months(new Date(this.current_month.year, this.current_month.month, this.current_month.day > new_month.days ? new_month.days : this.current_month.day));
		}  else {
			var new_month = this.get_date(new Date(this.current_month.year, m, 1));
			this.get_months(new Date(this.current_month.year, m, d > new_month.days ? new_month.days : d));
		}

		if (r && r == true)    {
			$('#sw_' + this.id).click(); // Hide calendar
			$('#' + this.date_id).val(this.generate_date(this.current_month));
			$('#' + this.date_id).trigger('change');
			return true;
		}

		this.display();
	},

	display: function()
	{
		var months_part_one = '';
		var months_part_two = '';
		var week_days = '';
		var _years = [];
		var container = $('#' + this.id);
		var button = $('#' + this.button_id);

		// Generate year list
		for (var i = 0; i < this.years.length ; i++) {
			_years.push('<option value="' + this.years[i] + '"' + (this.current_month.year == this.years[i] ? ' selected="selected"' : '') + '>' + this.years[i] + '</option>');
		}

		// Generate month list
		for (var i = 0; i <= 5 ; i++) {
			months_part_one += '<li><a class="cm-months-list' + (this.current_month.month == i ? ' selected' : '') + '" rev="' + i + '">' + this.months[i] + '</a></li>';
		}
		for (var i = 6; i <= 11 ; i++) {
			months_part_two += '<li><a class="cm-months-list' + (this.current_month.month == i ? ' selected' : '') + '" rev="' + i + '">' + this.months[i] + '</a></li>';
		}
		week_days += this.sunday_first ? '<th class="weekend">' + this.week_days_name[0] + '</th>' : '';
		for (var i = 1; i < 7 ; i++) {
			week_days += '<th' + (i == 6 ? ' class="weekend"' : '') + '>' + this.week_days_name[i] + '</th>';
		}
		week_days += !this.sunday_first ? '<th class="weekend">' + this.week_days_name[0] + '</th>' : '';

		container.html(
			'<div class="calendar-navig"><a title="Previous month" class="cm-previous-month">&nbsp;</a>' +
			this.current_month.day + ' ' +
			(this.months[this.current_month.month]) + ', ' +
			this.current_month.year +
			'<a title="Next month" class="cm-next-month">&nbsp;</a></div>' +
			'<div class="clear"><table border="0" cellspacing="0" cellpadding="0"><tr><td><div class="float-left"><ul class="float-left">' + months_part_one +
			'</ul><ul>' + months_part_two + '</ul><select class="cm-years-list">' + _years.join('') + '</select></div></td>' +
			'<td><table class="calendar float-left" cellspacing="0" cellpadding="0">' +
			'<tr>' + week_days + '</tr>' +
			this.generate_cells() +
			'</table></td></tr></table></div>');

		// Calculate deviation
		var w = jQuery.get_window_sizes();
		var bp = button.offset(); // button position
		var new_x = bp.left + container.width() > w.offset_x + w.view_width ? bp.left + button.width() - container.width() : bp.left;
		var new_y = bp.top + button.height() + container.height() > w.offset_y + w.view_height ? bp.top - container.height() : bp.top + button.height();
		container.css({'left': new_x + 'px', 'top': new_y + 'px'});
		var obj = this;
		$('.cm-years-list', container).change(function() {
			obj.choose($(this).val(), 'cy');
		});
	},

	generate_cells: function()
	{
		var cells = '<tr>';

		var today = this.get_date(new Date());
		var day = 0;
		var wday = 0;
		var week = 1;
		if (this.current_month.first_wday == 0) {
			this.current_month.first_wday = 7;
		}

		// Generate cells before first day (for previous month)
		var first_wday = this.sunday_first ? this.current_month.first_wday + 1 : this.current_month.first_wday;
		if (first_wday > 7) {
			first_wday = 1;
		}
		for (var k = 1; k < first_wday; k++) {
			cells += '<td class="previous-month-days"><a class="cm-previous-days">' + (this.previous_month.days + 1 - first_wday + k) + '</a></td>';
			wday++;
		}

		// Generate days for the current month
		var weekend_days = this.sunday_first ? [7, 1] : [6, 7];
		var done = false;
		while (done == false) {
			day++;
			wday++;

			cells += '<td class="' + (day == this.current_month.day ? 'selected' : '') + ' cm-current-days' + (jQuery.inArray(wday, weekend_days) != -1 ? ' weekend' : '') + '"><a class="cm-current-days' + (day == today.day && this.current_month.year == today.year && this.current_month.month == today.month ? ' strong' : '') + '">' + day +'</a></td>';
			if (wday == 7) {
				cells += '</tr><tr>';
				wday = 0;
				week++;
			}
			if (day == this.current_month.days) {
				break;
			}
		}

		// Generate cells after last day (for next month)

		for (var k = wday; k < 7 ; k++ ) {
			cells += '<td class="next-month-days"><a class="cm-next-days">' + ( k  - wday + 1) + '</a></td>';
		}
		day = 7 - wday;
		if (week < 6) {
			cells += '</tr><tr>';
			wday = 0;
			for (var k = 0; k < 7 ; k++ ) {
				cells += '<td class="next-month-days"><a class="cm-next-days">' + ( k + day + 1) + '</a></td>';
			}
		}


		// Close the last table row
		if (wday < 7) {
			cells += '</tr>';
		}

		return cells;
	},

	get_days: function(month, year)
	{
		var is_leap = (year % 4 == 0);
		var ma = [31,28,31,30,31,30,31,31,30,31,30,31];
		if (is_leap) {
			ma[1]++;
		}

		return ma[month];
	}
};
