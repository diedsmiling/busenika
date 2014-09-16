{* $Id: amchart.tpl 10422 2010-08-13 12:10:50Z 2tl $ *}
<!-- amchart script-->
	<div id="flashcontent_{$chart_id}am{$type}" align="center">
		<strong>{$lang.upgrade_flash_player}</strong>
	</div>
	{assign var="setting_type" value=$set_type|default:$type}
	{assign var="_uid" value=0|rand:10000}
	<script type="text/javascript">
		// <![CDATA[
		{literal}
		function amChartInited(chart_id)
		{
			var flashMovie = document.getElementById(chart_id);
			flashMovie.setParam('labels.label[0].text', chart_titles[chart_id]);
		}
		if (!chart_titles) {
			var chart_titles = {};
		}
		{/literal}

		var so{$_uid} = new SWFObject("{$config.current_path}/lib/amcharts/am{$type}/am{$type}.swf", "{$chart_id}am{$type}", "{$chart_width|default:'650'}", "{$chart_height|default:'500'}", "8", "{$chart_bgcolor|default:'#FFFFFF'}");
		so{$_uid}.addVariable("path", "{$config.current_path}/lib/amcharts/am{$type}/");
		so{$_uid}.addVariable("settings_file", escape("{$config.current_path}/lib/amcharts/am{$type}/am{$setting_type}_settings.xml"));
		so{$_uid}.addVariable("chart_data", encodeURIComponent('{$chart_data|escape:"javascript"}'));
		so{$_uid}.addVariable("preloader_color", "#999999");
		so{$_uid}.addVariable("chart_id", "{$chart_id}am{$type}");
		so{$_uid}.write("flashcontent_{$chart_id}am{$type}");
		chart_titles['{$chart_id}am{$type}'] = '<strong>{$chart_title|escape:javascript}</strong>';

		delete so{$_uid};
		// ]]>
	</script>
<!-- end of amchart script -->
