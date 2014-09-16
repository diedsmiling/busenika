{* $Id: search_form.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

<div class="search-field">
	<label for="configurable">{$lang.configurable}:</label>
	<select name="configurable" id="configurable">
		<option value="">--</option>
		<option value="C" {if $search.configurable == "C"}selected="selected"{/if}>{$lang.yes}</option>
		<option value="N" {if $search.configurable == "P"}selected="selected"{/if}>{$lang.no}</option>
	</select>
</div>