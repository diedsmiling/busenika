{* $Id: subheader.tpl 10234 2010-07-28 07:46:31Z angel $ *}
{if $anchor}
<a name="{$anchor}"></a>
{/if}
<h2 class="{$class|default:"subheader"}">
	{if $notes|trim}
		<div class="float-left">{include file="common_templates/help.tpl" content=$notes id=$notes_id text=$text}&nbsp;</div>
	{/if}
	{$extra}
	{$title}
</h2>