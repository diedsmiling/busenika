{* $Id: customer_notes.tpl 9403 2010-05-07 13:11:06Z klerik $ *}

{hook name="checkout:notes"}
<p>{$lang.text_customer_notes}:</p>
<textarea class="input-textarea checkout-textarea" name="customer_notes" cols="60" rows="8"></textarea>
{/hook}
