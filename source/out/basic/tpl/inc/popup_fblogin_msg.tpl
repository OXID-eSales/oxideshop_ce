<div id="popup" class="popup fbMsg">
    <strong>[{ oxmultilang ident="FACEBOOK_POPUP_HEADER" }]</strong>

    <div class="popupMsg">
        <br><br>
        [{ oxmultilang ident="FACEBOOK_POPUP_UPDATEDONETEXT" }]
    </div>

    <div class="popupFooter">
        <form action="" method="get">
        <div>
           <span class="btn"><input id="test_Login" type="button" class="btn" name="cancel_send" value="[{ oxmultilang ident="FACEBOOK_POPUP_CLOSEBTN" }]" onClick = "oxid.popup.hide();"></span>
        </div>
    </form>
    </div>
</div>

[{oxscript add="oxid.popup.showFbMsg();" }]

