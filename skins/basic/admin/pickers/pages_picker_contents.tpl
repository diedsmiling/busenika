{* $Id: pages_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.pages}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}
{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:javascript}';
var display_type = '{$smarty.request.display|escape:javascript}';
{literal}
	function fn_add_js_page(hide, close)
	{
		var d_form = document.forms['pages_form'];
		if(!d_form){
			return false;
		}
		var pages = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					pages[id] = $('#page_title_' + id).text();
				});
				parent.window.jQuery.add_js_item(pages, 'a', null, hide);
			}

			if (display_type != 'radio') {
				jQuery.showNotifications({'notification': {'type': 'N', 'title': lang.notice, 'message': lang.text_items_added, 'save_state': false}});
			}
		}
	}
{/literal}
//]]>
</script>
{/if}
</head>

<body class="picker-body">
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

<div class="hidden">{include file="common_templates/notification.tpl"}</div>

{include file="views/pages/components/pages_search_form.tpl" dispatch="pages.picker" extra="<input type=\"hidden\" name=\"display\" value=\"`$smarty.request.display`\" /><input type=\"hidden\" name=\"extra\" value=\"`$smarty.request.extra`\" /><input type=\"hidden\" name=\"checkbox_name\" value=\"`$smarty.request.checkbox_name`\" /><input type=\"hidden\" name=\"except_id\" value=\"`$except_id`\" />"}

{if $pages_tree}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="pages_form">

	{include file="common_templates/pagination.tpl"}

	<div class="items-container multi-level">
		{include file="views/pages/components/pages_tree.tpl" header=true picker=true checkbox_name=$smarty.request.checkbox_name hide_delete_button=true display=$smarty.request.display dispatch="pages.picker"}
	</div>

	{include file="common_templates/pagination.tpl"}

	<div class="buttons-container hidden">
	{if $smarty.request.extra}
		{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_pages but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="button_main"}
	{else}
		{if $smarty.request.display == "radio"}
			{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.choose but_meta="cm-no-submit" but_name="submit" but_role="submit" but_onclick="fn_add_js_page(true, false);"}
		{else}
			{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_page but_name="submit" but_onclick="fn_add_js_page(false, false);" but_role="button_main" but_meta="cm-process-items cm-no-submit"}
			{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_pages_and_close but_onclick="fn_add_js_page(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}
		{/if}
	{/if}
	</div>
</div>
</form>
{else}
<div class="items-container"><p class="no-items">{$lang.no_data}</p></div>
{/if}

</body>

</html>
