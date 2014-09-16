{* $Id: subheader.tpl 6361 2008-11-18 14:22:03Z lexa $ *}

<h2 class="{$subheader_class|default:"subheader"}">
	{if $mode == "translate"}
		
	{else}
		{if $notes}
			{include file="common_templates/help.tpl" content=$notes id=$notes_id}
		{/if}
		{$title}
	{/if}
</h2>