{* $Id: details.tpl 9206 2010-04-01 07:54:17Z lexa $ *}
<div id="content_groupfeedback">
<form action="{""|fn_url}" method="get" name="feedback_form" class="cm-form-highlight">
<div class="object-container">
	<p>{$lang.text_feedback_notice}</p>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th width="15%">{$lang.Section}</th>
		<th width="20%">{$lang.param}</th>
		<th width="65%">{$lang.value}</th>
	</tr>
	{foreach from=$fdata key="section" item="data"}
	<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td colspan="3">{if $lang.$section|strpos:"Option"===false}{$lang.$section}{else}{$section}{/if}</td>
	</tr>
		{if $section == 'payments' || $section == 'currencies' || $section == 'taxes' || $section == 'shippings' || $section == 'promotions' || $section == 'addons'}
		<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td>&nbsp;</td>
		<td colspan="2">
			<div id="parameters_{$section}">
			<table cellpadding="0" cellspacing="0" border="0" width="80%" class="table">
			{foreach from=$data key="key" item="value" name="section"}
				{if $smarty.foreach.section.first}
				<tr>
				{foreach from=$value item="item" key="key" name="param_keys"}
				<th>{$key}</th>
				{/foreach}
				</tr>
				{/if}
				<tr>
				{foreach from=$value item="item" key="key" name="param_items"}
				<td>{if $item == 'Y'}{$lang.yes}{elseif $item == 'N'}{$lang.no}{else}{$item}{/if}</td>
				{/foreach}
				</tr>
			{/foreach}
			</table>
			<!--parameters_{$section}--></div>
		</td>
		</tr>
		{else}
		{foreach from=$data key="key" item="value" name="section"}
			<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
			{if $section=='settings'}
				<td class="nowrap">&nbsp;</td>
				<td>{$value.name}</td>
				<td>{$value.value|replace:'&amp;':'&amp; '}</td>
			{elseif $section=='addons'}
				<td class="nowrap"></td>
				<td class="nowrap">{$value.addon}</td>
				<td>{$value.status}</td>
			{elseif $section=='languages'}
				<td class="nowrap"></td>
				<td class="nowrap">{$value.lang_code}</td>
				<td>{$value.status}</td>
			{else}
				<td class="nowrap"></td>
				<td class="nowrap">{$key}</td>
				<td  width="200px">{$value}</td>
			{/if}
			</tr>
		{/foreach}
		{/if}
	{/foreach}
	</table>
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_name="dispatch[feedback.send]" but_text=$lang.send but_role="button_main"}
</div>
</form>
<!--content_groupfeedback--></div>
