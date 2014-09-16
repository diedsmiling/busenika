{* $Id: items_content.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $return_info.extra.gift_certificates}
	<div class="form-field">
		<label>{$lang.gift_certificates}</label>
		{assign var="return_current_url" value=$config.current_url|escape:"url"}
		{foreach from=$return_info.extra.gift_certificates item="gift_cert" key="gift_cert_key"}
			<div><a href="{"gift_certificates.delete?gift_cert_id=`$gift_cert_key`&amp;extra[return_id]=`$smarty.request.return_id`&amp;return_url=`$return_current_url`"|fn_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="" align="bottom" /></a>&nbsp;<a class="text-button-link" href="{"gift_certificates.update?gift_cert_id=`$gift_cert_key`"|fn_url}">{$gift_cert.code}</a>&nbsp;({include file="common_templates/price.tpl" value=$gift_cert.amount})</div>
		{/foreach}
	</div>
{/if}