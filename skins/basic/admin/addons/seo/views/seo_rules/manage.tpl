{* $Id: manage.tpl 7467 2009-05-17 10:59:43Z zeke $ *}

{script src="js/picker.js"}

{capture name="add_seo_rule"}

<form action="{""|fn_url}" method="post" name="rule_add_var">
<input type="hidden" name="page" value="{$smarty.request.page}" />
<div class="object-container">
	<div class="form-field">
		<label class="cm-required" for="rule_name">{$lang.name}:</label>
		<input type="text" name="name" id="rule_name" value="" class="input-text-large" />
	</div>
	<div class="form-field">
		<label class="cm-required" for="rule_controller">{$lang.controller}:</label>
		<input type="text" name="controller" id="rule_controller" value="" class="input-text-large" />
		<p class="description">{$lang.controller_description}</p>
	</div>
</div>
<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[seo_rules.add_rule]" create=true cancel_action="close"}
</div>
</form>

{/capture}

{capture name="mainbox"}

{include file="addons/seo/views/seo_rules/components/search_form.tpl" dispatch="seo_rules.manage"}

<form action="{""|fn_url}" method="post" name="seo_form">

{include file="common_templates/pagination.tpl"}

<input type="hidden" name="page" value="{$smarty.request.page}" />
<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table">
<tr>
	<th width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="35%">{$lang.controller}</th>
	<th width="64%">{$lang.name}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$seo_data item="var" key="key"}
<tr {cycle values="class=\"table-row\", " name="2"} valign="top">
	<td>
		<input type="checkbox" name="controllers[]" value="{$var.dispatch}" class="checkbox cm-item" /></td>
	<td>
		<input type="hidden" name="seo_data[{$key}][dispatch]" value="{$var.dispatch}" />
		<strong>{$var.dispatch}</strong></td>
	<td>
		<input type="text" name="seo_data[{$key}][name]" value="{$var.name}" class="input-text-large" /></td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"seo_rules.delete_rule?controller=`$var.dispatch`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$var.name tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $seo_data}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[seo_rules.delete_rules]" class="cm-process-items cm-confirm" rev="seo_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[seo_rules.update_rules]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="float-right">
		{capture name="tools"}
			{include file="common_templates/popupbox.tpl" id="add_seo_rule" text=$lang.new_rule link_text=$lang.add_new content=$smarty.capture.add_seo_rule act="general"}
		{/capture}
	
		{include file="common_templates/popupbox.tpl" id="add_seo_rule" text=$lang.new_rule link_text=$lang.add_new act="general"}
	</div>
</div>
</form>

{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.seo_rules content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools select_languages=true}
