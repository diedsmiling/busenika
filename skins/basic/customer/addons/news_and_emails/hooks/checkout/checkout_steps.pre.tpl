{* $Id: checkout_steps.pre.tpl 10284 2010-07-30 09:08:26Z angel $ *}

{if $page_mailing_lists}

	{include file="common_templates/subheader.tpl" title=$lang.mailing_lists}

	<p>{$lang.text_signup_for_subscriptions}</p>
	
	{foreach from=$page_mailing_lists item=list}
		<div class="select-field">
			<input type="hidden" name="mailing_lists[{$list.list_id}]" value="0" />
			<label><input type="checkbox" name="mailing_lists[{$list.list_id}]" value="1" {if $user_mailing_lists[$list.list_id]}checked="checked"{/if} class="checkbox" />{$list.object}</label>
		</div>
	{/foreach}
	
	<p><select name="newsletter_format">
		<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}" {if $newsletter_format == $smarty.const.NEWSLETTER_FORMAT_TXT}selected="selected"{/if}>{$lang.txt_format}</option>
		<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}" {if $newsletter_format == $smarty.const.NEWSLETTER_FORMAT_HTML}selected="selected"{/if}>{$lang.html_format}</option>
	</select></p>

{/if}