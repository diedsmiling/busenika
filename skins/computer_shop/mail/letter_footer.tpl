{* $Id: letter_footer.tpl 5807 2008-08-26 09:27:03Z zeke $ *}

<p>
{if $user_type == 'A' || $user_data.user_type == 'A'}
	{$lang.admin_text_letter_footer|replace:'[company_name]':$settings.Company.company_name}
{elseif $user_type == 'P' || $user_data.user_type == 'P'}
	{$lang.affiliate_text_letter_footer}
{elseif $user_type == 'S' || $user_data.user_type == 'S'}
	{$lang.supplier_text_letter_footer}
{else}
	{$lang.customer_text_letter_footer}
{/if}
</p>
</body>
</html>