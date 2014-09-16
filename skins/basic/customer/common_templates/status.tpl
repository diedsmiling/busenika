{* $Id: status.tpl 9196 2010-03-30 13:03:21Z lexa $ *}

{if !$order_status_descr}
	{if !$status_type}{assign var="status_type" value=$smarty.const.STATUSES_ORDER}{/if}
	{assign var="order_status_descr" value=$status_type|fn_get_statuses:true}
{/if}

{strip}
{if $display == "view"}
	{$order_status_descr.$status}
{elseif $display == "select"}
	{html_options name=$name options=$order_status_descr selected=$status id=$select_id}
{elseif $display == "checkboxes"}
	<div>
		{html_checkboxes name=$name options=$order_status_descr selected=$status columns=4}
	</div>
{/if}
{/strip}
