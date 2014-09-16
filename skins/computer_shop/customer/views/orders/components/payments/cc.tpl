{* $Id: cc.tpl 10268 2010-07-29 11:32:59Z angel $ *}

{if $card_id}
	{assign var="id_suffix" value="_`$card_id`"}
{else}
	{assign var="id_suffix" value=""}
{/if}
{assign var="card_item" value=$card_data|default:$cart.payment_info}

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr valign="top">
	<td>
		<div class="form-field">
			<label for="cc_type{$id_suffix}" class="cm-required cm-cc-type">{$lang.select_card}:</label>
			<select id="cc_type{$id_suffix}" name="payment_info[card]" onchange="fn_check_cc_type(this.value, '{$id_suffix}');">
				{foreach from=$credit_cards item="c"}
					<option value="{$c.param}" {if $card_item.card == $c.param}selected="selected"{/if}>{$c.descr}</option>
				{/foreach}
			</select>
		</div>
		
		<div class="form-field">
			<label for="cc_number{$id_suffix}" class="cm-required cm-custom (validate_cc)">{$lang.card_number}:</label>
			<input id="cc_number{$id_suffix}" size="35" type="text" name="payment_info[card_number]" value="{$card_item.card_number}" class="input-text" autocomplete="off" />
		</div>

		<div class="form-field">
			<label for="cc_name{$id_suffix}" class="cm-required">{$lang.cardholder_name}:</label>
			<input id="cc_name{$id_suffix}" size="35" type="text" name="payment_info[cardholder_name]" value="{$card_item.cardholder_name}" class="input-text" />
		</div>

		<div class="form-field hidden" id="display_start_date{$id_suffix}">
			<label class="cm-required">{$lang.start_date}:</label>
			<label for="cc_start_month{$id_suffix}" class="hidden cm-required cm-custom (check_cc_date)">{$lang.month}:</label><label for="cc_start_year{$id_suffix}" class="hidden cm-required cm-custom (check_cc_date)">{$lang.year}:</label>
			<input type="text" id="cc_start_month{$id_suffix}" name="payment_info[start_month]" value="{$card_item.start_month}" size="2" maxlength="2" class="input-text-short" />&nbsp;/&nbsp;<input type="text" id="cc_start_year{$id_suffix}" name="payment_info[start_year]" value="{$card_item.start_year}" size="2" maxlength="2" class="input-text-short" />&nbsp;(mm/yy)
		</div>

		<div class="form-field">
			<label class="cm-required">{$lang.expiry_date}:</label>
			<label for="cc_exp_month{$id_suffix}" class="hidden cm-required cm-custom (check_cc_date)">{$lang.month}:</label><label for="cc_exp_year{$id_suffix}" class="hidden cm-required cm-custom (check_cc_date)">{$lang.year}:</label>
			<input type="text" id="cc_exp_month{$id_suffix}" name="payment_info[expiry_month]" value="{$card_item.expiry_month}" size="2" maxlength="2" class="input-text-short" />&nbsp;/&nbsp;<input type="text" id="cc_exp_year{$id_suffix}" name="payment_info[expiry_year]" value="{$card_item.expiry_year}" size="2" maxlength="2" class="input-text-short" />&nbsp;(mm/yy)
		</div>

		<div class="form-field hidden" id="display_cvv2{$id_suffix}">
			<label for="cc_cvv2{$id_suffix}" class="cm-required cm-integer">{$lang.cvv2}:</label>
			<input id="cc_cvv2{$id_suffix}" type="text" name="payment_info[cvv2]" value="{$card_item.cvv2}" size="4" maxlength="4" class="input-text-short" disabled="disabled" autocomplete="off" />
			{capture name="cvv2_info"}
				{include file="views/orders/components/payments/cvv2_info.tpl"}
			{/capture}
			{include file="common_templates/help.tpl" text=$lang.cvv2_code content=$smarty.capture.cvv2_info id="cvv2_`$card_id`" link_text=$lang.what_is_cvv2 link_meta="lowercase" link_only=true}
		</div>

		<div class="form-field hidden" id="display_issue_number{$id_suffix}">
			<label for="cc_issue_number{$id_suffix}" class="cm-integer">{$lang.issue_number}:</label>
			<input id="cc_issue_number{$id_suffix}" type="text" name="payment_info[issue_number]" value="{$card_item.issue_number}" size="2" maxlength="2" class="input-text-short" disabled="disabled" autocomplete="off" />&nbsp;{$lang.if_printed_on_your_card}
		</div>
	</td>
	<td>
		{foreach from=$credit_cards item="c" name="credit_card"}
			{if $c.icon}
				{if $smarty.foreach.credit_card.first}
					{assign var="img_class" value="cm-cc-item"}
				{else}
					{assign var="img_class" value="cm-cc-item hidden"}
				{/if}
				{include file="common_templates/image.tpl" images=$c.icon class=$img_class obj_id="`$c.param``$id_suffix`" object_type="credit_card"}
			{/if}
		{/foreach}
	</td>
</tr>
</table>

<script type="text/javascript" class="cm-ajax-force">
//<![CDATA[
	{if $smarty.capture.cc_script != 'Y'}
	lang.error_card_number_not_valid = '{$lang.error_card_number_not_valid|escape:javascript}';

	var cvv2_required = new Array();
	var start_date_required = new Array();
	var issue_number_required = new Array();
	{foreach from=$credit_cards item="c"}
		cvv2_required['{$c.param}'] = '{$c.param_2}';
		start_date_required['{$c.param}'] = '{$c.param_3}';
		issue_number_required['{$c.param}'] = '{$c.param_4}';
	{/foreach}

	{literal}
	function fn_check_cc_type(card, suffix)
	{
		if (cvv2_required[card] == 'Y') {
			$('#display_cvv2' + suffix).switchAvailability(false);
		} else {
			$('#display_cvv2' + suffix).switchAvailability(true);
		}

		if (start_date_required[card] == 'Y') {
			$('#display_start_date' + suffix).switchAvailability(false);
		} else {
			$('#display_start_date' + suffix).switchAvailability(true);
		}

		if (issue_number_required[card] == 'Y') {
			$('#display_issue_number' + suffix).switchAvailability(false);
		} else {
			$('#display_issue_number' + suffix).switchAvailability(true);
		}

		$('.cm-cc-item', $('#det_img_' + card + suffix).parents('td:first')).hide();
		$('#det_img_' + card + suffix).show();
	}

	function fn_check_cc_date(id)
	{
		var elm = $('#' + id);

		if (!jQuery.is.integer(elm.val())) {
			return lang.error_validator_integer;
		} else {
			if (elm.val().length == 1) {
				elm.val('0' + elm.val());
			}
		}

		return true;
	}
	{/literal}
	
	{capture name="cc_script"}Y{/capture}
	{/if}

	fn_check_cc_type($('#cc_type{$id_suffix}').val(), '{$id_suffix}');
//]]>
</script>
