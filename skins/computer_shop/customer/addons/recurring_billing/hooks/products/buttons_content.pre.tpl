{* $Id: buttons_content.pre.tpl 7574 2009-06-10 13:15:30Z lexa $ *}

{if $product.recurring_plans}

{include file="addons/recurring_billing/views/products/components/recurring_plans.tpl" hide_common_inputs=true}
{capture name="passed_to_buttons_content"}Y{/capture}

{/if}