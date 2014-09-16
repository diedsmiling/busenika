//
// $Id: func.js 6929 2009-02-20 07:01:33Z zeke $
//

function fn_recurring_billing_add_js_item(data)
{
	if (data.var_prefix == 'r') {
		data.append_obj_content = data.object_html.str_replace('{recurring_plan_id}', data.var_id).str_replace('{recurring_plan}', data.item_id);
	}
}

function fn_recurring_billing_check_exceptions(data)
{
	if (typeof(recurring_plan) != 'undefined' && recurring_plan[data.id]) {
		var price_func = fn_update_product_price;
		var hook_data = {
			'id': data.id,
			'func': price_func
		};
		fn_set_hook('get_price_function', hook_data);
		price_func = hook_data.func;
		for (var id in recurring_plan[data.id]) {
			fn_update_recurring_prices('recurring_price', data.id, id, recurring_plan[data.id][id]['last_price'], price_func);
			fn_update_recurring_prices('start_recurring_price', data.id, id, recurring_plan[data.id][id]['price'], price_func);
		}
		price[data.id] = recurring_plan[data.id][$('#rb_plan_' + data.id).val()]['price'];
		price_func(data.id);
	}
}

function fn_update_recurring_prices(id, prod_id, plan_id, rec_price, price_f)
{
	if (plan_id == 0) {
		return;
	}
	var elm = $('#' + id + '_' + prod_id + '_' + plan_id);
	if (elm.length) {
		price[prod_id] = rec_price;
		price_f(prod_id);
		elm.html(jQuery.formatNum((typeof(update_ids[prod_id]['discounted_price']['P']) != 'undefined' ? update_ids[prod_id]['discounted_price']['P'] : update_ids[prod_id]['discounted_price']['S']), decplaces, false));
	}
	elm = $('#sec_' + id + '_' + prod_id + '_' + plan_id);
	if (elm.length) {
		price[prod_id] = rec_price;
		price_f(prod_id);
		elm.html(jQuery.formatNum(update_ids[prod_id]['discounted_price']['S'], decplaces, false));
	}
}