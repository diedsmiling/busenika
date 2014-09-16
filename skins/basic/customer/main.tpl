{* $Id: main.tpl 10165 2010-07-22 07:00:23Z angel $ *}
 
{block group="top" assign="top"}
{block group="left" assign="left"}
{block group="right" assign="right"}
{block group="bottom" assign="bottom"}
<div id="container" class="container{if !$left|trim && !$right|trim}-long{elseif !$left|trim}-left{elseif !$right|trim}-right{/if}">
	{hook name="index:main_content"}
	<div id="header">{include file="top.tpl"}</div>
	{/hook}
	
	<div id="content">
		<div class="content-helper clear">
			{if $top|trim}
			<div class="header">
				{$top}
			</div>
			{/if}
			
			{hook name="index:columns"}
			<div class="central-column">
				<div class="central-content">
					{include file="common_templates/breadcrumbs.tpl"}
					{block group="central"}
				</div>
			</div>
		
			{if $left|trim}
			<div class="left-column">
				{$left}
			</div>
			{/if}
			
			{if $right|trim}
			<div class="right-column">
				{$right}
			</div>
			{/if}
			{/hook}
			
			{if $bottom|trim}
			<div class="bottom clear-both">
				{$bottom}
			</div>
			{/if}
		</div>
	</div>
	
	<div id="footer">
		<div class="footer-helper-container">
			<div class="footer-top-helper"><span class="float-left">&nbsp;</span><span class="float-right">&nbsp;</span></div>
			{include file="bottom.tpl"}
			<div class="footer-bottom-helper"><span class="float-left">&nbsp;</span><span class="float-right">&nbsp;</span></div>
		</div>
	</div>
</div>