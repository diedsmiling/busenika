{* $Id: image_verification.tpl 10249 2010-07-28 12:58:35Z klerik $ *}

{if ""|fn_needs_image_verification == true}
	{assign var="is" value="Image_verification"|fn_get_settings}
	
	<p{if $align} class="{$align}"{/if}>{$lang.image_verification_body}</p>
	{assign var="id_uniqid" value=$id|uniqid}
	{if $sidebox}
		<p><img id="verification_image_{$id}" class="image-captcha valign" src="{"image.captcha?verification_id=`$SESS_ID`:`$id`&amp;`$id_uniqid`&amp;"|fn_url:'C':'rel':'&'}" alt="" onclick="this.src += 'reload' ;" width="{$is.width}" height="{$is.height}" /></p>
	{/if}

	<p><input class="captcha-input-text valign" type="text" name="verification_answer" value= "" autocomplete="off" />
	{if !$sidebox}
		<img id="verification_image_{$id}" class="image-captcha valign" src="{"image.captcha?verification_id=`$SESS_ID`:`$id`&amp;`$id_uniqid`&amp;"|fn_url:'C':'rel':'&'}" alt="" onclick="this.src += 'reload' ;"  width="{$is.width}" height="{$is.height}" />
	{/if}</p>
{/if}
