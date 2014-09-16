{* $Id: account_update.pre.tpl 8442 2009-12-22 13:18:23Z lexa $ *}

{if $page_mailing_lists}

	{include file="common_templates/subheader.tpl" title=$lang.mailing_lists}

	<p>{$lang.text_signup_for_subscriptions}</p>

	{foreach from=$page_mailing_lists item=list}
		<div class="select-field">
			<input type="hidden" name="mailing_lists[{$list.list_id}]" value="0" />
			<input id="profile_mailing_list_{$list.list_id}" type="checkbox" name="mailing_lists[{$list.list_id}]" value="1" {if $user_mailing_lists[$list.list_id]}checked="checked"{/if} class="checkbox" /><label for="profile_mailing_list_{$list.list_id}">{$list.object}</label>
		</div>
	{/foreach}

	<div class="select-field">
	<select name="newsletter_format">
		<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}" {if $newsletter_format == $smarty.const.NEWSLETTER_FORMAT_TXT}selected="selected"{/if}>{$lang.txt_format}</option>
		<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}" {if $newsletter_format == $smarty.const.NEWSLETTER_FORMAT_HTML}selected="selected"{/if}>{$lang.html_format}</option>
	</select>
	</div>
{/if}