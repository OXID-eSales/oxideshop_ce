[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign}]
<script type="text/javascript">
<!--
function LoadPayment( oObject)
{   oObject = document.getElementById("paymaster");
    if ( oObject != null && oObject.selectedIndex != -1)
    {   document.myedit2.oxpaymentid.value = oObject.item(oObject.selectedIndex).value;
        document.myedit2.fnc.value = "";
        document.myedit2.submit();
    }
}

function popU(evt,currElem)
{    popUpWin = document.getElementById("ttpop");
    oHeightObject = document.getElementById("popheight");
    title = currElem.getAttribute("caption");
    if (title.length == 0)
        return;

    document.getElementById("ttcontents").innerHTML = title;
    popUpWin = popUpWin.style;
    var y = parseInt(evt.clientY) - 20 - oHeightObject.height;
    var x = parseInt(evt.clientX) - 20;

    if(document.all){
        if ( x > document.body.clientWidth - 150 ){
            x = parseInt(document.body.clientWidth) - 100;
            y = y - 15;
        }
    }
    else{
        if ( x > self.innerWidth - 100 ){
            x = parseInt(self.innerWidth) - 100;
        }
    }
    popUpWin.top  = Math.max(2,y)-20;
    popUpWin.left = Math.max(2,x)-250-30;
    popUpWin.visibility = "visible";
    window.status = "";
}
function popD(currElem)
{
    var popUpWin = document.getElementById("ttpop");
    popUpWin = popUpWin.style;
    popUpWin.visibility = "hidden"
}



function Edit( sID, sCl)
{   var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value = sID;
    oTransfer.cl.value = sCl;
    oTransfer.target = '_parent';
    oTransfer.submit();
}

//-->
</script>
<style type="text/css">
.tableheader { border: 1px solid #404040; border-right: 0; padding:2px;empty-cells:show;}
.tablecell { border: 1px solid #c0c0c0; border-right: 0; border-top: 0; padding:2px;empty-cells:show;}
.cellcounter { text-align: center; font-weight: bold;background-color:#d4d0c8; border: 1px solid #404040; border-top: 0; border-right: 0px; padding:2px;empty-cells:show;}
.pagenavigation { background-color: transparent;}
</style>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>


<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="dyn_ipayment">
        <input type="hidden" name="fnc" value="save">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxshops__oxid]" value="[{ $oxid }]">

            <tr>
             <td valign="top" class="edittext">
                [{ oxmultilang ident="DYN_IPAYMENT_ACCOUNTNUM" }]&nbsp;&nbsp;
             </td>
             <td valign="top" class="edittext">
                <input type=text class="editinput" style="width:270" name=confstrs[iShopID_iPayment_Account] value="[{$confstrs.iShopID_iPayment_Account}]">
             </td>
            </tr>

            <tr>
             <td valign="top" class="edittext">
                [{ oxmultilang ident="DYN_IPAYMENT_USER" }]
             </td>
             <td valign="top" class="edittext">
                <input type=text class="editinput" style="width:270" name=confstrs[iShopID_iPayment_User] value="[{$confstrs.iShopID_iPayment_User}]">
             </td>
            </tr>

            <tr>
             <td valign="top" class="edittext">
                [{ oxmultilang ident="DYN_IPAYMENT_PASSWORD" }]
             </td>
             <td valign="top" class="edittext">
                <input type=text class="editinput" style="width:270" name=confstrs[iShopID_iPayment_Passwort] value="[{$confstrs.iShopID_iPayment_Passwort}]">
             </td>
            </tr>

        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
             <input type="submit" class="confinput" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]">
            </td>
        </tr>
            </form>
        </table>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">

    </td>
    </tr>
</table>


</td>
</tr>

[{include file="bottomnaviitem.tpl" }]

[{include file="bottomitem.tpl"}]
