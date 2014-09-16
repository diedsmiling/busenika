{* $Id: diff.tpl 6545 2008-12-12 14:18:29Z zeke $ *}

{capture name="mainbox"}

<pre class="diff-container">{$diff|unescape}</pre>

{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.diff`: `$smarty.request.file`" content=$smarty.capture.mainbox}
