{* $Id: tooltip.tpl 9190 2010-03-30 11:08:39Z alexions $ *}

{if $tooltip} (<a onclick="return false;" class="cm-tooltip{if $params} {$params}{/if}">?</a>)<span class="hidden cm-tooltip-text">{$tooltip|unescape}</span>{/if}