//
// $Id: func.js 6929 2009-02-20 07:01:33Z zeke $
//

function fn_news_and_emails_add_js_item(data)
{
	if (data.var_prefix == 'n') {
		data.append_obj_content = data.object_html.str_replace('{news_id}', data.var_id).str_replace('{news}', data.item_id);
	}
}