{* $Id: meta.post.tpl 9707 2010-06-03 06:02:00Z klerik $ *}

{if !$smarty.request|fn_seo_is_indexed_page}
<meta name="robots" content="noindex{if $smarty.const.HTTPS === true},nofollow{/if}" />
{/if}

{if $languages|sizeof > 1}
{foreach from=$languages item="language"}
<link title="{$language.name}" dir="rtl" type="text/html" rel="alternate" charset="{$smarty.const.CHARSET}" hreflang="{$language.lang_code|lower}" href="{$config.current_url|fn_link_attach:"sl=`$language.lang_code`"|fn_url}" />
{/foreach}
{/if}
