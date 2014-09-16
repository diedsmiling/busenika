{* $Id: scripts.post.tpl 6929 2009-02-20 07:01:33Z zeke $ *}

{if $smarty.const.CONTROLLER != "checkout"}
{script src="addons/recurring_billing/js/func.js"}
<script type="text/javascript">

// Extend core function
fn_register_hooks('recurring_billing', ['check_exceptions']);

</script>
{/if}