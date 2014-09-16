{* $Id: pages_link.tpl 7722 2009-07-16 07:13:34Z lexa $ *}
<div class="form-field">
	<label for="page_link" class="cm-required">{$lang.page_link}:</label>
	<input type="text" name="page_data[link]" id="page_link" size="55" value="{$page_data.link}" class="input-text" />
</div>

<div class="form-field">
	<label for="page_link_new_window">{$lang.open_in_new_window}:</label>
	<input type="hidden" name="page_data[new_window]" value="0" />
	<input {if $page_data.new_window != "0"}checked="checked"{/if} type="checkbox" name="page_data[new_window]" id="page_link_new_window" size="55" value="1" class="checkbox" />
</div>

