[{if $blSubscribeNews}]
    <li>
        [{block name="user_billing_newsletter"}]
        <label>[{ oxmultilang ident="FORM_FIELDSET_USER_SUBSCRIBENEWSLETTER" }]</label>
        <input type="hidden" name="blnewssubscribed" value="0">
        <input id="subscribeNewsletter" type="checkbox" name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed()}]checked[{/if}]>
        <br>
        <div class="note">[{ oxmultilang ident="FORM_FIELDSET_USER_SUBSCRIBENEWSLETTER_MESSAGE" }]</div>
        [{/block}]
    </li>
[{/if}]