{* $Id: newsletter_body.tpl 5966 2008-09-22 12:53:21Z brook $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$subscription_info.firstname},<br /><br />

{$header}<br /><br />

{$lang.rb_subscription} <a href="{"subscriptions.view?subscription_id=`$subscription_info.subscription_id`"|fn_url:'C':'http':'&'}">#{$subscription_info.subscription_id}</a> {$lang.rb_will_be_charged_on} {$subscription_info.next_timestamp|date_format:$settings.Appearance.date_format}

{include file="letter_footer.tpl"}