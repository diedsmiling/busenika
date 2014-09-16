{* $Id: button_popup.tpl 6730 2009-01-09 10:02:44Z zeke $ *}

{capture name="pop"}
window.open(this.href{if $href_extra} + {$href_extra}{/if},'{$window|default:"popupwindow"}','width={$width|default:"450"},height={$height|default:"350"},toolbar={$toolbar|default:"yes"},status={$status|default:"no"},scrollbars={$scrollbars|default:"yes"},resizable={$resizable|default:"no"},menubar={$menubar|default:"yes"},location={$location|default:"no"},direction={$direction|default:"no"}');
{/capture}
{include file="buttons/button.tpl" but_onclick=$smarty.capture.pop but_href=$but_href but_text=$but_text}