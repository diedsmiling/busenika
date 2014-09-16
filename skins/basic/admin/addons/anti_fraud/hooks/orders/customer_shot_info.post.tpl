{* $Id: customer_shot_info.post.tpl 6962 2009-03-02 14:40:38Z angel $ *}

{if $order_info.fraud_checking}
{capture name="tools_list"}
	{include file="common_templates/subheader.tpl" title=$lang.fraud_checking} 
	
	<div class="form-field">
		<label>{$lang.ip_address}:</label>
		{$order_info.ip_address}
	</div>
	
	{if $order_info.fraud_checking.B}
	<div class="form-field">
		<label>{$lang.reason}:</label>
		{foreach from=$order_info.fraud_checking.B item=item}
			<p class="fraud-check-risk">{$lang.$item}</p>
		{/foreach}
	</div>
	{/if}
	
	{if $order_info.fraud_checking.G}
	<div class="form-field">
		<label>{$lang.reason}:</label>
		{foreach from=$order_info.fraud_checking.G item=item}
			<p class="fraud-check-normal">{$lang.$item}</p>
		{/foreach}
	</div>
	{/if}
	
	<div class="form-field">
		<label>{$lang.risk_factor}:</label>
		<strong class="risk-factor">{$order_info.fraud_checking.risk_factor|default:$lang.not_available}</strong>
	</div>
	
	<div class="form-field">
		<label>{$lang.decision}:</label>
		{if $order_info.fraud_checking.risk_factor > $addons.anti_fraud.anti_fraud_risk_factor}
			{$lang.anti_fraud_order_not_approved}.
		{else}
			{$lang.anti_fraud_order_approved}.
		{/if}
	</div>
{/capture}

{capture name="but_text"}
	{$lang.fraud_risk}{if $order_info.fraud_checking.risk_factor}: <strong>{$order_info.fraud_checking.risk_factor}</strong>{/if}
{/capture}

{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline-block" link_text=$smarty.capture.but_text only_popup=true}
{/if}
