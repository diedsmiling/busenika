{* $Id: picker_opts.tpl 6840 2009-01-30 09:28:11Z zeke $ *}
<a class="cm-combo-on cm-combination" id="sw_picker_options">{$lang.show_extra_options_section}</a>
 <div class="cm-picker-extra-options hidden" id="picker_options">

	{if $mailing_lists}
	<div class="form-field">
		<label>{$lang.mailing_lists}:</label>
		{html_checkboxes name="picker_mailing_list_ids" options=$mailing_lists columns="3" selected=$smarty.request.list_id}
	</div>
	{/if}

	<div class="form-field">
		<label>{$lang.format}:</label>
		<select name="picker_mailing_lists[format]">
			<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}">{$lang.txt_format}</option>
			<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}" selected="selected">{$lang.html_format}</option>
		</select>
	</div>
				
	<div class="form-field">			
		<label>{$lang.confirmed}:</label>			
		<input type="hidden" name="picker_mailing_lists[confirmed]" value="0" />
		<input type="checkbox" name="picker_mailing_lists[confirmed]" value="1" class="checkbox" />
	</div>

	<div class="form-field">
		<label>{$lang.notify_user}:</label>
		<input type="hidden" name="picker_mailing_lists[notify_user]" value="0" />
		<input type="checkbox" name="picker_mailing_lists[notify_user]" value="1" class="checkbox" />
	</div>
</div>
