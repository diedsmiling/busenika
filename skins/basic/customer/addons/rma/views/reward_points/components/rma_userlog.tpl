{* $Id: rma_userlog.tpl 10028 2010-07-09 11:17:28Z 2tl $ *}

	{assign var="statuses" value=$smarty.const.STATUSES_RETURN|fn_get_statuses:true:true:true}
	{assign var="reason" value=$ul.reason|@unserialize}
	{$lang.rma_return}&nbsp;<a href="{"rma.details?return_id=`$reason.return_id`"|fn_url}">&nbsp;<strong>#{$reason.return_id}</strong></a>:&nbsp;{$statuses[$reason.from]}&nbsp;&#8212;&#8250;&nbsp;{$statuses[$reason.to]}


