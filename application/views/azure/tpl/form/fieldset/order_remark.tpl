[{if $blOrderRemark}]
    [{oxscript include="js/widgets/oxinnerlabel.js" priority=10 }]
    [{oxscript add="$( '#orderRemark' ).oxInnerLabel();"}]
    <li>
        <label>[{ oxmultilang ident="WHAT_I_WANTED_TO_SAY" }]</label>
        <label for="orderRemark" class="innerLabel textArea">[{ oxmultilang ident="HERE_YOU_CAN_ENETER_MESSAGE" }]</label>
        <textarea id="orderRemark" cols="60" rows="7" name="order_remark" class="areabox" >[{$oView->getOrderRemark()}]</textarea>
    </li>
[{/if}]