{* $Id$ *}

<link href="{$config.skin_path}/styles.css" rel="stylesheet" type="text/css" />
{if $include_file_tree}
<link href="{$config.skin_path}/jqueryFileTree.css" rel="stylesheet" type="text/css" />
{/if}
<!--[if lte IE 7]>
<link href="{$config.skin_path}/styles_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link href="{$config.skin_path}/custom_styles.css" rel="stylesheet" type="text/css" />
{hook name="index:styles"}{/hook}