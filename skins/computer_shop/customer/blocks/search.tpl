{* $Id: search.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
{** block-description:search **}
<form action="{""|fn_url}" name="search_form" method="get">
<input type="hidden" name="subcats" value="Y" />
<input type="hidden" name="type" value="extended" />
<input type="hidden" name="status" value="A" />
<input type="hidden" name="pshort" value="Y" />
<input type="hidden" name="pfull" value="Y" />
<input type="hidden" name="pname" value="Y" />
<input type="hidden" name="pkeywords" value="Y" />
<input type="hidden" name="search_performed" value="Y" />
{hook name="search:additional_fields"}{/hook} 

{$lang.search}:
<p>
{strip}
<input type="text" name="q" value="{$search.q}" onfocus="this.select();" class="input-text" />

{if $settings.General.search_objects}
	{include file="buttons/go.tpl" but_name="search.results" alt=$lang.search}
{else}
	{include file="buttons/go.tpl" but_name="products.search" alt=$lang.search}
{/if}
{/strip}
</p>
</form>
