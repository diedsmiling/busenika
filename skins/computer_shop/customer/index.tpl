{* $Id: index.tpl 9807 2010-06-18 07:39:35Z lexa $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$smarty.const.CART_LANGUAGE|lower}">
<head>
{strip}
<title>
{if $page_title}
	{$page_title}
{else}
	{foreach from=$breadcrumbs item=i name="bkt"}
		{if !$smarty.foreach.bkt.first}{$i.title}{if !$smarty.foreach.bkt.last} :: {/if}{/if}
	{/foreach}
	{if !$skip_page_title}{if $breadcrumbs|count > 1} {/if} 
Интернет-магазин товаров для хранения Москва (495) 943-55-04: корзины, ящики, контейнеры с доставкой. {/if}
{/if}
</title>
{/strip}
{include file="meta.tpl"}
<link href="{$images_dir}/icons/favicon.ico" rel="shortcut icon" />
{include file="common_templates/styles.tpl" include_dropdown=true}
{include file="common_templates/scripts.tpl"}
{literal}
<script type="text/javascript" src="//vk.com/js/api/openapi.js?84"></script>

<script type="text/javascript">
  VK.init({apiId: 3518436, onlyWidgets: true});
</script>
{/literal}
</head>

<body>
	{literal}
	<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=155811331150993";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
{/literal}
{if "SKINS_PANEL"|defined}
{include file="demo_skin_selector.tpl"}
{/if}
<div class="helper-container">
	<a name="top"></a>
	{include file="common_templates/loading_box.tpl"}
	{include file="common_templates/notification.tpl"}

	{include file="main.tpl"}

	{if "TRANSLATION_MODE"|defined}
		{include file="common_templates/translate_box.tpl"}
	{/if}
	{if "CUSTOMIZATION_MODE"|defined}
		{include file="common_templates/template_editor.tpl"}
	{/if}
	{if "CUSTOMIZATION_MODE"|defined || "TRANSLATION_MODE"|defined}
		{include file="common_templates/design_mode_panel.tpl"}
	{/if}
    
</div>

{hook name="index:footer"}{/hook}

</body>

</html>
