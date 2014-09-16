{* $Id: notification.tpl 4250 2007-10-22 08:24:34Z seva $ *}
                                                   
{$lang.dear_sirs},<br /><br />

{if $status_inventory == 'D'}
{$lang.supplier_email_header}<br /><br />
{/if}

<b>{$lang.invoice}:</b><br>

{include file="orders/supplier_invoice.tpl"}

{$lang.contact_information}:<br /><br />
<span style="margin-left:20px;">&nbsp;</span>{$settings.Company.company_name}<br />
<span style="margin-left:20px;">&nbsp;</span>{if $settings.Company.company_address}{$settings.Company.company_address}, {/if}
				  {if $settings.Company.company_zipcode}{$settings.Company.company_zipcode}, {/if}
				  {if $settings.Company.company_city}{$settings.Company.company_city}, {/if}
				  {if $settings.Company.company_state && $settings.Company.company_country}{$settings.Company.company_state|fn_get_state_name:$settings.Company.company_country|escape}, {/if}
				  {$settings.Company.company_country|fn_get_country_name|escape}<br />
<span style="margin-left:20px;">&nbsp;</span>{if $settings.Company.company_phone}{$lang.phone}:&nbsp;{$settings.Company.company_phone}{if $settings.Company.company_fax}, {/if}{/if}{if $settings.Company.company_fax}{$lang.fax}:&nbsp;{$settings.Company.company_fax}{/if}.<br />
<span style="margin-left:20px;">&nbsp;</span>{$lang.email}:&nbsp;{$settings.Company.company_orders_department}
