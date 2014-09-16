{* $Id: langvars_search_form.tpl 5934 2008-09-18 08:18:35Z brook $ *}

{capture name="section"}

<form action="{""|fn_url}" name="seo_rules_search_form" method="get">

<table cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.name}:</label>
		<div class="break">
			<input type="text" name="name" size="20" value="{$search.name}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="seo_rules.manage"}&nbsp;
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.controller}:</label>
		<div class="break">
			<input type="text" name="controller" size="20" value="{$search.controller}" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[seo_rules.manage]" but_role="submit"}
	</td>
</tr>
</table>

{include file="common_templates/advanced_search.tpl" dispatch=$dispatch view_type="seo_rules"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
