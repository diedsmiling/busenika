{* $Id: edit_product_options.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

<div class="form-field">
	<label for="point_modifier_{$id}">{$lang.earned_point_modifier}&nbsp;/&nbsp;{$lang.type}:</label>
	<input type="text" id="point_modifier_{$id}" name="option_data[variants][{$num}][point_modifier]" value="{$vr.point_modifier}" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][{$num}][point_modifier_type]">
		<option value="A" {if $vr.point_modifier_type == "A"}selected="selected"{/if}>({$lang.points_lower})</option>
		<option value="P" {if $vr.point_modifier_type == "P"}selected="selected"{/if}>(%)</option>
	</select>
</div>