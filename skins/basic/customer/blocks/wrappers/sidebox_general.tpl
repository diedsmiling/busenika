{* $Id: sidebox_general.tpl 10606 2010-09-06 09:55:33Z 2tl $ *}

<div class="{$sidebox_wrapper|default:"sidebox-wrapper"} {if $hide_wrapper}hidden cm-hidden-wrapper{/if}">
	<h3 class="sidebox-title{if $header_class} {$header_class}{/if}"><span>{$title}</span></h3>
	<div class="sidebox-body">{$content|default:"&nbsp;"}</div>
	<div class="sidebox-bottom"><span>&nbsp;</span></div>
</div>