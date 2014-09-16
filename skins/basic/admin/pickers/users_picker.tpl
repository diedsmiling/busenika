{* $Id: users_picker.tpl 9578 2010-05-24 07:02:51Z  $ *}

{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}

{script src="js/picker.js"}

{if $item_ids && !$item_ids|is_array}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{assign var="display" value=$display|default:"checkbox"}

{if $view_mode != "button"}
{if $display != "radio"}
	<input id="u{$data_id}_ids" type="hidden" name="{$input_name}" value="{if $item_ids}{","|implode:$item_ids}{/if}" />

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th width="100%">{$lang.name}</th>
		<th>&nbsp;</th>
	</tr>
	<tbody id="{$data_id}"{if !$item_ids} class="hidden"{/if}>
	{include file="pickers/js_user.tpl" user_id="`$ldelim`user_id`$rdelim`" email="`$ldelim`email`$rdelim`" user_name="`$ldelim`user_name`$rdelim`" holder=$data_id clone=true}
	{if $item_ids}
	{foreach from=$item_ids item="user" name="items"}
		{assign var="user_info" value=$user|fn_get_user_short_info}
		{include file="pickers/js_user.tpl" user_id=$user email=$user_info.email user_name="`$user_info.firstname` `$user_info.lastname`" holder=$data_id first_item=$smarty.foreach.items.first}
	{/foreach}
	{/if}
	</tbody>
	<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
	<tr class="no-items">
		<td colspan="2"><p>{$no_item_text|default:$lang.no_items}</p></td>
	</tr>
	</tbody>
	</table>
{/if}
{/if}

{if $view_mode != "list"}
	{assign var="but_text" value=$but_text|default:$lang.add_users}
	{if !$no_container}<div class="buttons-container">{/if}
		{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$but_text but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
	{if !$no_container}</div>{/if}

	{capture name="picker_content"}
		{capture name="iframe_url"}{$index_script}?dispatch=profiles.picker{if $display}&amp;display={$display}{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>

		{if $opts_file}
			<div id="users_picker_form_inject" class="cm-picker-options-container">
				{include file=$opts_file}
			</div>
			{assign var="_mode" value="#users_picker_form_inject"}
		{else}
			{assign var="_mode" value=""}
		{/if}

		<div class="buttons-container">
			{if $extra_var}
				{assign var="_act" value="#add_item"}
				{if $display == "checkbox"}
					{assign var="_but_text" value=$lang.add_users}
				{elseif $display == "radio"}
					{assign var="_but_text" value=$lang.choose}
				{/if}
			{else}
				{if $display == "checkbox"}
					{assign var="_but_text" value=$lang.add_users_and_close}
					{assign var="_act" value="#add_item_close"}
				{elseif $display == "radio"}
					{assign var="_but_text" value=$lang.choose}
					{assign var="_act" value="#add_item"}
				{/if}
			{/if}
			{assign var="extra_buttons" value="extra_buttons_$rnd"}
			{if !$extra_var}
				{capture name=$extra_buttons}
					{include file="buttons/button.tpl" but_text=$lang.add_users but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '#add_item', '#users_picker_form_inject')"}
				{/capture}
			{/if}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '`$_act`', '`$_mode`')" but_text=$_but_text cancel_action="close" extra=$smarty.capture.$extra_buttons}
		</div>
	{/capture}
	{include file="pickers/picker_skin.tpl" picker_content=$smarty.capture.picker_content data_id=$data_id but_text=$but_text}
	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['{$data_id}'] = '{$smarty.capture.iframe_url|fn_url|escape:"javascript"}';
		{if $extra_var}
		iframe_extra['{$data_id}'] = '{$extra_var|escape:"javascript"}';
		{/if}
	//]]>
	</script>
{/if}