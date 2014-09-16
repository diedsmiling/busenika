{* $Id: global_update.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

<div class="form-field">
	<label for="gu_points">{$lang.price_in_points}:</label>
	<input type="text" id="gu_points" name="update_data[price_in_points]" size="6" value="" class="input-text" />
	<select name="update_data[price_in_points_type]">
		<option value="A" >{$lang.points_lower}</option>
		<option value="P" >%</option>
	</select>
</div>