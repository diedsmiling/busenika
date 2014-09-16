{* $Id: manage.tpl 7395 2009-04-29 12:22:05Z zeke $ *}

{capture name="mainbox"}

{capture name="tabsbox"}

{**Product groups section**}
{include file="addons/product_configurator/views/configurator/components/product_groups.tpl"}
{**/Product groups section**}

{**Product classes section**}
{include file="addons/product_configurator/views/configurator/components/product_classes.tpl"}
{**/Product classes section**}

{**Product steps section**}
{include file="addons/product_configurator/views/configurator/components/steps.tpl"}
{**/Product steps section**}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.product_configurator content=$smarty.capture.mainbox select_languages=true}
