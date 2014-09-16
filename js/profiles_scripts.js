// $Id: profiles_scripts.js 10548 2010-08-30 15:15:09Z 2tl $

jQuery.profiles = {
	rebuild_states : function(section)
	{
		var country_id = $('.cm-country.cm-location-' + section).attr('for');
		var elm = $('#' + $('.cm-state.cm-location-' + section).attr('for')).attr('id');
		var sbox = $('#' + elm).is('select') ? $('#' + elm) : $('#' + elm + '_d');
		var inp = $('#' + elm).is('input') ? $('#' + elm) : $('#' + elm + '_d');
		var cntr = $('#' + country_id);
		if (cntr.length) {
			var cntr_disabled = cntr.is(':disabled');
		} else {
			var cntr_disabled = sbox.is(':disabled');
		}
		var country_code = (cntr.length) ? cntr.val() : default_country;
		var tag_switched = false;
		var pkey = '';

		if (!sbox.length && !inp.length) {
			return false;
		}

		if (states && states[country_code]) { // Populate selectbox with states
			sbox.attr('length', 1);
			for (var k in states[country_code]) {
				pkey = k.str_replace('__', '');
				sbox.append('<option value="' + pkey + '"' + (pkey == default_state[section] ? ' selected' : '') + '>' + states[country_code][k] + '</option>');
			}

			sbox.attr('id', elm).attr('disabled', '').show().removeClass('cm-skip-avail-switch');
			inp.attr('id', elm + '_d').attr('disabled', 'disabled').hide().addClass('cm-skip-avail-switch');

		} else { // Disable states

			sbox.attr('id', elm + '_d').attr('disabled', 'disabled').hide().addClass('cm-skip-avail-switch');
			inp.attr('id', elm).attr('disabled', '').show().removeClass('cm-skip-avail-switch');
		}

		if (cntr_disabled == true) {
			sbox.attr('disabled', 'disabled');
			inp.attr('disabled', 'disabled');
		}

		default_state[section] = (sbox.attr('disabled')) ? inp.val() : sbox.val();
	}
}