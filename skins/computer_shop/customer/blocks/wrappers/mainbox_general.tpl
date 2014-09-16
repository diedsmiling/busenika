{* $Id: mainbox_general.tpl 10606 2010-09-06 09:55:33Z 2tl $ *}
{if $anchor}
<a name="{$anchor}"></a>
{/if}
<div class="mainbox-container{if $details_page} details-page{/if}">
	{if $title}
		{php}		
		if($_GET['dispatch'] != 'product_features.view'):        			
		{/php}
	<span class="mainbox-title"><span>{$title}</span></span>
		{php}		
			endif;
		{/php}
	{/if}
	<div class="mainbox-body">{$content}</div> 
</div>
