{* $Id: welcome.tpl 5807 2008-08-26 09:27:03Z zeke $ *}

{capture name="mainbox"}
	Welcome	text
{/capture}
{include file="common_templates/mainbox.tpl" title="Welcome" content=$smarty.capture.mainbox}
