{* $Id: tabs_block.post.tpl 7061 2009-03-18 08:54:48Z zeke $ *}
{** block-description:send_to_friend **}

<div id="content_send_to_friend">
<form name="send_to_friend_form" action="{""|fn_url}" method="post">
<input type="hidden" name="selected_section" value="send_to_friend" />
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<div class="form-field">
	<label for="send_name">{$lang.name_of_friend}:</label>
	<input id="send_name" class="input-text" size="50" type="text" name="send_data[to_name]" value="{$send_data.to_name}" />
</div>

<div class="form-field">
	<label for="send_email" class="cm-required cm-email">{$lang.email_of_friend}:</label>
	<input id="send_email" class="input-text" size="50" type="text" name="send_data[to_email]" value="{$send_data.to_email}" />
</div>

<div class="form-field">
	<label for="send_yourname">{$lang.your_name}:</label>
	<input id="send_yourname" size="50" class="input-text" type="text" name="send_data[from_name]" value="{if $send_data.from_name}{$send_data.from_name}{elseif $auth.user_id}{$user_info.firstname} {$user_info.lastname}{/if}" />
</div>

<div class="form-field">
	<label for="send_youremail" class="cm-email">{$lang.your_email}:</label>
	<input id="send_youremail" class="input-text" size="50" type="text" name="send_data[from_email]" value="{if $send_data.from_email}{$send_data.from_email}{elseif $auth.user_id}{$user_info.email}{/if}" />
</div>

<div class="form-field">
	<label for="send_notes" class="cm-required">{$lang.your_message}:</label>
	<textarea id="send_notes"  class="input-textarea" rows="5" cols="72" name="send_data[notes]">{if $send_data.notes}{$send_data.notes}{else}{$product.product|unescape}{/if}</textarea>
</div>

{if $settings.Image_verification.use_for_send_to_friend == "Y"}
	{include file="common_templates/image_verification.tpl" id="send_to_friend" align="left"}
{/if}

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.send but_name="dispatch[send_to_friend.send]"}
</div>

</form>
</div>
