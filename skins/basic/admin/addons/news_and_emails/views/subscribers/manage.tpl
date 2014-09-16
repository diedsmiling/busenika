{* $Id: manage.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{script src="js/tabs.js"}

{capture name="mainbox"}

{include file="addons/news_and_emails/views/subscribers/components/subscribers_search_form.tpl" dispatch="subscribers.manage"}

<form action="{""|fn_url}" method="post" name="subscribers_form">

{include file="common_templates/pagination.tpl" save_current_page=true}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="50%">{$lang.email}</th>
	<th width="50%">{$lang.registered}</th>
	<th width="1%">
		<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-subscribers" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-subscribers" /></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$subscribers item="s"}
<tbody class="hover">
<tr>
	<td class="center">
   		<input type="checkbox" name="subscriber_ids[]" value="{$s.subscriber_id}" class="checkbox cm-item" /></td>
	<td><input type="hidden" name="subscribers[{$s.subscriber_id}][email]" value="{$s.email}" />
		<a href="mailto:{$s.email|escape:url}">{$s.email}</a></td>
	<td>
		{$s.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"},&nbsp;{assign var="count" value=$s.mailing_lists|@count}{$lang.subscribed_to|replace:"[num]":$count}
	</td>
	<td class="center nowrap">
		<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_subscribers_{$s.subscriber_id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-subscribers" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_subscribers_{$s.subscriber_id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-subscribers" /><a id="sw_subscribers_{$s.subscriber_id}" class="cm-combination-subscribers">{$lang.extra}</a>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"subscribers.delete?subscriber_id=`$s.subscriber_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$s.subscriber_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
<tr id="subscribers_{$s.subscriber_id}" class="hidden">
	<td>&nbsp;</td>
	<td colspan="6">
		<table cellpadding="5" cellspacing="0" border="0" class="table">
		<tr>
			<th>{$lang.mailing_list}</th>
			<th>{$lang.format}</th>
			<th>{$lang.language}</th>
			<th>{$lang.subscribed}</th>
			<th>{$lang.confirmed}</th>
		</tr>
		{foreach from=$mailing_lists item="list" key="list_id"}
			<tr>
				<td>{$list}</td>
				<td>
					<select name="subscribers[{$s.subscriber_id}][mailing_lists][{$list_id}][format]">
					<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}"  {if $s.mailing_lists[$list_id].format == $smarty.const.NEWSLETTER_FORMAT_TXT}selected="selected"{/if}>{$lang.txt_format}</option>
					<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}" {if $s.mailing_lists[$list_id].format == $smarty.const.NEWSLETTER_FORMAT_HTML}selected="selected"{/if}>{$lang.html_format}</option>
					</select>
				</td>
				<td>
					<select name="subscribers[{$s.subscriber_id}][mailing_lists][{$list_id}][lang_code]">
					{foreach from=$languages item=lng}
					<option value="{$lng.lang_code}" {if $s.mailing_lists[$list_id].lang_code == $lng.lang_code}selected="selected"{/if} >{$lng.name}</option>
					{/foreach}
					</select>
				</td>
				<td class="center">
					<input type="checkbox" name="subscribers[{$s.subscriber_id}][list_ids][]" value="{$list_id}" {if $s.mailing_lists[$list_id]}checked="checked"{/if} class="checkbox cm-item-{$id}"></td>
				<td>
					<input type="hidden" name="subscribers[{$s.subscriber_id}][mailing_lists][{$list_id}][confirmed]" value="0" />
					<input type="checkbox" name="subscribers[{$s.subscriber_id}][mailing_lists][{$list_id}][confirmed]" value="1" {if $s.mailing_lists[$list_id].confirmed}checked="checked"{/if} class="checkbox" />
				</td>
			</tr>
		{foreachelse}
			<tr class="no-items">
				<td colspan="5"><p>{$lang.no_data}</p></td>
			</tr>
		{/foreach}
		</table>
		
	</td>
</tr>
</tbody>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

{include file="pickers/users_picker.tpl" data_id="subscr_user" opts_file="addons/news_and_emails/views/subscribers/components/picker_opts.tpl" extra_var="dispatch=subscribers.add_users&list_id=`$smarty.request.list_id`" but_text=$lang.add_subscribers_from_users view_mode="button"}

<div class="buttons-container buttons-bg">
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[subscribers.delete]" class="cm-process-items cm-confirm" rev="subscribers_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[subscribers.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	
	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_subscribers" text=$lang.add_new_subscribers link_text=$lang.add_subscriber act="general"}
	</div>
</div>

</form>


{capture name="tools"}
{capture name="add_new_picker"}

<form action="{""|fn_url}" method="post" name="subscribers_form_0" class="cm-form-highlight">

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_mailing_list_details_0" class="cm-js cm-active"><a>{$lang.general}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="content_tab_mailing_list_details_0">
	<fieldset>
		<div class="form-field">
			<label for="subscribers_email_0" class="cm-required cm-email">{$lang.email}:</label>
			<input type="text" name="add_subscribers[0][email]" id="subscribers_email_0" value="" class="input-text-large main-input" />
		</div>

		{if $mailing_lists}
		<div class="form-field">
			<label class="cm-required">{$lang.mailing_lists}:</label>
			{html_checkboxes name="add_subscribers[0][list_ids]" options=$mailing_lists columns=3}
		</div>
		{/if}

		<div class="form-field">
			<label for="elm_format_0" class="cm-required">{$lang.format}:</label>
			<select id="elm_format_0" name="add_subscribers[0][format]">
				<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}">{$lang.txt_format}</option>
				<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}" selected="selected">{$lang.html_format}</option>
			</select>
		</div>

		<div class="form-field">
			<label for="elm_lang_0" class="cm-required">{$lang.language}:</label>
			<select id="elm_lang_0" name="add_subscribers[0][lang_code]">
				{foreach from=$languages item="lng"}
					<option value="{$lng.lang_code}">{$lng.name}</option>
				{/foreach}
			</select>
		</div>

		<div class="form-field">
			<label for="elm_conf_0">{$lang.confirmed}:</label>
			<input type="hidden" name="add_subscribers[0][confirmed]" value="0" />
			<input id="elm_conf_0" type="checkbox" name="add_subscribers[0][confirmed]" value="1" class="checkbox" />
		</div>

		<div class="form-field">
			<label for="elm_notify_0">{$lang.notify_user}:</label>
			<input type="hidden" name="add_subscribers[0][notify_user]" value="0" />
			<input id="elm_notify_0" type="checkbox" name="add_subscribers[0][notify_user]" value="1" class="checkbox" />
		</div>

	</fieldset>
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[subscribers.add]" create=true cancel_action="close"}
</div>

</form>

{/capture}
{include file="common_templates/popupbox.tpl" id="add_new_subscribers" text=$lang.add_new_subscribers content=$smarty.capture.add_new_picker link_text=$lang.add_subscriber act="general"}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.subscribers content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools select_languages=true}
