[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"     value=$oEmailView->getUser() }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title>[{ oxmultilang ident="EMAIL_WISHLIST_HTML_WISHLISTBY" }] [{ $shop->oxshops__oxname->value }]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
  </head>
  <body marginwidth="0" marginheight="0">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#CECCCD">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="1" cellpadding="0">
            <tr>
              <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top : 10px; padding-bottom : 10px; padding-left : 10px;  padding-right : 10px;">
                  <tr>
                    <td bgcolor="#ffffff" align="left"><font face="Arial" size="4" color="#808080">&nbsp;&nbsp;[{ oxmultilang ident="EMAIL_WISHLIST_HTML_MYWISHLISTBY" }] </font></td>
                    <td bgcolor="#ffffff" align="right"><img src="[{$oViewConf->getImageUrl()}]logo_white.gif" border="0" hspace="0" vspace="0" alt="[{ $shop->oxshops__oxname->value }]" align="texttop"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#CECCCD">
      <tr>
        <td bgcolor="#CECCCD" width="1" rowspan="2"><img src="[{$oViewConf->getImageUrl()}]leer.gif" width="1" border="0" hspace="0" vspace="0" alt=""></td>
        <td bgcolor="#FFFFFF"><font face="Verdana,Arial" size="2">
          [{$user->send_message|oxescape}]<br><br>
          [{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE1" }] <a href="[{ $oViewConf->getBaseDir() }]index.php?cl=wishlist&wishid=[{$user->send_id}]"><font face="Verdana,Arial" size="2"><b>[{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE2" }]</b></font></a><br><br>
          [{ oxmultilang ident="EMAIL_WISHLIST_HTML_WITHLOVE" }]<br><br>
          [{$user->send_name|oxescape}]</font>
        </td>
        <td bgcolor="#CECCCD" width="1" rowspan="2"><img src="[{$oViewConf->getImageUrl()}]leer.gif" width="1" border="0" hspace="0" vspace="0" alt=""></td>
      </tr>
      <tr><td height="1"></td></tr>
    </table>
    <br><br>
    [{ oxcontent ident="oxemailfooter" }]
  </body>
</html>
