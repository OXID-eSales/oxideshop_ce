  <b>[{ oxmultilang ident="REPORT_USER_PER_GROUP" }]:</b>
  <br><br>

  [{if $oView->drawReport()}]
    <img src="[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=reports/report_user_per_group&fnc=user_per_group&time_from=[{ $time_from }]&time_to=[{ $time_to }]" hspace="0" vspace="0" border="0" align="baseline" alt="">
  [{else}]
    <b>[{ oxmultilang ident="GENERAL_NODATA" }]</b>
  [{/if}]

  <br><br>
