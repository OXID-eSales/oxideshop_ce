[{if $blSubscribeNews}]
    <li>
        [{block name="user_billing_newsletter"}]
        <label for="subscribeNewsletter">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" suffix="COLON" }]</label>
        <input type="hidden" name="blnewssubscribed" value="0">
        <input id="subscribeNewsletter" type="checkbox" name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed()}]checked[{/if}]>
        <br>
        <div class="note">[{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION" }]</div>
        [{/block}]
    </li>
[{/if}]