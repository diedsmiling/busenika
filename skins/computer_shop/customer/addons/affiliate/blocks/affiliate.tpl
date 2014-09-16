{* $Id: affiliate.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:affiliate **}

{if $auth.is_affiliate}
<ul class="arrows-list">
	<li><a href="{"banners_manager.manage?banner_type=T"|fn_url}">{$lang.text_banners}</a></li>
	<li><a href="{"banners_manager.manage?banner_type=G"|fn_url}">{$lang.graphic_banners}</a></li>
	<li><a href="{"banners_manager.manage?banner_type=P"|fn_url}">{$lang.product_banners}</a></li>
	<li class="delim"></li>
	<li><a href="{"affiliate_plans.list"|fn_url}">{$lang.affiliate_plan}</a></li>
	<li><a href="{"partners.list"|fn_url}">{$lang.balance_account}</a></li>
	<li class="delim"></li>
	<li><a href="{"aff_statistics.commissions"|fn_url}">{$lang.commissions}</a></li>
	<li><a href="{"payouts.list"|fn_url}">{$lang.payouts}</a></li>
</ul>
{/if}
