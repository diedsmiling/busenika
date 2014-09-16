{* $Id: recurring_plans.tpl 6967 2009-03-04 09:26:06Z angel $ *}
{** block-description:rb_subscription **}

{if $product.recurring_plans && !$smarty.capture.passed_to_buttons_content}

{include file="addons/recurring_billing/views/products/components/recurring_plans.tpl" hide_common_inputs=false}

{/if}