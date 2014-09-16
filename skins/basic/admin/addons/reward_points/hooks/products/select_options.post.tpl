{* $Id: select_options.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $vr.point_modifier|floatval}&nbsp;{include file="addons/reward_points/common_templates/point_modifier.tpl" mod_type=$vr.point_modifier_type mod_value=$vr.point_modifier }{/if}