{* $Id: discussion_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="discussion_search_form" method="get">
<input type="hidden" name="object_type" id="obj_type" value="{$search.object_type|default:"P"}" />
<input type="hidden" name="dispatch" value="discussion_manager.manage" />

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="author">{$lang.author}:</label>
		<div class="break">
			<input type="text" class="input-text" id="author" name="name" value="{$search.name}" />
		</div>
	</td>
	<td class="search-field">
		<label for="message">{$lang.message}:</label>
		<div class="break">
			<input type="text" class="input-text" id="message" name="message" value="{$search.message}" />
		</div>
	</td>
	<td class="search-field">
		<label for="rating_value">{$lang.rating}:</label>
		<div class="break">
			<select name="rating_value" id="rating_value">
			<option value="">--</option>
				<option value="5" {if $search.rating_value == "5"}selected="selected"{/if}>{$lang.excellent}</option>
				<option value="4" {if $search.rating_value == "4"}selected="selected"{/if}>{$lang.very_good}</option>
				<option value="3" {if $search.rating_value == "3"}selected="selected"{/if}>{$lang.average}</option>
				<option value="2" {if $search.rating_value == "2"}selected="selected"{/if}>{$lang.fair}</option>
				<option value="1" {if $search.rating_value == "1"}selected="selected"{/if}>{$lang.poor}</option>
			</select>
		</div>
	</td>
	<td class="search-field">
		<label for="discussion_type">{$lang.discussion}:</label>
		<div class="break">
			<select name="type" id="discussion_type">
				<option value="">--</option>
				<option value="B" {if $search.type == "B"}selected="selected"{/if}>{$lang.rating} & {$lang.communication}</option>
				<option value="R" {if $search.type == "R"}selected="selected"{/if}>{$lang.rating}</option>
				<option value="C" {if $search.type == "C"}selected="selected"{/if}>{$lang.communication}</option>
			</select>
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[discussion_manager.manage]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label for="ip_address">{$lang.ip_address}:</label>
	<input type="text" class="input-text" id="ip_address" name="ip_address" value="{$search.ip_address}" />
</div>

<div class="search-field">
	<label for="status">{$lang.approved}:</label>
	<select name="status" id="status">
		<option value="">--</option>
		<option value="Y" {if $search.status == "Y"}selected="selected"{/if}>{$lang.yes}</option>
		<option value="N" {if $search.status == "N"}selected="selected"{/if}>{$lang.no}</option>
	</select>
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="discussion_search_form"}
</div>

<div class="search-field">
	<label for="sort_by">{$lang.sort_by}:</label>
	<select name="sort_by" id="sort_by">
		<option {if $search.sort_by == "name"}selected="selected"{/if} value="name">{$lang.author}</option>
		<option {if $search.sort_by == "status"}selected="selected"{/if} value="status">{$lang.approved}</option>
		<option {if $search.sort_by == "timestamp"}selected="selected"{/if} value="timestamp">{$lang.date}</option>
		<option {if $search.sort_by == "ip_address"}selected="selected"{/if} value="ip_address">{$lang.ip_address}</option>
	</select>

	<select name="sort_order">
		<option {if $search.sort_order == "desc"}selected="selected"{/if} value="desc">{$lang.desc}</option>
		<option {if $search.sort_order == "asc"}selected="selected"{/if} value="asc">{$lang.asc}</option>
	</select>
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="discussion_manager.manage" view_type="discussion"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
