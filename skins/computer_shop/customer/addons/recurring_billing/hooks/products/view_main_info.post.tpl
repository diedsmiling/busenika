{* $Id: view_main_info.post.tpl 9055 2010-03-11 13:23:31Z angel $ *}

{if $product.recurring_plans}
	<div class="buttons-container">
		{include file="buttons/button.tpl" but_onclick="$('#block_recurring_plans').click(); jQuery.scrollToElm($('#content_block_recurring_plans'));" but_role="text" but_text=$lang.rb_edit_subscription}
	</div>
{/if}