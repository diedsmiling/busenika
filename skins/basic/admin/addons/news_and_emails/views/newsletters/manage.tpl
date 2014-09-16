{* $Id: manage.tpl 10298 2010-08-02 14:31:56Z imac $ *}

{if $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
	{assign var="object_names" value=$lang.newsletters}
{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_TEMPLATE}
	{assign var="object_names" value=$lang.newsletter_templates}
{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_AUTORESPONDER}
	{assign var="object_names" value=$lang.newsletter_autoresponders}
{/if}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="newsletters_form" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="newsletter_type" value="{$newsletter_type}" />

{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" /></th>
	<th>{$lang.subject}</th>
	{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
	{*<th>{$lang.status}</th>*}
	<th>{$lang.date}</th>
	{/if}
	<th width="100%">&nbsp;</th>
</tr>
{foreach from=$newsletters item=newsletter}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="newsletter_ids[]" value="{$newsletter.newsletter_id}" class="checkbox cm-item" /></td>
	<td >
		<input type="text" name="newsletters[{$newsletter.newsletter_id}][newsletter]" value="{$newsletter.newsletter}" size="20" class="input-text" /></td>

	{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
		{*<td>
			{include file="common_templates/select_popup.tpl" id=$newsletter.newsletter_id status=$newsletter.status items_status="A: `$lang.active`, D: `$lang.disabled`, S: `$lang.sent`" object_id_name="newsletter_id" table="newsletters"}
		</td>*}

		<td class="center nowrap">
			{if $newsletter.sent_date}
			{$newsletter.sent_date|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
			{else}
			&nbsp;-&nbsp;
			{/if}
		</td>
	{/if}

	<td class="nowrap right">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"newsletters.delete?newsletter_id=`$newsletter.newsletter_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$newsletter.newsletter_id tools_list=$smarty.capture.tools_items href="newsletters.update?newsletter_id=`$newsletter.newsletter_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}5{else}3{/if}"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $newsletters}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[newsletters.delete]" class="cm-process-items cm-confirm" rev="newsletters_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[newsletters.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	<div class="float-right">
	{if $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_NEWSLETTER`" prefix="bottom" hide_tools="true" link_text=$lang.add_newsletter}
	{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_TEMPLATE}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_TEMPLATE`" prefix="bottom" hide_tools="true" link_text=$lang.add_template}
	{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_AUTORESPONDER}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_AUTORESPONDER`" prefix="bottom" hide_tools="true" link_text=$lang.add_autoresponder}
	{/if}
</div>
</div>
</form>

{capture name="tools"}
	{if $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_NEWSLETTER`" prefix="top" hide_tools="true" link_text=$lang.add_newsletter}
	{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_TEMPLATE}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_TEMPLATE`" prefix="top" hide_tools="true" link_text=$lang.add_template}
	{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_AUTORESPONDER}
		{include file="common_templates/tools.tpl" tool_href="newsletters.add?type=`$smarty.const.NEWSLETTER_TYPE_AUTORESPONDER`" prefix="top" hide_tools="true" link_text=$lang.add_autoresponder}
	{/if}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$object_names content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
