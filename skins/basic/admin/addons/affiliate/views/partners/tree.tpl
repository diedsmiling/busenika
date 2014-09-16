{* $Id: tree.tpl 7927 2009-09-01 12:52:17Z angel $ *}

{capture name="mainbox"}

{include file="addons/affiliate/views/partners/components/partner_tree.tpl" partners=$partners level=0 header=true}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.affiliate_tiers_tree content=$smarty.capture.mainbox}
