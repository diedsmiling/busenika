{* $Id: scripts.post.tpl 6646 2008-12-22 21:00:51Z zeke $ *}

{script src="addons/reward_points/js/func.js"}
<script type="text/javascript">
//<![CDATA[
var price_in_points_with_discounts = '{$addons.reward_points.price_in_points_with_discounts}';
var points_with_discounts = '{$addons.reward_points.points_with_discounts}';

// Extend core function
fn_register_hooks('reward_points', ['check_exceptions']);

//]]>
</script>