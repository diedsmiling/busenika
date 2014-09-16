{* $Id: manage.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

<div class="items-container" id="attachments_list">
{foreach from=$attachments item="a"}

	{capture name="object_group"}
		{include file="addons/attachments/views/attachments/update.tpl" mode="update" attachment=$a object_id=$object_id object_type=$object_type}
	{/capture}
	{include file="common_templates/object_group.tpl" content=$smarty.capture.object_group id=$a.attachment_id text=$a.description status=$a.status object_id_name="attachment_id" table="attachments" href_delete="attachments.delete?attachment_id=`$a.attachment_id`&object_id=`$object_id`&object_type=`$object_type`" rev_delete="attachments_list" header_text="`$lang.editing_attachment`: `$a.description`"}

{foreachelse}

	<p class="no-items">{$lang.no_data}</p>

{/foreach}
<!--attachments_list--></div>

<div class="buttons-container">
	{capture name="add_new_picker"}
		{include file="addons/attachments/views/attachments/update.tpl" mode="add" attachment="" object_id=$object_id object_type=$object_type}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_attachments_files" text=$lang.new_attachment link_text=$lang.add_attachment content=$smarty.capture.add_new_picker act="general"}
</div>
