{* $Id: view.tpl 9655 2010-05-28 09:17:48Z lexa $ *}

<div class="wysiwyg-content">
	{hook name="pages:page_content"}
	{$page.description|unescape}
	{/hook}
	{capture name="mainbox_title"}{$page.page}{/capture}
</div>
	
{hook name="pages:page_extra"}
{/hook}