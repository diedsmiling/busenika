{* $Id: statuses.tpl 5934 2008-09-18 08:18:35Z brook $ *}

{include file="views/statuses/update.tpl" privilege="manage_gift_certificates" item=$lang.statuses title=$lang.gift_certificate_statuses status_type=$smarty.const.STATUSES_GIFT_CERTIFICATE no_inventory="Y" extra_fields="<input type=\"hidden\" name=\"redirect_mode\" value=\"`$smarty.request.mode`\">" }
