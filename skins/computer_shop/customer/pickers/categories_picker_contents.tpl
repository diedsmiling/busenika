{* $Id: categories_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.categories}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}
{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:"javascript"}';
var display_type = '{$smarty.request.display|escape:javascript}';
{literal}
	function fn_add_js_category(hide, close)
	{
		var d_form = document.forms['categories_form'];
		if(!d_form){
			return false;
		}
		var categories = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					categories[id] = $('#category_' + id).text();
				});
				parent.window.jQuery.add_js_item(categories, 'c', null, hide);
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

<body class="clear-body">
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

<div class="hidden">{include file="common_templates/notification.tpl"}</div>

{if $categories_tree}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="categories_form">
{assign var="level" value="0"}
<div id="category_rows">{include file="views/categories/components/categories_tree_simple.tpl" header="1" form_name="discounted_categories_form" checkbox_name=$smarty.request.checkbox_name|default:"categories_ids" parent_id=$category_id display=$smarty.request.display}</div>

<div class="buttons-container hidden">
{if $smarty.request.extra}
	{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_categories but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="submit"}
{else}
	{if $smarty.request.display == "radio"}
		{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.choose but_meta="cm-no-submit" but_name="submit" but_role="submit" but_onclick="fn_add_js_category(true, false);"}
	{else}
		{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_categories_and_close but_onclick="fn_add_js_category(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}
		{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_categories but_name="submit" but_onclick="fn_add_js_category(false, false);" but_role="submit" but_meta="cm-process-items cm-no-submit"}
	{/if}
{/if}
</div>

</form>
{/if}

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
