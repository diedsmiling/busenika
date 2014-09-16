{* $Id: recurring_plans_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.rb_recurring_plans}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}
{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:"javascript"}';
{literal}
	function fn_add_js_recurring_plan(hide, close)
	{
		var d_form = document.forms['recurring_plans_form'];
		if(!d_form){
			return false;
		}
		var recurring_plans = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					recurring_plans[id] = $('#recurring_plan_' + id).text();
				});
				parent.window.jQuery.add_js_item(recurring_plans, 'r', null, hide);
			}

			jQuery.showNotifications({'notification': {'type': 'N', 'title': lang.notice, 'message': lang.text_items_added, 'save_state': false}});
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
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="recurring_plans_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" /></th>
	<th>{$lang.rb_recurring_plan}</th>
</tr>
{foreach from=$recurring_plans item=recurring_plan}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="{$smarty.request.checkbox_name|default:"recurring_plans_ids"}[]" value="{$recurring_plan.plan_id}" class="checkbox cm-item" /></td>
	<td id="recurring_plan_{$recurring_plan.plan_id}" width="100%">{$recurring_plan.name}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="2"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{if $recurring_plans}
<div class="buttons-container hidden">
	{if $smarty.request.extra}
		<div class="float-left">{include file="buttons/button.tpl" but_id="add_item" but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="button_main"}</div>
	{else}
		<div class="float-left">{include file="buttons/button.tpl" but_id="add_item" but_name="submit" but_onclick="fn_add_js_recurring_plan(false, false);" but_role="button_main" but_meta="cm-process-items cm-no-submit"}</div>
		<div class="float-left">{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_onclick="fn_add_js_recurring_plan(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}</div>
	{/if}
</div>
{/if}

</form>

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
