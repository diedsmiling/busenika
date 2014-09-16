{* $Id: manage.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

{script src="js/picker.js"}
{include file="addons/store_locator/pickers/map.tpl"}

{capture name="mainbox"}

{include file="common_templates/pagination.tpl" save_current_page=true}

<div class="items-container" id="store_locations">
{foreach from=$store_locations item=loc}
	{capture name="edit_picker"}
		{include file="addons/store_locator/views/store_locator/update.tpl" loc=$loc}
	{/capture}
	{include file="common_templates/object_group.tpl" id=$loc.store_location_id text=$loc.name status=$loc.status href="" object_id_name="store_location_id" table="store_locations" href_delete="store_locator.delete?store_location_id=`$loc.store_location_id`" rev_delete="pagination_contents" header_text=`$lang.editing_store_location`:&nbsp;`$loc.name` content=$smarty.capture.edit_picker}

{foreachelse}

	<p class="no-items">{$lang.no_data}</p>

{/foreach}
<!--store_locations--></div>

{include file="common_templates/pagination.tpl" save_current_page=true}

<div class="buttons-container">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="addons/store_locator/views/store_locator/update.tpl" mode="add" loc=""}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_store_location" text=$lang.new_store_location content=$smarty.capture.add_new_picker link_text=$lang.add_store_location act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_store_location" text=$lang.new_store_location link_text=$lang.add_store_location act="general"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.store_locator content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}