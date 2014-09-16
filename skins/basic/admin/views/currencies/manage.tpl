{* $Id: manage.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}

{capture name="mainbox"}
{include file="common_templates/sortable_position_scripts.tpl" sortable_table="currencies" sortable_id_name="currency_code"}

{assign var="r_url" value=$config.current_url|escape:url}

<div class="items-container cm-sortable" id="manage_currencies_list">
{foreach from=$currencies_data item="currency"}
	{if $currency.is_primary == "Y"}
		{assign var="_href_delete" value=""}
	{else}
		{assign var="_href_delete" value="currencies.delete?currency_code=`$currency.currency_code`"}
	{/if}
	{assign var="currency_details" value="<strong>`$currency.currency_code`</strong>, `$lang.currency_rate`: <strong>`$currency.coefficient`</strong>, `$lang.currency_sign`: <strong>`$currency.symbol`</strong>"}
	{include file="common_templates/object_group.tpl" id=$currency.currency_code text=$currency.description details=$currency_details|unescape href="currencies.update?currency_code=`$currency.currency_code`&amp;return_url=$r_url" href_delete=$_href_delete rev_delete="pagination_contents" header_text="`$lang.editing_currency`:&nbsp;`$currency.description`" table="currencies" object_id_name="currency_code" status=$currency.status additional_class="cm-sortable-row cm-sortable-id-`$currency.currency_code`"}
{foreachelse}

	<p class="no-items">{$lang.no_data}</p>

{/foreach}
<!--manage_currencies_list--></div>

<div class="buttons-container">
	{capture name="extra_tools"}
		{hook name="currencies:import_rates"}{/hook}
	{/capture}
	{if !"COMPANY_ID"|defined}
	{include file="common_templates/popupbox.tpl" id="add_new_currency" text=$lang.add_currency link_text=$lang.add_currency act="general"}
	{/if}
</div>
{capture name="tools"}
	{capture name="add_new_picker"}
		{include file="views/currencies/update.tpl" mode="add" currency=""}
	{/capture}
	
	{include file="common_templates/popupbox.tpl" id="add_new_currency" text=$lang.add_currency content=$smarty.capture.add_new_picker link_text=$lang.add_currency act="general"}
{/capture}
{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.currencies content=$smarty.capture.mainbox tools=$smarty.capture.tools title_extra=$smarty.capture.title_extra select_languages=true extra_tools=$smarty.capture.extra_tools|trim}
