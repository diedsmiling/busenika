{* $Id: subscribe.tpl 9783 2010-06-10 10:24:09Z lexa $ *}
{** block-description:mailing_lists **}

{if $mailing_lists}
<form action="{""|fn_url}" method="post" name="subscribe_form">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<p>{$lang.text_signup_for_subscriptions}</p>
{foreach from=$mailing_lists item=list}
	<div class="select-field">
		<label for="mailing_list_{$block.block_id}{$list.list_id}">
			<input id="mailing_list_{$block.block_id}{$list.list_id}" type="checkbox" class="checkbox" name="mailing_lists[{$list.list_id}]" value="1" />{$list.object}
		</label>
	</div>
{/foreach}
<div class="select-field">
	<select name="newsletter_format" id="newsletter_format{$block.block_id}">
		<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}">{$lang.txt_format}</option>
		<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}">{$lang.html_format}</option>
	</select>
</div>
{strip}
<div class="form-field">
	<label for="subscr_email{$block.block_id}" class="cm-required cm-email hidden">{$lang.email}</label>
	<input type="text" name="subscribe_email" id="subscr_email{$block.block_id}" size="20" value="{$lang.enter_email|escape:html}" class="input-text cm-hint" />
	{include file="buttons/go.tpl" but_name="newsletters.add_subscriber" alt=$lang.go}
</div>
{/strip}
</form>
{/if}
