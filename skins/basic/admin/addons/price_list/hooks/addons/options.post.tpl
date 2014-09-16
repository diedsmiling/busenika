{* $Id: options.post.tpl 7959 2009-09-08 13:01:16Z alexions $ *}

{literal}
<script type="text/javascript">

$('#left_addon_option_price_list_price_list_fields').change(function () {
	var options = '';
	
	$('option', this).each(function (id, elm) {
		options += '<option value="' + this.value + '">' + this.text + '</option>';
	});
	
	$('#addon_option_price_list_price_list_sorting').html(options);
});

</script>
{/literal}