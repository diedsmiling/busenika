{* $Id: js_news.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $news_id == "0"}
	{assign var="news" value=$default_name}
{else}
	{assign var="news" value=$news_id|fn_get_news_name|default:"`$ldelim`news`$rdelim`"}
{/if}

<tr {if !$clone}id="{$holder}_{$news_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
	{if $position_field}<td><input type="text" name="{$input_name}[{$news_id}]" value="{math equation="a*b" a=$position b=10}" size="3" class="input-text-short" {if $clone}disabled="disabled"{/if} /></td>{/if}
	<td><a href="{"news.update?news_id=`$news_id`"|fn_url}">{$news}</a></td>
	<td>{if !$hide_delete_button && !$view_only}
		<a onclick="jQuery.delete_js_item('{$holder}', '{$news_id}', 'n'); return false;"><img width="12" height="18" border="0" class="hand valign" alt="" src="{$images_dir}/icons/icon_delete.gif"/></a>
		{else}&nbsp;{/if}</td>
	{if !$hide_input}
		<input {if $input_id}id="{$input_id}"{/if} type="hidden" name="{$input_name|escape}" value="{$news_id}" />
	{/if}
</tr>
