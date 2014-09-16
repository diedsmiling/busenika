{* $Id: sortable_position_scripts.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{script src="js/iutil.js"}
{script src="js/idrag.js"}
{script src="js/idrop.js"}
{script src="js/isortables.js"}

<script type="text/javascript">
//<![CDATA[
lang.text_position_updating = '{$lang.text_position_updating}';
var update_sortable_url = '{"tools.update_position?table=`$sortable_table`&id_name=`$sortable_id_name`"|fn_url:'A':'rel':'&'}';

{literal}
$(document).ready(
	function () {
		var positionids = [];
		$('.cm-sortable').Sortable( {
			accept : 	'cm-sortable-row',
			containment:	'.cm-sortable',
			tolerance:	'intersect',
			opacity: 	0.9,
			fx:		false,
			axis:		'vertically',
			containment_padding : {x:0, y:7, h:0, w:0},
			onStart: function(elm) {
				var i = 0;
				positionids = [];
				$("*[class*='cm-sortable-id-']").each(function() {
					var matched = $(this).attr('class').match(/cm-sortable-id-([^\s]+)/i);
					positionids[i++] = matched[1];
				});
			},
			onStop: function(elm) {
				var i = 0;
				var changed_positions = [];
				var changed_ids = [];
				$("*[class*='cm-sortable-id-']").each(function() {
					var matched = $(this).attr('class').match(/cm-sortable-id-([^\s]+)/i);
					if (positionids[i] != matched[1]) {
						changed_positions.push(i);
						changed_ids.push(matched[1]);
					}
					i++;
				});
				if (changed_ids.length > 0) {
					var data_obj = {};
					data_obj['positions'] = changed_positions.join(',');
					data_obj['ids'] = changed_ids.join(',');
					jQuery.ajaxRequest(update_sortable_url, {method: 'get', caching: false, message: lang.text_position_updating, data: data_obj});
					return true;
				}
			}
		});
	}
);
//]]>
</script>
{/literal}