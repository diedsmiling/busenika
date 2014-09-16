{* $Id: credit_cards.tpl 7196 2009-04-06 07:57:02Z zeke $ *}

{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}
{script src="js/cc_validator.js"}

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table" id="credit_cards_list">
<tr>
	<th width="5%">&nbsp;</th>
	<th width="25%">{$lang.credit_card}</th>
	<th width="30%">{$lang.card_number}</th>
	<th width="25%">{$lang.expiry_date}</th>
	<th width="15%">&nbsp;</th>
</tr>
{foreach from=$profile_cards item="card" key="card_key"}
<tr class="cm-row-item{cycle values=", table-row"}">
	<td class="center"><input type="radio" name="default_card" value="{$card_key}"{if $card.default} checked="checked"{/if} /></td>
	{assign var="card_param" value=$card.card}
	<td>{$card_names.$card_param}</td>
	<td>{$card.card_number|substr_replace:"############":0:12}</td>
	<td>{$card.expiry_month}/{$card.expiry_year}</td>
	<td class="nowrap">{include file="views/profiles/components/update_credit_card.tpl" id=$card_key pid=$user_data.profile_id uid=$uid link_text=$lang.edit title=$lang.editing_credit_card card_data=$card card_id=$card_key link_meta="lowercase"}
		&nbsp;|&nbsp;
		<a class="lowercase cm-delete-row cm-ajax cm-confirm" href="{"profiles.delete_card?card_id=`$card_key`&amp;profile_id=`$user_data.profile_id`"|fn_url}" rev="credit_cards_list">{$lang.delete}</a>
	</td>
</tr>
{foreachelse}
<tr>
	<td colspan="5"><p class="no-items">{$lang.no_items}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="5">&nbsp;</td>
</tr>
<!--credit_cards_list--></table>

<div class="buttons-container">
	<div class="float-left">{include file="buttons/save.tpl" but_onclick="$('#default_card_id').val($('input[name=default_card]:checked').val()); $('#save_profile_but').click();"}</div>
	<div class="float-right">{include file="views/profiles/components/update_credit_card.tpl" id="new_card" pid=$user_data.profile_id uid=$uid link_text=$lang.add_credit_card title=$lang.new_credit_card link_meta="text-button"}</div>
</div>