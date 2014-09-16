{* $Id: events.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="subscriptions_list_form">

<div class="items-container" id="subscriptions_list">
{foreach from=$recurring_billing_data.events item="event_name" key="key"}
	{if $recurring_events.$key}
		<div class="object-group clear">
			<div class="float-left object-name">
				{$lang.$event_name}
			</div>
		</div>
		{foreach from=$recurring_events.$key item="subs_id"}
			{assign var="subs_data" value=$subs_id|fn_get_recurring_subscription_info}
			{include file="common_templates/price.tpl" value=$subs_data.price assign="subs_price"}
			{assign var="order_link" value="orders.details?order_id=`$subs_data.order_id`"|fn_url}
			{assign var="_details" value="`$lang.customer`: <strong>`$subs_data.firstname` `$subs_data.lastname`</strong> | `$lang.rb_price`: <strong>`$subs_price`</strong> | `$lang.order`: <a class=\"link\" href=\"`$order_link`\">#`$subs_data.order_id`</a>"}
			{assign var="_text" value="`$lang.rb_subscription` #`$subs_id`"}
			
			{include file="common_templates/object_group.tpl" id="`$key`_`$subs_id`" text=$_text details=$_details href="subscriptions.process_event?subscription_id=`$subs_id`&amp;type=`$key`" link_text=$lang.process element="-elements" act="default" checkbox_name="process_subscriptions[`$key`][]" checkbox_value=$subs_id checked=true no_rev="true" main_link="subscriptions.update?subscription_id=`$subs_id`"}
		{/foreach}
	{/if}
{foreachelse}
	<p class="no-items">{$lang.no_data}</p>
{/foreach}
<!--subscriptions_list--></div>

{if $recurring_billing_data.events}
	<div class="table-tools">
		<a class="cm-check-items cm-on underlined" name="check_all">{$lang.select_all}</a>&nbsp;|&nbsp;
		<a class="cm-check-items cm-off underlined" name="check_all">{$lang.unselect_all}</a>
	</div>
{/if}

<div class="buttons-container buttons-bg">
	<div class="float-left">
		{include file="buttons/button.tpl" but_text=$lang.rb_process_selected_events but_name="dispatch[subscriptions.process_events]" but_meta="cm-confirm cm-process-items" but_role="button_main"}
	</div>
</div>
</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.rb_subscription_events content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
