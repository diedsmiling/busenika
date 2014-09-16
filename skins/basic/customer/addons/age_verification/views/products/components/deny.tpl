{* $Id: deny.tpl 6986 2009-03-10 13:35:00Z zeke $ *}

<p>{if $product.age_warning_message}{$product.age_warning_message}{else}{$age_warning_message}{/if}</p>

{capture name="mainbox_title"}{$product.product|unescape}{/capture}
