//
// $Id: func.js 8712 2010-01-28 08:43:20Z alexions $
//

function fn_buy_together_get_price_schema(chain_id)
{
	var result = {};
	var prices = {};
	var total_price = 0;
	
	elms = $('div#content_tab_products_' + chain_id);
	
	$('.cm-chain-' + chain_id, elms).each(function(){
		var elm_id = $(this).val();
		
		if (elm_id != '{bt_id}') {
			prices[elm_id] = {};
			prices[elm_id]['amount'] = $('[name*=amount]', $(this).parent().parent()).val();
			
			if (!isNaN(parseInt(prices[elm_id]['amount']))) {
				prices[elm_id]['amount'] = parseInt(prices[elm_id]['amount']);
			} else {
				prices[elm_id]['amount'] = 0;
			}
			
			prices[elm_id]['price'] = parseFloat($('#item_price_bt_' + chain_id + '_' + elm_id, elms).val());
			prices[elm_id]['modifier'] = parseFloat($('#item_modifier_bt_' + chain_id + '_' + elm_id, elms).val());
			
			if (isNaN(prices[elm_id]['modifier'])) {
				prices[elm_id]['modifier'] = 0;
			}
			
			prices[elm_id]['modifier_type'] = $('#item_modifier_type_bt_' + chain_id + '_' + elm_id, elms).val();
			
			total_price += prices[elm_id]['price'] * prices[elm_id]['amount'];
		}
	});
	
	
	result['price_schema'] = prices;
	result['total_price'] = total_price;
	
	return result;
}

function fn_buy_together_apply_discount(chain_id)
{
	var global_discount = 0;
	
	elms = $('div#content_tab_products_' + chain_id);
	
	global_discount = parseFloat($('#global_discount_' + chain_id, elms).val());
	
	if (isNaN(global_discount)) {
		return false;
	}
	
	var prices = {};
	var total_price = 0;
	var discounted_price = 0;
	
	price_schema = fn_buy_together_get_price_schema(chain_id);

	prices = price_schema['price_schema'];
	total_price = price_schema['total_price'];
	
	if (global_discount > total_price) {
		global_discount = total_price;
		$('#global_discount_' + chain_id, elms).val(total_price);
	}
	
	for (i in prices) {
		discount = prices[i]['price'] / total_price * global_discount;
		discount = discount.toFixed(2);
		item_price = prices[i]['price'] - discount;
		item_price = item_price.toFixed(2);
		
		$('#item_modifier_bt_' + chain_id + '_' + i, elms).val(discount);
		$('#item_modifier_type_bt_' + chain_id + '_' + i, elms).val('by_fixed');
		$('[id*=item_display_price_bt_' + chain_id + '_' + i + '_]', elms).text(prices[i]['price'].toFixed(2));
		$('[id*=item_discounted_price_bt_' + chain_id + '_' + i + '_]', elms).text(item_price);
		
		discounted_price += item_price * prices[i]['amount'];
	}
	
	$('[id*=total_price_' + chain_id + ']', elms).text(total_price.toFixed(2));
	$('[id*=price_for_all_' + chain_id + ']', elms).text(discounted_price.toFixed(2));
}

function fn_buy_together_recalculate(chain_id)
{
	var prices = {};
	var total_price = 0;
	var discounted_price = 0;
	
	elms = $('div#content_tab_products_' + chain_id);
	
	price_schema = fn_buy_together_get_price_schema(chain_id);

	prices = price_schema['price_schema'];
	total_price = price_schema['total_price'];
	
	for (i in prices) {
		switch(prices[i]['modifier_type']) {
		case 'to_fixed':
			item_price = prices[i]['modifier'];
			break;
			
		case 'by_fixed':
			item_price = prices[i]['price'] - prices[i]['modifier'];
			break;
			
		case 'to_percentage':
			item_price = (prices[i]['modifier'] / 100) * prices[i]['price'];
			break;
			
		case 'by_percentage':
			item_price = prices[i]['price'] - (prices[i]['modifier'] / 100) * prices[i]['price'];
			break;
			
		default:
			item_price = prices[i]['price'];
		}
		
		if (item_price < 0) {
			item_price = 0;
		}
		
		item_price = item_price.toFixed(2);
		discounted_price += item_price * prices[i]['amount'];
		
		$('[id*=item_display_price_bt_' + chain_id + '_' + i + '_]', elms).text(prices[i]['price'].toFixed(2));
		$('[id*=item_discounted_price_bt_' + chain_id + '_' + i + '_]', elms).text(item_price);
	}
	
	$('[id*=price_for_all_' + chain_id + ']', elms).text(discounted_price.toFixed(2));
	$('[id*=total_price_' + chain_id + ']', elms).text(total_price.toFixed(2));
	
	// Clear global discount field
	$('#global_discount_' + chain_id, elms).val('');
}

function fn_buy_together_share_discount(evt, chain_id)
{
	if (evt.keyCode) {
		code = evt.keyCode;
	} else if (evt.which) {
		code = evt.which;
	}

	if (code == 13) {
		fn_buy_together_apply_discount(chain_id);
	}
	
	return false;
}

// Hook add_js_item
function fn_buy_together_add_js_item(data)
{
	if (data['var_prefix'] == 'p') {
		price = parseFloat(data.item_id.price);
		if (isNaN(price)) {
			price = 0;
		}
		
		data['append_obj_content'] = data['append_obj_content'].str_replace('{bt_id}', data['item_id']['product_id']).str_replace('{price}', price);
		
		// Price replacement
		price_re = /<span id="(.*?)price_bt(.*?)">(.*?)<\/span>/ig;
		data['append_obj_content'] = data['append_obj_content'].replace(price_re, '<span id="$1price_bt$2">' + price.toFixed(2) + '<\/span>');
		
	}
}

// Hook add_js_item
function fn_buy_together_transfer_js_items(data)
{
	for (id in data) {
		data[id].price = $('#price_' + id).val();
	}
}