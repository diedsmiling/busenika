{* $Id: orders_picker.tpl 9578 2010-05-24 07:02:51Z  $ *}

{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}

{script src="js/picker.js"}

{if $item_ids && !$item_ids|is_array}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{if $view_mode != "button"}
	

	<input id="o{$data_id}_ids" type="hidden" name="{$input_name}" value="{if $item_ids}{","|implode:$item_ids}{/if}" />
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
		<tr>
			<th width="10%">{$lang.id}</th>
			<th width="15%">{$lang.status}</th>
			<th width="25%">{$lang.customer}</th>
			<th width="25%">{$lang.date}</th>
			<th width="24%" class="right">{$lang.total}</th>
			{if !$view_only}<th>&nbsp;</th>{/if}
		</tr>
		<tbody id="{$data_id}"{if !$item_ids} class="hidden"{/if}>
		{include file="pickers/js_order.tpl" order_id="`$ldelim`order_id`$rdelim`" status="`$ldelim`status`$rdelim`" customer="`$ldelim`customer`$rdelim`" timestamp="`$ldelim`timestamp`$rdelim`" total="`$ldelim`total`$rdelim`" holder=$data_id clone=true}
		{foreach from=$item_ids item="o"}
			{assign var="order_info" value=$o|fn_get_order_short_info}
			{include file="pickers/js_order.tpl" order_id=$o status=$order_info.status customer="`$order_info.firstname` `$order_info.lastname`" timestamp=$order_info.timestamp total=$order_info.total holder=$data_id}
		{/foreach}
		</tbody>
		<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
		<tr class="no-items">
			<td colspan="5"><p>{$no_item_text}</p></td>
		</tr>
		</tbody>
	</table>
{/if}

{if $view_mode != "list"}

	{if !$no_container}<div class="buttons-container">{/if}
		{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$lang.add_orders but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
	{if !$no_container}</div>{/if}

	{capture name="picker_content"}
		{capture name="iframe_url"}orders.picker{if $extra_var}?extra={$extra_var|escape:url}{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{assign var="extra_buttons" value="extra_buttons_$rnd"}
			{if !$extra_var}
				{assign var="_but_text" value=$lang.add_orders_and_close}
				{assign var="_act" value="#add_item_close"}
				{capture name=$extra_buttons}
					{include file="buttons/button.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '#add_item')" but_text=$lang.add_orders}
				{/capture}
			{else}
				{assign var="_but_text" value=$lang.add_orders}
				{assign var="_act" value="#add_item"}
			{/if}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '`$_act`')" but_text=$_but_text cancel_action="close" extra=$smarty.capture.$extra_buttons}
		</div>
	{/capture}
	{include file="pickers/picker_skin.tpl" picker_content=$smarty.capture.picker_content data_id=$data_id but_text=$lang.add_orders}
	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['{$data_id}'] = '{$smarty.capture.iframe_url|fn_url|escape:"javascript"}';
		{if $extra_var}
		iframe_extra['{$data_id}'] = '{$extra_var|escape:"javascript"}';
		{/if}
	//]]>
	</script>
{/if}