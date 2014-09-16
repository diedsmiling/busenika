{* $Id: update_fields.tpl 6966 2009-03-04 06:42:39Z angel $ *}

<div class="form-field">
	<label for="age_verification">{$lang.age_verification}:</label>
	<input type="hidden" name="{$array_name}[age_verification]" value="N" /><input type="checkbox" id="age_verification" name="{$array_name}[age_verification]" value="Y" {if $record.age_verification == "Y"}checked="checked"{/if} class="checkbox" />
</div>

<div class="form-field">
	<label for="age_limit">{$lang.age_limit}:</label>
	<input type="text" id="age_limit" name="{$array_name}[age_limit]" size="10" maxlength="2" value="{$record.age_limit|default:"0"}" class="input-text-short" /> {$lang.years}
</div>

<div class="form-field">
	<label for="age_warning_message">{$lang.age_warning_message}:</label>
	<textarea id="age_warning_message" name="{$array_name}[age_warning_message]" cols="55" rows="4" class="input-textarea-long">{$record.age_warning_message}</textarea>
</div>