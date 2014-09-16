{* $Id: update.tpl 10296 2010-08-02 11:16:49Z klerik $ *}

<script type="text/javascript">
	//<![CDATA[
	{literal}
	$(document).ready(function(){
		// template load button
		$("#load_template").click(function(){
			if ($("select[@name='newsletter_data[template]'] option[@selected]").val() != '0') {
				$.ajaxRequest(
					{/literal}
					'{"newsletters.render?template_id="|fn_url:'A':'rel':'&'}' + $("select[@name='newsletter_data[template]'] option[@selected]").val(),
					{literal}
					{
						callback: append_newsletter_template
					}
				)
			}
		});


		// test send ajax form submit
		$("#test_send_button").click(function(){
			var form = $("form[@name='newsletters_form']");
			$.ajaxSubmit(form, $("#test_send_button"));
		});

		// test send ajax form submit
		$("#preview_txt").click(function(){
			var form = $("form[@name='newsletters_form']");
			$.ajaxSubmit(form, $("#test_send_button"));
		});


	});

	function append_newsletter_template(data) {
		$("#man_descr_txt").val($("#man_descr_txt").val() + data['template']['txt']);
		$("#man_descr_html").val($("#man_descr_html").val() + data['template']['html']);
	}

	/*
	function newsletter_preview(type) {
		type = type || 'txt';

		$.ajaxRequest(
			fn_url("newsletters.preview_" + type),
			{
				method: 'post',
				data: { body:  $("#man_descr_" + type).val() },
				callback: open_preview_window
			}
		);
	}


	function open_preview_window(data) {
		w = window.open(fn_url('newsletters.preview_popup'),'product_popup_window','width=600,height=400,toolbar=yes,status=no,scrollbars=yes,resizable=yes,menubar=yes,location=no,direction=no');
		console.log(w, w.document.body.onLoad);
		w.document.body.onLoad = tt;
		//console.log($('#contentu', w.document));
		$(w.document).contents().find('#contentu').text('xxx');
	}*/
	{/literal}
	//]]>
</script>


{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="newsletters_form" class="cm-form-highlight">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="newsletter_id" value="{$newsletter.newsletter_id}" />
<input type="hidden" name="newsletter_data[type]" value="{$newsletter_type}" />
<input type="hidden" name="newsletter_data[type]" value="{$newsletter_type}" />
<input type="hidden" name="dispatch" value="" />

<fieldset>

{notes}
	<ul>
	{foreach from=$placeholders item=p_descr key=p}
		<li>{$p}: {$lang.$p_descr}</li>
	{/foreach}
	</ul>
{/notes}

<div class="form-field">
	<label for="newsletter_subject" class="cm-required">{$lang.subject}:</label>
	<input type="text" name="newsletter_data[newsletter]" id="newsletter_subject" value="{$newsletter.newsletter}" size="40" class="input-text-large main-input" />
</div>

{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
<div class="form-field">
	<label for="newsletter_subject_multiple">{$lang.more_subjects}:</label>
	<textarea name="newsletter_data[newsletter_multiple]" id="newsletter_subject_multiple" class="input-textarea-long">{$newsletter.newsletter_multiple}</textarea>
</div>
{/if}

<div class="form-field">
	<label for="man_descr_txt">{$lang.body_txt}:</label>
	<textarea id="man_descr_txt" name="newsletter_data[body_txt]" cols="35" rows="8" class="input-textarea-long">{$newsletter.body_txt}</textarea>
	<p>
	{include file="buttons/button.tpl" but_text=$lang.preview but_name="dispatch[newsletters.preview_txt]" but_meta="cm-new-window"}
	</p>
</div>

<div class="form-field">
	<label for="man_descr_html">{$lang.body_html}:</label>
	<textarea id="man_descr_html" name="newsletter_data[body_html]" cols="35" rows="8" class="input-textarea-long">{$newsletter.body_html}</textarea>
	{include file="common_templates/wysiwyg.tpl" id="man_descr_html"}
	{include file="buttons/button.tpl" but_text=$lang.preview but_name="dispatch[newsletters.preview_html]" but_meta="cm-new-window"}
</div>

<div class="form-field">
	<label>{$lang.template}:</label>
	<select name="newsletter_data[template]">
		<option value="0">{$lang.no_template}</option>
		{foreach from=$newsletter_templates item=template}
		<option {if $newsletter.template == $template.newsletter_id}selected="selected"{/if} value="{$template.newsletter_id}">{$template.newsletter}</option>
		{/foreach}
	</select>
	&nbsp;{include file="buttons/button.tpl" but_text="`$lang.load`" but_name="dispatch[newsletters.test_send]" but_id="load_template" but_meta="cm-no-submit"}
</div>

{*
<div class="form-field">
	<label>{$lang.delivery_date}:</label>
	{html_select_date field_array="newsletter_data[timestamp]" time=$newsletter.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
</div> *}

{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
	<div class="form-field">
		<label for="newsletter_campaigns">{$lang.campaign}:</label>
		<select name="newsletter_data[campaign_id]" id="newsletter_campaigns">
			<option value="0">{$lang.none}</option>
			{foreach from=$newsletter_campaigns item=campaign}
			<option {if $newsletter.campaign_id == $campaign.campaign_id}selected="selected"{/if} value="{$campaign.campaign_id}">{$campaign.object}</option>
			{/foreach}
		</select>
	</div>
{/if}

{include file="common_templates/select_status.tpl" input_name="newsletter_data[status]" id="newsletter_data" obj=$newsletter items_status="A: `$lang.active`, D: `$lang.disabled`, S: `$lang.sent`"}

</fieldset>

<fieldset>

{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
{include file="common_templates/subheader.tpl" title=$lang.send_to}

<div class="form-field">
	<label>{$lang.mailing_lists}:</label>
	<div class="select-field">

	{foreach from=$mailing_lists item="list"}
	<input type="checkbox" value="{$list.list_id}" name="newsletter_data[mailing_lists][]" {if $list.list_id|in_array:$newsletter.mailing_lists}checked="checked"{/if} /><label>{$list.object}</label>
	{/foreach}
	</div>
</div>

<div class="form-field">
	<label>{$lang.users}:</label>
	{include file="pickers/users_picker.tpl" but_text=$lang.add_recipients_from_users data_id="return_users" input_name="newsletter_data[users]" item_ids=$newsletter.users}
</div>
{/if}


{if $newsletter_type != $smarty.const.NEWSLETTER_TYPE_TEMPLATE}
	<div class="form-field">
		<label for="test_send">{$lang.send_to_test_email}:</label>
		<input class="input-text" type="text" name="test_email" id="test_send" value="" />&nbsp;{include file="buttons/button.tpl" but_text="`$lang.send`" but_name="dispatch[newsletters.test_send]" but_id="test_send_button" but_meta="cm-no-submit"}
	</div>
{/if}

{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
	<div class="form-field">
		<label for="abandoned_type">{$lang.customers_with_abandoned}:</label>
		<select id="abandoned_type" name="newsletter_data[abandoned_type]">
			<option value="cart" {if $newsletter.abandoned_type == "cart"}selected="selected"{/if}>{$lang.cart}</option>
			<option value="wishlist" {if $newsletter.abandoned_type == "wishlist"}selected="selected"{/if}>{$lang.wishlist}</option>
			<option value="both" {if $newsletter.abandoned_type == "both"}selected="selected"{/if}>{$lang.cart_or_wishlist}</option>
		</select>
		{$lang.which_is}
		<input class="input-text-short" type="text" name="newsletter_data[abandoned_days]" value="{$newsletter.abandoned_days}" />
		{$lang.days_old}
	</div>
{/if}

{if $newsletter_links}
<div class="form-field">
	<label>{$lang.clicks}:</label>
	<table class="table">
		<tr>
			<th>{$lang.url}</th>
			<th>{$lang.campaign}</th>
			<th>{$lang.clicks}</th>
		</tr>
	{foreach from=$newsletter_links item=link}
		<tr>
			<td>{$link.url}</td>
			<td>{$newsletter_campaigns[$link.campaign_id].object}</td>
			<td>{$link.clicks}</td>
		</tr>
	{/foreach}
	</table>
</div>
{/if}

</fieldset>

<div class="buttons-container buttons-bg">
	{if $newsletter_type == $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[newsletters.send]" rev="newsletters_form">{$lang.save_and_send}</a></li>
		</ul>
		{/capture}
	{/if}
	{if $mode == "add"}
		{include file="buttons/create.tpl" but_name="dispatch[newsletters.$mode]" but_role="button_main"}
	{else}
		{include file="buttons/save.tpl" but_name="dispatch[newsletters.$mode]" but_role="button_main"}
	{/if}
	{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
</div>

</form>
{/capture}
{if $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_NEWSLETTER}
	{assign var="object_name" value=$lang.newsletter|lower}
{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_TEMPLATE}
	{assign var="object_name" value=$lang.newsletter_template|lower}
{elseif $newsletter_type ==  $smarty.const.NEWSLETTER_TYPE_AUTORESPONDER}
	{assign var="object_name" value=$lang.newsletter_autoresponder|lower}
{/if}


{if $mode == "add"}
	{include file="common_templates/mainbox.tpl" title="`$lang.new` `$object_name`" content=$smarty.capture.mainbox select_languages=true}
{else}
	{include file="common_templates/mainbox.tpl" title="`$lang.editing` `$object_name`:&nbsp;`$newsletter.newsletter`" content=$smarty.capture.mainbox select_languages=true}
{/if}

