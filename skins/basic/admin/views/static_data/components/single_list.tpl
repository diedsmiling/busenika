{* $Id: manage.tpl 6369 2008-11-20 10:54:05Z zeke $ *}

{include file="common_templates/sortable_position_scripts.tpl" sortable_table="static_data" sortable_id_name="param_id"}

<div class="items-container cm-sortable">
{foreach from=$static_data item="s"}

	{include file="common_templates/object_group.tpl" id=$s.param_id text=$s.descr status=$s.status hidden=false href="static_data.update?param_id=`$s.param_id`&amp;section=$section" object_id_name="param_id" table="static_data" href_delete="static_data.delete?param_id=`$s.param_id`&amp;section=$section" rev_delete="static_data_list" header_text=$lang[$section_data.edit_title]|cat:": `$s.descr`" link_text="" additional_class="cm-sortable-row cm-sortable-id-`$s.param_id`"}

{foreachelse}
	<p class="no-items">{$lang.no_data}</p>
{/foreach}
</div>
