{* $Id: map.tpl 7599 2009-06-23 05:26:26Z lexa $ *}

{script src="js/picker.js"}

{if !$smarty.capture.goole_api}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$addons.store_locator.google_key}&amp;hl={$smarty.const.CART_LANGUAGE|fn_store_locator_google_langs}" type="text/javascript"></script>
{capture name="goole_api"}Y{/capture}
{/if}

<script type="text/javascript">
//<![CDATA[
var map;
var saved_point = null;
var marker = null;
var city = null;

var latitude_name = '';
var longitude_name = '';
lang.text_address_not_found = '{$lang.text_address_not_found|escape:javascript}';

{literal}
function fn_init_map(country_field, city_field, latitude_field, longitude_field)
{
	saved_point = null;
	marker = null;

	latitude_name = latitude_field;
	longitude_name = longitude_field;

	map = new GMap2(document.getElementById("map_canvas"), {draggableCursor: 'crosshair', draggingCursor: 'pointer'});
	map.addControl(new GLargeMapControl());
	map.addControl(new GScaleControl());

	if ($('#' + latitude_name).val() && $('#' + longitude_name).val()) {
		var start = new GLatLng($('#' + latitude_name).val(), $('#' + longitude_name).val());
		map.setCenter(start, 15);
		fn_update_point(start);
	} else {
		var address = '';
		var value = $('#' + city_field).val();
		if (value) {
			city = value;
			address = value;
		}

		var value = $('#' + country_field).val();
		if (value) {
			if (address) {
				address += ' ';
			}

			address += value;
		}

		var geocoder = new GClientGeocoder();
		geocoder.getLatLng(address, function(point) {
			if (!point) {
				alert(lang.text_address_not_found + ': ' + address);
			} else {
				if (city && city.length) {
					map.setCenter(point, 13);
				} else {
					map.setCenter(point, 5);
				}
			}
		});
	}

	GEvent.addListener(map, 'click', function(overlay, point)
	{
		if (overlay) {
		} else if (point) {
			fn_update_point(point) ;
		}
	});
}

function fn_update_point(point)
{
	if (saved_point && marker) {
		map.removeOverlay(marker);
	}

	marker = new GMarker(point);

	map.addOverlay(marker);
	saved_point = point;
}

function fn_save_point()
{
	if (saved_point) {
		$('#' + latitude_name).val(saved_point['y']);
		$('#' + longitude_name).val(saved_point['x']);
	}
	jQuery.hide_picker();
}
{/literal}
//]]>
</script>

<div class="popup-content cm-popup-box cm-picker hidden" id="map_picker">
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$lang.select_coordinates}:</h3>
	</div>
	<div class="cm-popup-content-footer">
		<div class="object-container">
			<div class="map-canvas" id="map_canvas"></div>
		</div>

		<form name="map_picker" action="" method="">
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_onclick="fn_save_point()" but_type="button" but_text=$lang.set cancel_action="close"}
		</div>
		</form>
	</div>
</div>
