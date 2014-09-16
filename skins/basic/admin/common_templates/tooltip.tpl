{* $Id$ *}

{if $tooltip} (<a onclick="return false;" class="cm-tooltip{if $params} {$params}{/if}">?</a>)<span class="hidden cm-tooltip-text">{$tooltip|unescape}</span>{/if}