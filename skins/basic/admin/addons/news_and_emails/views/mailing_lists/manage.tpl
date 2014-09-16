{* $Id: manage.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<div class="items-container" id="mailing_lists">

{foreach from=$mailing_lists item="mailing_list"}

	{capture name="tool_items"}
		<li><a href="{"subscribers.manage?list_id=`$mailing_list.list_id`"|fn_url}">{$lang.add_subscribers}</a></li>
	{/capture}
	{include file="common_templates/object_group.tpl" id=$mailing_list.list_id text=$mailing_list.object status=$mailing_list.status hidden=true href="mailing_lists.update?list_id=`$mailing_list.list_id`" details="`$lang.subscribers_num`: `$mailing_list.subscribers_num`" object_id_name="list_id" table="mailing_lists" href_delete="mailing_lists.delete?list_id=`$mailing_list.list_id`" rev_delete="mailing_lists" header_text="`$lang.editing_mailing_list`:&nbsp;`$mailing_list.object`" tool_items=$smarty.capture.tool_items}

{foreachelse}

	<p class="no-items">{$lang.no_items}</p>

{/foreach}
<!--mailing_lists--></div>

<div class="buttons-container">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="addons/news_and_emails/views/mailing_lists/update.tpl" mode="add" mailing_list=""}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_mailing_lists" text=$lang.add_mailing_lists content=$smarty.capture.add_new_picker link_text=$lang.add_mailing_lists act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_mailing_lists" text=$lang.add_mailing_lists link_text=$lang.add_mailing_lists act="general"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.mailing_lists content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
