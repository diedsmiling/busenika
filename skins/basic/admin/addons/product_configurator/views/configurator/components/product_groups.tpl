{* $Id: product_groups.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div {if $smarty.request.selected_section != "content_groups"}class="hidden"{/if} id="content_groups">

<form action="{""|fn_url}" method="post" name="configurator_groups_form">

{assign var="c_url" value=$config.current_url|fn_query_remove:"group_sort_by":"group_sort_order":"selected_section"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

{include file="common_templates/pagination.tpl" div_id="pagination_groups"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%"><a class="{$ajax_class}{if $groups_search.group_sort_by == "group_name"} sort-link-{$groups_search.group_sort_order}{/if}" href="{"`$c_url`&amp;group_sort_by=group_name&amp;group_sort_order=`$groups_search.group_sort_order`&amp;selected_section=groups"|fn_url}" rev="pagination_groups">{$lang.name}</a></th>
	<th><a class="{$ajax_class}{if $groups_search.group_sort_by == "step_name"} sort-link-{$groups_search.group_sort_order}{/if}" href="{"`$c_url`&amp;group_sort_by=step_name&amp;group_sort_order=`$groups_search.group_sort_order`&amp;selected_section=groups"|fn_url}" rev="pagination_groups">{$lang.step}</a></th>
	<th><a class="{$ajax_class}{if $groups_search.group_sort_by == "display_type"} sort-link-{$groups_search.group_sort_order}{/if}" href="{"`$c_url`&amp;group_sort_by=display_type&amp;group_sort_order=`$groups_search.group_sort_order`&amp;selected_section=groups"|fn_url}" rev="pagination_groups">{$lang.display_type}</a></th>
	<th><a class="{$ajax_class}{if $groups_search.group_sort_by == "status"} sort-link-{$groups_search.group_sort_order}{/if}" href="{"`$c_url`&amp;group_sort_by=status&amp;group_sort_order=`$groups_search.group_sort_order`&amp;selected_section=groups"|fn_url}" rev="pagination_groups">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$groups item=group}
{assign var="pair" value=$group.image_pairs}
<tr {cycle values="class=\"table-row\", " name="2"}>
	<td class="center" width="1%">
		<input type="checkbox" name="group_ids[]" value="{$group.group_id}" class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="configurator_group_data[{$group.group_id}][configurator_group_name]" value="{$group.configurator_group_name}" class="input-text-long" /></td>
	<td>
		<select name="configurator_group_data[{$group.group_id}][step_id]">
			<option value="0">-&nbsp;{$lang.none}&nbsp;-</option>
			{foreach from=$steps item="step"}
				<option value="{$step.step_id}" {if $group.step_id == $step.step_id}selected="selected"{/if}>{$step.step_name}</option>
			{/foreach}
		</select></td>
	<td>
		<select name="configurator_group_data[{$group.group_id}][configurator_group_type]">
			<option value="S" {if $group.configurator_group_type == "S"}selected="selected"{/if}>{$lang.selectbox}</option>
			<option value="R" {if $group.configurator_group_type == "R"}selected="selected"{/if}>{$lang.radiogroup}</option>
			<option value="C" {if $group.configurator_group_type == "C"}selected="selected"{/if}>{$lang.checkbox}</option>
		</select></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$group.group_id prefix="group" status=$group.status hidden="" object_id_name="group_id" table="conf_groups"}
	</td>
	<td class="nowrap">
		{assign var="gr_id" value=$group.group_id}
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"configurator.delete_group?group_id=`$gr_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$gr_id tools_list=$smarty.capture.tools_items href="configurator.update_group?group_id=$gr_id"}
		</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>
{if $groups}
{include file="common_templates/table_tools.tpl" href="#configurator_groups" visibility="Y"}
{/if}

{include file="common_templates/pagination.tpl" div_id="pagination_groups"}

<div class="buttons-container buttons-bg">
	{if $groups}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			{hook name="product_configurator_groups:list_extra_links"}
			<li><a name="dispatch[configurator.delete_groups]" class="cm-process-items cm-confirm" rev="configurator_groups_form">{$lang.delete_selected}</a></li>
			{/hook}
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[configurator.update_groups]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="configurator.add_group" prefix="bottom" hide_tools=true link_text=$lang.add_group}
	</div>
</div>
</form>

<!--content_groups--></div>