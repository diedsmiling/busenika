{* $Id: notification.tpl 8061 2009-10-06 14:53:50Z zeke $ *}
                                                   
{include file="letter_header.tpl"}

{$lang.dear} {$user_data.firstname},<br /><br />

{$lang.we_would_like_to_inform}: {$reason.amount} {$lang.points} {if $reason.action == 'A'}{$lang.reward_points_subj_added_to}{elseif $reason.action == 'S'}{$lang.reward_points_subj_subtracted_from}{/if}<br />

<b>{$lang.reason}:</b><br />
		{$reason.reason}

{include file="letter_footer.tpl"}