{* $Id: payout_search.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
{capture name="section"}

<form name="payout_search_form" action="{""|fn_url}" method="get">

{include file="common_templates/period_selector.tpl" period=$payout_search.period form_name="payout_search_form" search=$payout_search}

<div class="form-field">
	<label>{$lang.status}:</label>
	<select name="payout_search[status]">
		<option value="0" {if $payout_search.status=="0"}selected="selected"{/if}>- {$lang.any_status} -</option>
		<option value="O" {if $payout_search.status=="O"}selected="selected"{/if}>{$lang.open}</option>
		<option value="S" {if $payout_search.status=="S"}selected="selected"{/if}>{$lang.successful}</option>
	</select>
</div>

<div class="form-field">
	<label>{$lang.amount} ({$currencies.$primary_currency.symbol}):</label>
	<input type="text" name="payout_search[amount][from]" size="7" value="{$payout_search.amount.from}" class="input-text-short" /> -
	<input type="text" name="payout_search[amount][to]" size="7" value="{$payout_search.amount.to}" class="input-text-short" />
</div>

<div class="buttons-container">{include file="buttons/search.tpl" but_name="dispatch[$controller.list]"}</div>

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section section_title=$lang.search}
