{* $Id: langvars_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="langvars_search_form" method="get">

<table cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.search_for_pattern}:</label>
		<div class="break">
			<input type="text" name="q" size="20" value="{$smarty.request.q}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="languages.manage"}&nbsp;
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[languages.manage]" but_role="submit"}
	</td>
</tr>
</table>

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
