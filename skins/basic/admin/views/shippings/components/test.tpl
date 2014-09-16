{* $Id: test.tpl 5805 2008-08-24 20:31:37Z zeke $ *}
<html>
<head>
<title>{$lang.shipping}</title>
<meta http-equiv="content-type" content="text/html; charset={$smarty.const.CHARSET}" />
</head>
{literal}
<style>
body,th,td,tt,p,div,span {
	color: #000000;
	font-family: tahoma, verdana, arial, sans-serif;
	font-size: 11px;
}
body,form,div {
	margin-top:	0px;
	margin-bottom: 0px;
	margin-left: 0px;
	margin-right: 0px;
}
body {
	background-color: #fbfbfb;
}
p {
	margin: 6px 0px;
}
a:link, a:visited, a:active {
	color: #296dc1;
	text-decoration: none;
}
a:hover	{
	color: #f27a00;
	text-decoration: underline;
}
.table {
	margin: 0px;
	border: 1px solid #a5afb8/*#D0DBE3*/;
}

.table th {
	color: #151515;
	font-weight: bold;
	text-transform: uppercase;
}
.table th {
	background-color: #c6d5e8/*#d1d9e4/*#b3e3fc*/;
	white-space: nowrap;
	padding: 7px 8px 6px 8px;
	text-align: left;
	border-bottom: 1px solid #a3aabe;
}
.table .table-row {
	background-color: #f1f8ff;
}
</style>
{/literal}
<body onLoad="self.focus()">

{if $service}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>&nbsp;</th>
	<th>{$lang.origination} </th>
	<th>&nbsp;&nbsp;&nbsp;</th>
	<th>{$lang.destination} </th>
</tr>
<tr class="table-row">
	<td><strong>{$lang.address}:</strong>&nbsp;</td>
	<td>{$settings.Company.company_address} </td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{$settings.General.default_address} </td>
</tr>
<tr>
	<td><strong>{$lang.city}:</strong>&nbsp;</td>
	<td>{$settings.Company.company_city}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{$settings.General.default_city} </td>
</tr>
<tr class="table-row">
	<td><strong>{$lang.country}:</strong>&nbsp;</td>
	<td>{$settings.Company.company_country}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{$settings.General.default_country} </td>
</tr>
<tr>
	<td><strong>{$lang.state}:</strong>&nbsp;</td>
	<td>{$settings.Company.company_state}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{$settings.General.default_state} </td>
</tr>
<tr class="table-row">
	<td><strong>{$lang.zip_postal_code}:</strong>&nbsp;</td>
	<td>{$settings.Company.company_zipcode}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{$settings.General.default_zipcode} </td>
</tr>
</table>

<table cellpadding="2" cellspacing="1" border="0">
<tr>
	<td><strong>{$lang.shipping_service}:</strong>&nbsp;</td>
	<td>{$service}</td>
</tr>
<tr>
	<td><strong>{$lang.weight}:</strong>&nbsp;</td>
	<td>{$weight}&nbsp;{$settings.General.weight_symbol}</td>
</tr>
{if $data.cost}
<tr>
	<td><strong>{$lang.cost}:</strong>&nbsp;</td>
	<td>{include file="common_templates/price.tpl" value=$data.cost}</td>
</tr>
{else}
<tr>
	<td><strong>{$lang.error}:</strong>&nbsp;</td>
	<td>{$data.error|default:"n/a"}</td>
</tr>
{/if}
</table>

{/if}

<p class="center"><a href="javascript: window.close();" class="underlined">{$lang.close_window}</a></p>
</body>
</html>
