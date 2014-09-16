{* $Id: progressbar.tpl 10190 2010-07-23 13:29:50Z alexions $ *}

<div class="pb-container">
	<span class="{if $edit_step == "step_one"}active{elseif $completed_steps.step_one == true}complete{/if}">
		<em>1</em>
		{if $edit_step != "step_one"}<a href="{"checkout.checkout&amp;edit_step=step_one"|fn_url}">{/if}{$lang.user_info}{if $edit_step != "step_one"}</a>{/if}
	</span>

	<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

	<span class="{if $edit_step == "step_two"}active{elseif $completed_steps.step_two == true}complete{/if}">
		<em>2</em>
		{if $edit_step != "step_two"}<a href="{"checkout.checkout&amp;edit_step=step_two"|fn_url}">{/if}{$lang.address}{if $edit_step != "step_two"}</a>{/if}
	</span>

	<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

	<span class="{if $edit_step == "step_three"}active{elseif $completed_steps.step_three == true}complete{/if}">
		<em>3</em>
		{if $edit_step != "step_three"}<a href="{"checkout.checkout&amp;edit_step=step_three"|fn_url}">{/if}{$lang.payment_and_shipping}{if $edit_step != "step_three"}</a>{/if}
	</span>

	<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

	<span class="{if $edit_step == "step_four"}active{/if}">
		<em>4</em>
		{$lang.review_and_place_order}
</div>
