{* $Id: list.tpl 9722 2010-06-04 11:58:46Z angel $ *}

<div class="wysiwyg-content">
{foreach from=$promotions key="promotion_id" item="promotion"}
	{hook name="promotions:list_item"}
		{include file="common_templates/subheader.tpl" title=$promotion.name}
		{$promotion.detailed_description|default:$promotion.short_description|unescape}
	{/hook}
{foreachelse}
	<p>{$lang.text_no_active_promotions}</p>
{/foreach}
</div>

{capture name="mainbox_title"}{$lang.active_promotions}{/capture}
