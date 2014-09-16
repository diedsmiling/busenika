{* $Id *}

<link href="{$config.skin_path}/styles.css" rel="stylesheet" type="text/css" />
<link href="{$config.skin_path}/print.css" rel="stylesheet" media="print" type="text/css" />
<link href="/js/fancybox/jquery.fancybox-1.2.6.css" rel="stylesheet" type="text/css" />
{if "TRANSLATION_MODE"|defined || "CUSTOMIZATION_MODE"|defined}
<link href="{$config.skin_path}/design_mode.css" rel="stylesheet" type="text/css" />

{/if}
{if $include_dropdown}
<link href="{$config.skin_path}/dropdown.css" rel="stylesheet" type="text/css" />
{/if}
<!--[if lte IE 7]>
<link href="{$config.skin_path}/styles_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
{hook name="index:styles"}{/hook}
