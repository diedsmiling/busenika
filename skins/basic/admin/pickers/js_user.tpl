{* $Id: js_user.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $view_mode == "list" || $view_mode == "mixed"}
    {if $user_id == "All"}
        <tr {if !$clone}id="{$holder}_{$user_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
            <td>{$lang.all_users}</td>
            <td class="nowrap">
                {if !$view_only}
                    {capture name="tools_items"}
                        <li><a onclick="jQuery.delete_js_item('{$holder}', '{$user_id}', 'u'); return false;">{$lang.delete}</a></li>
                    {/capture}
                    {include file="common_templates/table_tools_list.tpl" prefix=$order_id popup=true tools_list=$smarty.capture.tools_items href="profiles.update?user_id=`$user_id`"}
                {else}&nbsp;{/if}
            </td>
        </tr>
    {else}
        <tr {if !$clone}id="{$holder}_{$user_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
            <td>{$user_name} (<a href="{"profiles.update?user_id=`$user_id`"|fn_url}"" class="user-email"><strong>{$email}</strong></a>)</td>
            <td class="nowrap">
                {if !$view_only}
                    {capture name="tools_items"}
                        <li><a onclick="jQuery.delete_js_item('{$holder}', '{$user_id}', 'u'); return false;">{$lang.delete}</a></li>
                    {/capture}
                    {include file="common_templates/table_tools_list.tpl" prefix=$order_id tools_list=$smarty.capture.tools_items href="profiles.update?user_id=`$user_id`"}
                {else}&nbsp;{/if}
            </td>
        </tr>
    {/if}
{else}
	<{if $single_line}span{else}p{/if} {if !$clone}id="{$holder}_{$user_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
	{if !$first_item && $single_line}<span class="cm-comma{if $clone} hidden{/if}">,&nbsp;&nbsp;</span>{/if}
	{$user_name} (<a href="{"profiles.update?user_id=`$user_id`"|fn_url}" class="underlined user-email"><strong>{$email}</strong></a>)
	{if !$view_only}<a onclick="jQuery.delete_js_item('{$holder}', '{$user_id}', 'u'); return false;"><img width="12" height="18" border="0" class="hand valign" alt="" src="{$images_dir}/icons/icon_delete.gif"/></a>{/if}
	</{if $single_line}span{else}p{/if}>
{/if}