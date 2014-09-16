{* $Id: button.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $but_role == "text"}
	{assign var="class" value="text-link"}
{elseif $but_role == "delete"}
	{assign var="class" value="text-button-delete"}
{elseif $but_role == "add"}
	{assign var="class" value="text-button-add"}
{elseif $but_role == "action"}
	{assign var="suffix" value="-action"}
{elseif $but_role == "delete_item"}
	{assign var="class" value="text-button-delete-item"}
{elseif $but_role == "edit"}
	{assign var="class" value="text-button-edit"}
{elseif $but_role == "tool"}
	{assign var="class" value="tool-link"}
{elseif $but_role == "link"}
	{assign var="class" value="text-button-link"}
{elseif $but_role == "simple"}
	{assign var="class" value="text-button-simple"}
{else}
	{assign var="suffix" value=""}
	{assign var="class" value=""}
{/if}

{if $but_name}{assign var="r" value=$but_name}{else}{assign var="r" value=$but_href}{/if}
{if $r|fn_check_view_permissions}

{if $but_name || $but_role == "submit" || $but_role == "button_main" || $but_type || $but_role == "big"} {* submit button *}
	<span {if $but_css}style="{$but_css}"{/if} class="submit-button{if $but_role == "big"}-big{/if}{if $but_role == "submit"} strong{/if}{if $but_role == "button_main"} cm-button-main{/if} {$but_meta}"><input {if $but_id}id="{$but_id}"{/if} {if $but_meta}class="{$but_meta}"{/if} type="{$but_type|default:"submit"}"{if $but_name} name="{$but_name}"{/if}{if $but_onclick} onclick="{$but_onclick};{if !$allow_href} return false;{/if}"{/if} value="{$but_text}" {if $tabindex}tabindex="{$tabindex}"{/if} /></span>

{elseif $but_role && $but_role != "submit" && $but_role != "action" && $but_role != "advanced-search" && $but_role != "button"} {* TEXT STYLE *}
	<a {if $but_id}id="{$but_id}"{/if}{if $but_href} href="{$but_href|fn_url}"{/if}{if $but_onclick} onclick="{$but_onclick};{if !$allow_href} return false;{/if}"{/if}{if $but_target} target="{$but_target}"{/if}{if $but_rev} rev="{$but_rev}"{/if} class="{$class}{if $but_meta} {$but_meta}{/if}">{if $but_role == "delete_item"}<img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" class="valign" />{else}{$but_text}{/if}</a>

{elseif $but_role == "action" || $but_role == "advanced-search"} {* BUTTON STYLE *}
	<a {if $but_id}id="{$but_id}"{/if}{if $but_href} href="{$but_href|fn_url}"{/if} {if $but_onclick}onclick="{$but_onclick};{if !$allow_href} return false;{/if}"{/if} {if $but_target}target="{$but_target}"{/if} class="button{if $but_meta} {$but_meta}{/if}">{$but_text}{if $but_role == "action"}&nbsp;<img src="{$images_dir}/icons/but_arrow.gif" width="8" height="7" border="0" alt=""/>{/if}</a>
	
{elseif $but_role == "button"}
	<input {if $but_id}id="{$but_id}"{/if} {if $but_meta}class="{$but_meta}"{/if} type="button" {if $but_onclick}onclick="{$but_onclick};{if !$allow_href} return false;{/if}"{/if} value="{$but_text}" {if $tabindex}tabindex="{$tabindex}"{/if} />

{elseif !$but_role} {* DEFAULT INPUT BUTTON *}
	<input {if $but_id}id="{$but_id}"{/if} class="default-button{if $but_meta} {$but_meta}{/if}" type="submit" onclick="{$but_onclick};{if !$allow_href} return false;{/if}" value="{$but_text}" />
{/if}

{/if}