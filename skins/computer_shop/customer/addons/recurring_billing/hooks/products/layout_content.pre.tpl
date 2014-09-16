{* $Id: view_main_info.override.tpl 7574 2009-06-10 13:15:30Z lexa $ *}

{if $product.recurring_plans}
	{capture name="val_hide_form"}Y{/capture}
	{capture name="val_capture_options_vs_qty"}Y{/capture}
	{capture name="val_capture_buttons"}Y{/capture}
	{capture name="val_separate_buttons"}{/capture}
{/if}