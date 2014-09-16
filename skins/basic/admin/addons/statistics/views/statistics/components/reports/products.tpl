{* $Id: products.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

<div id="content_{$report_data.report}">

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th width="55%">{$lang.search_conditions}</th>
		<th width="15%" class="right">{$lang.date}</th>
		<th width="15%" class="right">{$lang.visitors}</th>
		<th width="15%" class="right">{$lang.found_products}</th>
	</tr>
	{foreach from=$report_data.data item="row" key="k"}
	<tr {cycle values=",class=\"table-row\""}>
		<td>
			{strip}
			<div class="clear">
				<div class="float-left">
					<strong>{if $row.label.q}{$row.label.q}{else}-&nbsp;{$lang.empty}&nbsp;-{/if}</strong>
					<p class="approved-text">[{if $row.label.match == "exact"}{$lang.exact_phrase}{elseif $row.label.match == "all"}{$lang.all_words}{else}{$lang.any_words}{/if}]</p>
				</div>
				
				<div class="float-right">
					<p>&nbsp;<a name="details_{$k}" class="hand" onclick="$('#stat_product_search_{$k}').toggle();">{$lang.details}&nbsp;&raquo;</a></p>
				</div>
			</div>
			
			<div id="stat_product_search_{$k}" class="light-notice-box hidden">
			{if $row.label.pname || $row.label.pshort || $row.label.pfull || $row.label.pkeywords}
			<p><strong>{$lang.search_in}:</strong>&nbsp;
				{assign var="comma" value=""}
				{if $row.label.pname}
					{$lang.product_name}
					{assign var="comma" value=",&nbsp;"}
				{/if}
				{if $row.label.pshort}
					{$comma}{$lang.short_description}
					{assign var="comma" value=",&nbsp;"}
				{/if}
				{if $row.label.pfull}
					{$comma}{$lang.full_description}
					{assign var="comma" value=",&nbsp;"}
				{/if}
				{if $row.label.pkeywords}
					{$comma}{$lang.keywords}
				{/if}</p>
			{/if}
				
			{if $row.label.feature}
			<p><strong>{$lang.search_by_product_features}:</strong>&nbsp;
				{assign var="comma" value=""}
				{foreach from=$row.label.feature item="feature_id"}
					{if $product_features.$feature_id.description}
						{$comma}{$product_features.$feature_id.description}
						{assign var="comma" value=",&nbsp;"}
					{/if}
				{/foreach}</p>
			{/if}
			
			{if $row.label.category}
				<p><strong>{$lang.search_in_category}:</strong>&nbsp;
				{$row.label.category}
				{if $row.label.subcats}&nbsp;[{$lang.search_in_subcategories}]{/if}</p>
			{/if}
			
			{if $row.label.pcode}
				<p><strong>{$lang.search_by_sku}:</strong>&nbsp;{$row.label.pcode}</p>
			{/if}
			
			{if $row.label.price_from || $row.label.price_to}
				<p><strong>{$lang.search_by_price}:</strong>&nbsp;{$row.label.price_from|format_price:$currencies.$primary_currency:"price_from_$k"}&nbsp;-&nbsp;{$row.label.price_to|format_price:$currencies.$primary_currency:"price_to_$k"}</p>
			{/if}
			
			{if $row.label.weight_from || $row.label.weight_to}
				<p><strong>{$lang.search_by_weight}&nbsp;({$settings.General.weight_symbol}):</strong>&nbsp;{$row.label.weight_from|default:0}&nbsp;-&nbsp;{$row.label.weight_to|default:0}</p>
			{/if}
			</div>
			
			{/strip}
		</td>
		<td class="right">{$row.date|date_format:$settings.Appearance.date_format}</td>
		<td class="right">
			<a href="{"statistics.visitors?section=products&amp;report=`$report_data.report`&amp;object_code=`$row.md5`"|fn_url}">{$row.count}</a>
		</td>
		<td class="right">
			{if $row.quantity}<a href="{"`config.customer_index`?dispatch=products.search?`$row.url`"|fn_url:'C'}" target="_blank">{/if}{$row.quantity|string_format:"%d"}{if $row.quantity}</a>{/if}</td>
	</tr>
	{foreachelse}
	<tr class="no-items">
		<td colspan="4"><p>{$lang.no_data}</p></td>
	</tr>
	{/foreach}
	</table>

<!--content_{$report_data.report}--></div>
