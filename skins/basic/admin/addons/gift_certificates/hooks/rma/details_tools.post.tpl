{* $Id: details_tools.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $return_info.extra.gift_certificates}
	{capture name="url"}gift_certificates.manage?{foreach from=$return_info.extra.gift_certificates item="gift_cert" key="gift_cert_key"}gift_cert_ids[]={$gift_cert_key}&amp;{/foreach}{/capture}
	&nbsp;|&nbsp;{include file="buttons/button.tpl" but_text=$lang.related_gift_cert but_href=$smarty.capture.url but_role="tool"}
{/if}