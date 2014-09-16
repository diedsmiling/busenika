{* $Id: manage.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}

{capture name="mainbox"}

<div class="items-container" id="statuses_list">
{foreach from=$statuses item="s" key="key"}
	{if $s.is_default !== "Y"}
		{assign var="cur_href_delete" value="statuses.delete?status=`$s.status`&type=`$type`"}
	{else}
		{assign var="cur_href_delete" value=""}
	{/if}
	{include file="common_templates/object_group.tpl" id=$s.status|lower text=$s.description href="statuses.update?status=`$s.status`&type=`$type`" href_delete=$cur_href_delete rev_delete="statuses_list" header_text="`$lang.editing_status`:&nbsp;`$s.description`"}

{foreachelse}

	<p class="no-items">{$lang.no_items}</p>

{/foreach}
<!--statuses_list--></div>

<div class="buttons-container">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="views/statuses/update.tpl" mode="add"}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_status" text=$lang.add_new_status content=$smarty.capture.add_new_picker link_text=$lang.add_status act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_status" text=$lang.add_new_status link_text=$lang.add_status act="general"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}