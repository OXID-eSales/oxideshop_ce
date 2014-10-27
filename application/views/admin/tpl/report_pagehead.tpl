<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>[{ oxmultilang ident="REPORT_PAGEHEAD_TITLE" }]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]style.css">
</head>

<body leftmargin=0 topmargin=0>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td class="pageheadline1" colspan="3">

	  <table width="100%">
	  <tr>
	  <td>
	  		<a href="[{$oViewConf->getSelfLink()}]"><img src="[{$oViewConf->getImageUrl()}]/logo.jpg" alt="[{ $oxcmp_shop->oxshops__oxname->value }]" class="pageheadgraphic"></a>
	  </td>
	  <td class="pageheadlinkupperback">
	        <span class="pageheadlinkupper">[{ oxmultilang ident="REPORT_PAGEHEAD_SHOPREPORT" }]</span>
	  </td>
	  </tr>
	  </table>

  </td>
</tr>
<tr>
  <td class="pageheadline2" colspan="3">
        <span class="pageheadlinkbottom">[{ oxmultilang ident="REPORT_PAGEHEAD_REPORTSFROM" }] [{ $time_from|date_format:"%d.%m.%Y" }] [{ oxmultilang ident="REPORT_PAGEHEAD_REPORTSTILL" }] [{ $time_to|date_format:"%d.%m.%Y" }] : </span>
  </td>
</tr>
</table>

<table>
<tr>
<td class="report_edittext">
<br><br>
