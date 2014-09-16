{* $Id: main.tpl 10165 2010-07-22 07:00:23Z angel $ *}

{block group="top" assign="top"}
{block group="left" assign="left"}
{block group="right" assign="right"}
{block group="bottom" assign="bottom"}
<div id="container" class="container{if !$left|trim && !$right|trim}-long{elseif !$left|trim}-left{elseif !$right|trim}-right{/if}">
	{hook name="index:main_content"}
	<div id="header">{include file="top.tpl"}</div>
	{/hook}
	<div class="wrapper">
		<div id="content">
			<div class="content-helper clear">
				{if $top|trim}
				<div class="header">
					{$top}
				</div>
				{/if}
				
				
				{if $left|trim}
				<div class="left-column">
					{$left}
				</div>
				{/if}
				
				
				{hook name="index:columns"}
				<div class="central-column">
					<div class="central-content">
						{include file="common_templates/breadcrumbs.tpl"}
						{block group="central"}
						{php}
						   //  if (!defined('_LLM_DOMAIN_KEY')){
						   //  define('_LLM_DOMAIN_KEY', '8d4f638e835332d66ae64bc5c93d0492'); 
						   //  }
						   //  require_once($_SERVER['DOCUMENT_ROOT'].'/llm-'._LLM_DOMAIN_KEY.'/llm.php'); 
						   //  $llm = new LLM_client();
						   //  echo $llm->return_links();
						{/php}
					</div>
				</div>
			
				
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
</div>
