{* $Id: totals_extra.pre.tpl 6822 2009-01-23 07:48:08Z angel $ *}

{if $cart.points_info.total_price && $user_points}
<div class="form-field">
	<label for="points_to_use">{$lang.points_to_use}:</label>
	<input type="text" class="input-text-medium" name="points_to_use" id="points_to_use" size="20" value="" />&nbsp;({$lang.available}:&nbsp;{$user_info.points}&nbsp;{$user_points|default:"0"}&nbsp;/&nbsp;{$lang.maximum}:&nbsp;{$cart.points_info.total_price})
</div>
{/if}