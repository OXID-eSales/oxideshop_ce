[{if $blOrderRemark}]
    [{oxscript include="js/widgets/oxinnerlabel.js" priority=10 }]
    [{oxscript add="$( '#orderRemark' ).oxInnerLabel();"}]
    <li>
        <label>[{ oxmultilang ident="FORM_FIELDSET_USER_YOURMESSAGE" }]</label>
        <label for="orderRemark" class="innerLabel textArea">[{ oxmultilang ident="FORM_FIELDSET_USER_MESSAGEHERE" }]</label>
        <textarea id="orderRemark" cols="60" rows="7" name="order_remark" class="areabox" >[{$oView->getOrderRemark()}]</textarea>
    </li>
[{/if}]