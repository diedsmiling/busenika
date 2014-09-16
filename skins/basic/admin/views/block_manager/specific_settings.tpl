{* $Id: specific_settings.tpl 9807 2010-06-18 07:39:35Z lexa $ *}

{if $spec_settings && (($spec_settings|count > 1 && $spec_settings.settings) || (!$spec_settings.settings))}
<div id="toggle_{$s_set_id}">
<div class="specific-settings float-left" id="container_{$s_set_id}">
<a id="sw_additional_{$s_set_id}" class="cm-combo-on|off cm-combination">{$lang.specific_settings}</a>
<img src="{$images_dir}/icons/section_collapsed.gif" width="7" height="9" border="0" alt="" id="on_additional_{$s_set_id}" class="cm-combination" />
<img src="{$images_dir}/icons/section_expanded.gif" width="7" height="9" border="0" alt="" id="off_additional_{$s_set_id}" class="cm-combination hidden" />
</div>

<div class="hidden" id="additional_{$s_set_id}">
{foreach from=$spec_settings key="set_name" item="_option"}
	{include file="views/block_manager/components/setting_element.tpl" set_name=$set_name option=$_option block=$block set_id=$s_set_id}
{/foreach}
</div>
<!--toggle_{$s_set_id}--></div>
{else}
<div id="toggle_{$s_set_id}"><!--toggle_{$s_set_id}--></div>
{/if}
