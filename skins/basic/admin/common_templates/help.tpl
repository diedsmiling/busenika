{* $Id: help.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $content}
<div class="float-right">
	{capture name="notes_picker"}
		<div class="object-container">
			{$content}
		</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" act="notes" id="content_`$id`_notes" text=$lang.note content=$smarty.capture.notes_picker link_text="?"}
</div>
{/if}