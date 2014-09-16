{* $Id: company_field.tpl 7394 2009-04-29 11:43:22Z 2tl $ *}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
{assign var="lang_vendor_supplier" value=$lang.vendor}
{else}
{assign var="lang_vendor_supplier" value=$lang.supplier}
{/if}

{if $company_id}
  ({$lang_vendor_supplier}: {$s_companies[$company_id].company})
{/if}
