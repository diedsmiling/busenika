{* $Id: product_classes.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div {if $smarty.request.selected_section != "content_classes"}class="hidden"{/if} id="content_classes">

<form action="{""|fn_url}" method="post" name="classes_form" enctype="multipart/form-data">

{assign var="c_url" value=$config.current_url|fn_query_remove:"class_sort_by":"class_sort_order":"selected_section"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<div id="pagination_classes">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%"><a class="{$ajax_class}{if $classes_search.class_sort_by == "class_name"} sort-link-{$classes_search.class_sort_order}{/if}" href="{"`$c_url`&amp;class_sort_by=class_name&amp;class_sort_order=`$classes_search.class_sort_order`&amp;selected_section=classes"|fn_url}" rev="pagination_classes">{$lang.name}</a></th>
	<th><a class="{$ajax_class}{if $classes_search.class_sort_by == "group_name"} sort-link-{$classes_search.class_sort_order}{/if}" href="{"`$c_url`&amp;class_sort_by=group_name&amp;class_sort_order=`$classes_search.class_sort_order`&amp;selected_section=classes"|fn_url}" rev="pagination_classes">{$lang.group}</a></th>
	<th><a class="{$ajax_class}{if $classes_search.class_sort_by == "status"} sort-link-{$classes_search.class_sort_order}{/if}" href="{"`$c_url`&amp;class_sort_by=status&amp;class_sort_order=`$classes_search.class_sort_order`&amp;selected_section=classes"|fn_url}" rev="pagination_classes">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>	
{foreach from=$classes item=pclass}
<tr {cycle values="class=\"table-row\", " name="3"}>
	<td class="center" width="1%"><input type="checkbox" name="class_ids[]" value="{$pclass.class_id}" class="checkbox cm-item" /></td>
	<td class="nowrap" width="40%"><input type="text" name="class_data[{$pclass.class_id}][class_name]" value="{$pclass.class_name}" class="input-text-long" /></td>
	<td class="nowrap">
		<select name="class_data[{$pclass.class_id}][group_id]">
			<option value="0">-&nbsp;{$lang.none}&nbsp;-</option>
			{foreach from=$all_groups item="group"}
			<option value="{$group.group_id}" {if $pclass.group_id == $group.group_id}selected="selected"{/if}>{$group.configurator_group_name}</option>
			{/foreach}
		</select>
	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$pclass.class_id prefix="class" status=$pclass.status hidden="" object_id_name="class_id" table="conf_classes"}
	</td>
	<td class="nowrap">
		{assign var="cls_id" value=$pclass.class_id}
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"configurator.delete_class?class_id=`$pclass.class_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$cls_id tools_list=$smarty.capture.tools_items href="configurator.update_class?class_id=$cls_id"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>
{if $classes}
	{include file="common_templates/table_tools.tpl" href="#product_classes" visibility="Y"}
{/if}

<!--pagination_classes--></div>

<div class="buttons-container buttons-bg">
	{if $classes}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[configurator.delete_classes]" class="cm-process-items cm-confirm" rev="classes_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[configurator.update_classes]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="configurator.add_class" prefix="bottom" hide_tools=true link_text=$lang.add_product_class}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="configurator.add_class" prefix="bottom" hide_tools=true link_text=$lang.add_product_class}
{/capture}

</form>

<!--content_classes--></div>
