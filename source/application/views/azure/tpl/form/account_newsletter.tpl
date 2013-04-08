<form action="[{ $oViewConf->getSelfActionLink() }]" name="newsletter" method="post">
    <ul class="form inlineForm clear">
        <li>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="subscribe">
            <input type="hidden" name="cl" value="account_newsletter">
            <label for="status">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" suffix="COLON" }]</label>
            <select name="status" id="status">
            <option value="1"[{if $oView->isNewsletter() }] selected[{/if }] >[{ oxmultilang ident="YES" }]</option>
            <option value="0"[{if !$oView->isNewsletter() }] selected[{/if }] >[{ oxmultilang ident="NO" }]</option>
            </select>
            <button id="newsletterSettingsSave" type="submit" class="submitButton">[{ oxmultilang ident="SAVE" }]</button>
            [{if $oView->isNewsletter() == 2}]
            <div class="info">
                [{ oxmultilang ident="MESSAGE_SENT_CONFIRMATION_EMAIL" }]
            </div>
            [{/if}]
            <span class="notice">[{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION" }]</span>
        </li>
    </ul>
</form>