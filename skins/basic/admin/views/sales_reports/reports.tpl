{* $Id: reports.tpl 10456 2010-08-18 13:35:18Z angel $ *}
{script src="lib/amcharts/swfobject.js"}

<div id="content_{$report.report_id}">

{capture name="mainbox"}

{capture name="extra_tools"}
	{include file="buttons/button.tpl" but_text=$lang.edit_report but_href="sales_reports.table.edit?report_id=$report_id&table_id=`$table.table_id`" but_role="tool"}
{/capture}

{include file="views/sales_reports/components/sales_reports_search_form.tpl" period=$report.period search=$report}


{if $report}

{capture name="tabsbox"}
{if $report.tables}
{assign var="table_id" value=$table.table_id}
{assign var="table_prefix" value="table_$table_id"}
<div id="content_table_{$table_id}">

{if !$table.elements || $table.empty_values == "Y"}

<p class="no-items">{$lang.no_data}</p>

{elseif $table.type == "T"}

{if $table_conditions.$table_id}
<p>
	<a id="sw_box_table_conditions_{$table_id}" class="text-link text-button cm-combination">{$lang.table_conditions}</a>
</p>
<div id="box_table_conditions_{$table_id}" class="hidden">
	{foreach from=$table_conditions.$table_id item="i"}
	<div class="form-field">
	<label>{$i.name}:</label>
	{foreach from=$i.objects item="o" name="feco"}
	{if $o.href}<a href="{$o.href|fn_url}">{/if}{$o.name}{if $o.href}</a>{/if}{if !$smarty.foreach.feco.last}, {/if}
	{/foreach}
	</div>
	{/foreach}
</div>
{/if}

{if $table.interval_id != 1}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table-fixed">
<tr valign="top">
	{cycle values="" assign=""}
	<td width="300">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="100%">{$table.parameter}</th>
		</tr>
		{foreach from=$table.elements item=element}
		<tr>
			<td>{$element.description|unescape}&nbsp;</td>
		</tr>
		{/foreach}
		<tr>
			<td class="right">{$lang.total}:</td>
		</tr>
		</table>
	</td>
	<td>
	{cycle values="" assign=""}
	<div id="div_scroll_{$table_id}" class="scroll-x">
		<table cellpadding="0" cellspacing="0" border="0" class="table no-left-border">
		<tr>
				{foreach from=$table.intervals item=row}
				<th>&nbsp;{$row.description}&nbsp;</th>
				{/foreach}
		</tr>
		{foreach from=$table.elements item=element}
		<tr>
		{assign var="element_hash" value=$element.element_hash}
				{foreach from=$table.intervals item=row}
				{assign var="interval_id" value=$row.interval_id}
				<td class="center">
				{if $table.values.$element_hash.$interval_id}
				{if $table.display != "product_number" && $table.display != "order_number"}{include file="common_templates/price.tpl" value=$table.values.$element_hash.$interval_id}{else}{$table.values.$element_hash.$interval_id}{/if}
				{else}-{/if}</td>
				{/foreach}
		</tr>
		{/foreach}
		<tr>
			{foreach from=$table.totals item=row}
			<td class="center">
				{if $row}
				<strong>{if $table.display != "product_number" && $table.display != "order_number"}{include file="common_templates/price.tpl" value=$row}{else}{$row}{/if}</strong>
				{else}-{/if}
			</td>
			{/foreach}
		</tr>
		</table>
	</div>
	</td>
</tr>
</table>

{else}

<table cellpadding="0" cellspacing="0" border="0" width="500" class="table-fixed">
<tr>
	{cycle values="" assign=""}
	<td width="403" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table no-bottom-border">
		<tr>
			<th>{$table.parameter}</th>
		</tr>
		</table>
	</td>
	<td width="100">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table no-left-border no-bottom-border">
		<tr>
			{foreach from=$table.intervals item=row}
			{assign var="interval_id" value=$row.interval_id}
			{assign var="interval_name" value="reports_interval_$interval_id"}
			<th class="center">&nbsp;{$lang.$interval_name}&nbsp;</th>
			{/foreach}
		</tr>
		</table>
	</td>
</tr>
</table>

{assign var="elements_count" value=$table.elements|sizeof}

{if $elements_count>14}
<div id="div_scroll_{$table_id}" class="reports-table-scroll">
{/if}

<table cellpadding="0" cellspacing="0" border="0" class="table-fixed" width="500">
<tr valign="top">
	<td width="403" class="max-height no-padding">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table no-top-border">
		{foreach from=$table.elements item=element}
		{assign var="element_hash" value=$element.element_hash}
		<tr>
			{foreach from=$table.intervals item=row}
			{assign var="interval_id" value=$row.interval_id}
			{math equation="round(value_/max_value*100)" value_=$table.values.$element_hash.$interval_id|default:"0" max_value=$table.max_value assign="percent_value"}
			{*if $percent_value<1}{assign var="percent_value" value=1}{/if*}
			{/foreach}
			<td class="no-padding">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table-fixed">
			<tr>
				<td class="nowrap overflow-hidden" width="233">{$element.description|unescape}&nbsp;</td>
				<td align="right" width="120">{include file="views/sales_reports/components/graph_bar.tpl" bar_width="100px" value_width=$percent_value}</td>
			</tr>
			</table>
			</td>
		</tr>
		{/foreach}
		<tr>
			<td class="right">{$lang.total}:</td>
		</tr>
		</table>
	</td>
	<td width="100">
		{cycle values="" assign=""}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table no-top-border no-left-border">
		{foreach from=$table.elements item=element}
		<tr>
		{assign var="element_hash" value=$element.element_hash}
				{foreach from=$table.intervals item=row}
				{assign var="interval_id" value=$row.interval_id}
				<td  class="center">
				{if $table.values.$element_hash.$interval_id}
				{if $table.display != "product_number" && $table.display != "order_number"}{include file="common_templates/price.tpl" value=$table.values.$element_hash.$interval_id}{else}{$table.values.$element_hash.$interval_id}{/if}
				{else}-{/if}</td>
				{/foreach}
		</tr>
		{/foreach}
		<tr>
			{foreach from=$table.totals item="row"}
			<td class="center">
				{if $row}
				<strong>{if $table.display != "product_number" && $table.display != "order_number"}{include file="common_templates/price.tpl" value=$row}{else}{$row}{/if}</strong>
				{else}-{/if}
			</td>
			{/foreach}
		</tr>
		</table>
	</td>
</tr>
</table>

{if $elements_count>14}
</div>
{/if}

{/if}

{elseif $table.type == "P"}
	<div id="{$table_prefix}pie">{include file="views/sales_reports/components/amchart.tpl" type="pie" chart_data=$new_array.pie_data chart_id=$table_prefix chart_title=$table.description chart_height=$new_array.pie_height}<!--{$table_prefix}pie--></div>

{elseif $table.type == "C"}
	<div id="{$table_prefix}pie">{include file="views/sales_reports/components/amchart.tpl" type="pie" set_type="piefl" chart_data=$new_array.pie_data chart_id=$table_prefix chart_title=$table.description chart_height=$new_array.pie_height}<!--{$table_prefix}pie--></div>

{elseif $table.type == "B"}
	<div id="div_scroll_{$table_id}" class="reports-graph-scroll">
		<div id="{$table_prefix}bar">{include file="views/sales_reports/components/amchart.tpl" type="column" chart_data=$new_array.column_data chart_id=$table_prefix chart_title=$table.description chart_height=$new_array.column_height chart_width=$new_array.column_width}<!--{$table_prefix}bar--></div>
	</div>
{/if}

<!--content_table_{$table_id}--></div>

{else}
	<p class="no-items">{$lang.no_data}</p>
{/if}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab="table_`$smarty.request.table_id`" track=true}

{else}
	<p class="no-items">{$lang.no_data}</p>
{/if}
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.reports content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}

<!--content_{$report.report_id}--></div>