{* $Id: manage.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{capture name="tabsbox"}

<div id="content_{$selected_section}">
{include file="addons/affiliate/views/banners_manager/components/banners_list.tpl" prefix=$selected_section}
<!--content_{$selected_section}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$selected_section}

{capture name="mainbox_title"}{$mainbox_title}{/capture}