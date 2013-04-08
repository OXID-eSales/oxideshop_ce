[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="userinfo" value=$oEmailView->getUser() }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title>[{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP" }] [{$userinfo->send_name|oxescape}], [{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP2" }] [{ $shop->oxshops__oxname->value }] [{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP3" }]</title>
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
                    <td bgcolor="#ffffff" align="left"><font face="Arial" size="4" color="#808080">&nbsp;&nbsp;[{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP" }] [{$userinfo->send_name|oxescape}], [{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP2" }] [{ $shop->oxshops__oxname->value }] [{ oxmultilang ident="EMAIL_INVITE_HTML_INVITETOSHOP3" }]</font></td>
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
        <td>
          <table border="0" width="100%" cellspacing="10" cellpadding="0" bgcolor="#FFFFFF">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                  <tr>
                    <td valign="top">
                      <table border="0" width="100%"cellspacing="10" cellpadding="0" bgcolor="#FFFFFF">
                        <tr>
                          <td bgcolor="#FFFFFF"></td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" width="10%"><font face="Verdana,Arial" size="1"><b>[{ oxmultilang ident="EMAIL_INVITE_HTML_FROM" }]</b></font></td>
                          <td bgcolor="#FFFFFF" align="left"><font face="Verdana,Arial" size="1">[{$userinfo->send_name|oxescape}]<br></font></td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" width="10%"><font face="Verdana,Arial" size="1"><b>[{ oxmultilang ident="EMAIL_INVITE_HTML_EMAIL" }]</b></font></td>
                          <td bgcolor="#FFFFFF" align="left"><font face="Verdana,Arial" size="1">[{$userinfo->send_email|oxescape}]</font></td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1">[{$userinfo->send_message|oxescape|nl2br}]</font></td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1"><a href="[{ $sHomeUrl }]">[{ $shop->oxshops__oxname->value }]</a></td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#FFFFFF" colspan="2"><font face="Verdana,Arial" size="1">[{ oxmultilang ident="EMAIL_INVITE_HTML_MENYGREETINGS" }] [{$userinfo->send_name|oxescape}]</font></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        <td bgcolor="#CECCCD" width="1" rowspan="2"><img src="[{$oViewConf->getImageUrl()}]leer.gif" width="1" border="0" hspace="0" vspace="0" alt=""></td>
      </tr>
      <tr><td height="1"></td></tr>
    </table>
    <br><br>
    [{ oxcontent ident="oxemailfooter" }]
  </body>
</html>
