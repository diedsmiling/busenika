{* $Id: help.tpl 10268 2010-07-29 11:32:59Z angel $ *}

{if $content}
{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}
{if !$link_only}<div class="float-right">{/if}
	{capture name="notes_picker"}
		<div class="object-container">
			{$content}
		</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" act="notes" id="content_`$id`_notes" text=$text content=$smarty.capture.notes_picker link_text=$link_text|default:"?"}
{if !$link_only}</div>{/if}
{/if}