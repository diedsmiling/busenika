{* $Id: view.tpl 8675 2010-01-25 08:23:14Z angel $ *}

{if $news}

<div class="wysiwyg-content">
	{capture name="tabsbox"}
	<div id="content_news">
		<h5 class="info-field-title">
			<em class="float-right">{$lang.date_added}: {$news.date|date_format:"`$settings.Appearance.date_format`"}</em>
			{$news.news}
		</h5>
		<div class="info-field-body wysiwyg-content">
			{$news.description|unescape}
		</div>
	</div>
	{hook name="news:view"}
	{/hook}
	
	{/capture}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}
</div>

{capture name="mainbox_title"}{$lang.site_news}{/capture}

{/if}
