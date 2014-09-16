{* $Id: manage.tpl 9935 2010-07-01 14:00:05Z lexa $ *}

{script src="js/picker.js"}
{include file="views/block_manager/components/scripts.tpl"}
{include file="common_templates/popupbox.tpl" text=$lang.editing_block content=$content id="edit_block_picker" edit_picker=true}
{capture name="mainbox"}
{capture name="tabsbox"}
<div id="content_{$location}">
<div class="block-manager"><div class="clear"></div>
	{include file="views/block_manager/components/group_element.tpl" blocks_target="top" main_class=""}
	<div class="clear">
	{hook name="block_manager:columns"}
		{include file="views/block_manager/components/group_element.tpl" blocks_target="left" main_class="float-left"}
		{include file="views/block_manager/components/group_element.tpl" blocks_target="central" main_class="float-left"}
		{include file="views/block_manager/components/group_element.tpl" blocks_target="right" main_class="float-left"}
	{/hook}
	</div>
	{include file="views/block_manager/components/group_element.tpl" blocks_target="bottom" main_class=""}
</div>
{include file="views/block_manager/components/sortable_scripts.tpl" object_id="0"}
</div>

{capture name="tools"}
	{capture name="add_new_picker"}
		{include file="views/block_manager/update.tpl" add_block=true block_type="B" block=null}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_block" text=$lang.new_block content=$smarty.capture.add_new_picker link_text=$lang.add_block act="general"}
	{capture name="add_new_picker"}
		{include file="views/block_manager/update.tpl" add_block=true block_type="G" block=null}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_group" text=$lang.new_group content=$smarty.capture.add_new_picker link_text=$lang.add_group act="general"}
{/capture}

<div class="buttons-container cm-toggle-button buttons-bg">
	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_block" text=$lang.new_block link_text=$lang.add_block act="general"}
		{include file="common_templates/popupbox.tpl" id="add_new_group" text=$lang.new_group link_text=$lang.add_group act="general"}
	</div>
</div>

{/capture}
{capture name="active_tab_extra"}
	{capture name="_link_text"}
		<img src="{$images_dir}/icons/icon_list.jpg" width="18" height="17" border="0" />
	{/capture}
	{include file="common_templates/popupbox.tpl" id="tab_`$location`" text="`$lang.editing_location`: `$lang.$location`" act="edit" picker_meta="cm-clear-content" href="block_manager.update_location?location=`$location`"  opener_ajax_class="cm-ajax" link_class="cm-ajax-force" link_text=$smarty.capture._link_text}
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$location active_tab_extra=$smarty.capture.active_tab_extra}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.blocks content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
