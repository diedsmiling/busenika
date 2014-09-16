{* $Id: tabs_extra.post.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

{if $page_type == $smarty.const.PAGE_TYPE_POLL && $page_data.page_id}

	<div class="cm-hide-save-button" id="content_poll_questions">

	{script src="js/tabs.js"}
	{script src="js/picker.js"}

	<div class="items-container">
	{foreach from=$questions key="k" item="q"}

		{include file="common_templates/object_group.tpl" id=$q.item_id text=$q.description status=$q.status href="pages.update_question?item_id=`$q.item_id`" object_id_name="item_id" table="poll_questions" href_delete="pages.delete_question?item_id=`$q.item_id`" rev_delete="content_poll_questions" header_text="`$lang.editing_question`: `$q.description`"}

	{foreachelse}

		<p class="no-items">{$lang.no_data}</p>

	{/foreach}
	</div>

	<div class="buttons-container">
		{capture name="add_new_picker"}
			{include file="addons/polls/views/pages/update_question.tpl" mode="add"}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_question" text=$lang.new_question content=$smarty.capture.add_new_picker link_text=$lang.add_question act="general"}
	</div>

	<!--content_poll_questions--></div>

	<div id="content_poll_statistics" class="cm-hide-save-button cm-track">
		{include file="addons/polls/views/pages/components/statistics.tpl"}
	</div>

{/if}