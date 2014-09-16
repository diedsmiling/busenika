//
// $Id: func.js 9141 2010-03-23 13:22:48Z alexions $
//

var points = {};

// This function calculates the new product (earned && price) points and updates values
function fn_reward_points_check_exceptions(data)
{
	id = data.id;
	if (exclude_from_calculate[id] || !points[id]) {
		return false;
	}

	var delta = 0;
	var modifiers = [];
	var qty = document.getElementById('amount_' + id) ? parseInt(document.getElementById('amount_' + id).value) : 1;

	for (i in pr_o[id]) {
		if (!document.getElementById(pr_o[id][i]['id']) || !pr_o[id][i]['pm']) {
			continue;
		}

		modifiers[i] = pr_o[id][i]['pm'][pr_o[id][i]['selected_value']];
		if (typeof(modifiers[i]) == 'undefined') {
			continue;
		}
		if (modifiers[i].substring(0, 1) == 'A') {
			delta += parseFloat(jQuery.formatPrice(modifiers[i].substring(1, modifiers[i].length-1), decplaces));
		} else if(modifiers[i].substring(0, 1)  == 'P') {
			delta += parseFloat(jQuery.formatPrice(points[id]['reward'] * parseFloat(modifiers[i].substring(1, modifiers[i].length-1))/100, decplaces));
		}
	}

	if (document.getElementById('reward_points_' + id) && points[id]['reward']) {
		// Points calculation is based on the original price
		if (points_with_discounts == 'Y' && pr_d[id] && (pr_d[id]['A'] || pr_d[id]['P'])) {
			var amount_for_points = (parseFloat(update_ids[id]['original_price']['P'])) ? (update_ids[id]['original_price']['P'] - update_ids[id]['discount_value']['P']) : 0;
		} else {
			var amount_for_points = parseFloat(update_ids[id]['original_price']['P']);
		}
		var pamount = (points[id]['amount_type'] && points[id]['amount_type'] == 'P') ? amount_for_points * points[id]['pure_amount'] / 100 : points[id]['pure_amount'] * amount_for_points/update_ids[id]['original_price']['P'];
		var reward = Math.round(parseFloat(pamount) + parseFloat(delta));
		reward = Math.round(parseInt(qty) * reward);
		
		var span_reward_points = document.getElementById('reward_points_' + id);
		span_reward_points.innerHTML = reward;

		var div_reward_points = $(span_reward_points).parent();
		if (reward) {
			$(div_reward_points).show();
		} else {
			$(div_reward_points).hide();
		}
	}
	if (document.getElementById('price_in_points_' + id) && points[id]['per']) {
		var subtotal = (price_in_points_with_discounts == 'Y' && pr_d[id] && (pr_d[id]['A'] || pr_d[id]['P'])) ? update_ids[id]['product_subtotal']['P'] : qty * update_ids[id]['original_price']['P'];
		document.getElementById('price_in_points_' + id).innerHTML = Math.round(points[id]['per'] * subtotal);
	}
	return true;
}