{* $Id: news_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
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
{literal}
	function fn_add_js_news(hide, close)
	{
		var d_form = document.forms['news_form'];
		if(!d_form){
			return false;
		}
		var news = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					news[id] = $('#news_' + id).text();
				});
				parent.window.jQuery.add_js_item(news, 'n', null, hide);
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
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="news_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" />
	</th>
	<th>{$lang.news}</th>
</tr>

{foreach from=$news item=n}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="{$smarty.request.checkbox_name|default:"news_ids"}[]" value="{$n.news_id}" class="checkbox cm-item" />
	</td>
	<td width="100%" id="news_{$n.news_id}">{$n.news}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="2"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{if $news}
<div class="buttons-container hidden">
	{if $smarty.request.extra}
		{include file="buttons/button.tpl" but_text=$lang.add_news but_id="add_item" but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="button_main"}
	{else}
		{include file="buttons/button.tpl" but_id="add_item" but_name="submit" but_text=$lang.add_news but_onclick="fn_add_js_news(false, false);" but_role="button_main" but_meta="cm-no-submit cm-process-items"}
		{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_news_and_close but_onclick="fn_add_js_news(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}
	{/if}
</div>
{/if}

</form>

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
