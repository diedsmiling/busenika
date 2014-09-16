{* $Id: users_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.users}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}
{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:javascript}';
{literal}
	function fn_add_js_users(hide, close)
	{
		var d_form = document.forms['add_users_form'];
		if(!d_form){
			return false;
		}
		var users = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					var item = $(this).parent().siblings();
					users[id] = {'email': item.find('.user-email').text(), 'user_name': item.find('.user-name').text()};
				});
				parent.window.jQuery.add_js_item(users, 'u', null, hide);
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

{include file="views/profiles/components/profiles_scripts.tpl"}

{include file="views/profiles/components/users_search_form.tpl" dispatch="profiles.picker" discount_id=$discount.discount_id user_type="C" extra="<input type=\"hidden\" name=\"display\" value=\"`$smarty.request.display`\" /><input type=\"hidden\" name=\"extra\" value=\"`$smarty.request.extra`\" />"}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="add_users_form">

{include file="common_templates/pagination.tpl" save_current_page=true}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%" class="center">
		{if $smarty.request.display == "checkbox"}
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
		{/if}
	<th>{$lang.id}</th>
	{if $settings.General.use_email_as_login != "Y"}
	<th>{$lang.username}</th>
	{/if}
	<th>{$lang.email}</th>
	<th>{$lang.name}</th>
	<th>{$lang.registered}</th>
	<th>{$lang.type}</th>
	<th>{$lang.active}</th>
</tr>
{foreach from=$users item=user}
<tr {cycle values=",class=\"table-row\""}>
	<td class="center">
		{if $smarty.request.display == "checkbox"}
		<input type="checkbox" name="add_users[]" value="{$user.user_id}" class="checkbox cm-item" />
		{elseif $smarty.request.display == "radio"}
		<input type="radio" name="selected_user_id" value="{$user.user_id}" checked="checked" class="radio" />
		{/if}
	</td>
	<td>{$user.user_id}</td>
	{if $settings.General.use_email_as_login != "Y"}
	<td><strong>{$user.user_login}</strong></td>
	{/if}
	<td><strong class="user-email">{$user.email}</strong></td>
	<td><span class="user-name">{if $user.firstname || $user.lastname}{$user.firstname} {$user.lastname}{else}-{/if}</span></td>
	<td>{$user.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>{if $user.user_type == "A"}{$lang.administrator}{elseif $user.user_type == "C"}{$lang.customer}{elseif $user.user_type == "S"}{$lang.supplier}{elseif $user.user_type == "P"}{$lang.affiliate}{/if}</td>
	<td class="center"><img src="{$images_dir}/checkbox_{if $user.active == "N"}un{/if}ticked.gif" width="13" height="13" alt="{$user.active}" /></td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="{if $settings.General.use_email_as_login != "Y"}8{else}7{/if}"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container hidden">
	{if $smarty.request.extra}
		{if $smarty.request.display == "checkbox"}
			{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_users but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="button_main"}
		{elseif $smarty.request.display == "radio"}
			{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.choose but_meta="cm-parent-window" but_name="submit" but_role="button_main"}
		{/if}
	{else}
		{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_users but_name="submit" but_onclick="fn_add_js_users(false, false);" but_role="button_main" but_meta="cm-process-items cm-no-submit"}
		{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_users_and_close but_onclick="fn_add_js_users(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}
	{/if}
</div>
</form>

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
