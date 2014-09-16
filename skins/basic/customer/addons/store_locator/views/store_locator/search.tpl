{* $Id: search.tpl 7987 2009-09-11 05:37:35Z angel $ *}

{if $store_locations}
{literal}
<script type="text/javascript">
//<![CDATA[
function fn_move_map(id)
{
	$('#map_canvas').insertAfter($('#loc_'+ id));
}
//]>
</script>
{/literal}
{foreach from=$store_locations item=loc key=num}
{if $num != 0}
<hr />
{/if}
<script type="text/javascript">
//<![CDATA[
function fn_show_marker_{$loc.store_location_id}()
{literal}{{/literal}
if (marker) {literal}{{/literal}
	map.removeOverlay(marker);
	marker = null;
{literal}}{/literal}
var loc = new GLatLng({$loc.latitude|doubleval}, {$loc.longitude|doubleval});
map.setCenter(loc, 17);
marker = new GMarker(loc);
GEvent.addListener(marker, "click", function() {literal}{{/literal}
	var html = '<div style="padding-right: 10px"><strong>{$loc.name|escape:javascript}</strong><p>{$loc.city|escape:javascript}, {$loc.country_title|escape:javascript}</p><\/div>';
	marker.openInfoWindowHtml(html);
	{literal}});{/literal}

map.addOverlay(marker);
GEvent.trigger(marker, "click");
{literal}}{/literal}
//]]>
</script>
<div class="product-container wysiwyg-content clear" id="loc_{$loc.store_location_id}">
<h1>{$loc.name}</h1>
{$loc.description|unescape}
{$loc.city}, {$loc.country_title}
<p>{include file="buttons/button.tpl" but_role="text" but_onclick="fn_move_map('`$loc.store_location_id`'); fn_show_marker_`$loc.store_location_id`();" but_text=$lang.view_on_map}</p>
</div>
{if $num == 0}
<div style="border: 1px solid #979797; width: 400px; height: 300px;" id="map_canvas"></div>
{if !$smarty.capture.goole_api}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$addons.store_locator.google_key}&amp;hl={$smarty.const.CART_LANGUAGE|fn_store_locator_google_langs}" type="text/javascript"></script>
{capture name="goole_api"}Y{/capture}
{/if}
<script type="text/javascript">
//<![CDATA[
var map;
var marker = null;

map = new GMap2(document.getElementById("map_canvas"));
map.addControl(new GSmallMapControl());
map.addControl(new GScaleControl());

fn_show_marker_{$loc.store_location_id}();
//]]>
</script>
{/if}
{/foreach}
{else}
<p class="no-items">{$lang.no_data}</p>
{/if}

{capture name="mainbox_title"}{$lang.store_locator}{/capture}