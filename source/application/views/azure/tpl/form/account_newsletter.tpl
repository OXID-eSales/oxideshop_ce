<form action="[{ $oViewConf->getSelfActionLink() }]" name="newsletter" method="post">
    <ul class="form inlineForm clear">
        <li>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="subscribe">
            <input type="hidden" name="cl" value="account_newsletter">
            <label for="status">[{ oxmultilang ident="FORM_USER_NEWSLETTER_SUBSCRIPTION" }]</label>
            <select name="status" id="status">
            <option value="1"[{if $oView->isNewsletter() }] selected[{/if }] >[{ oxmultilang ident="FORM_USER_NEWSLETTER_YES" }]</option>
            <option value="0"[{if !$oView->isNewsletter() }] selected[{/if }] >[{ oxmultilang ident="FORM_USER_NEWSLETTER_NO" }]</option>
            </select>
            <button id="newsletterSettingsSave" type="submit" class="submitButton">[{ oxmultilang ident="FORM_USER_NEWSLETTER_SAVE" }]</button>
            <span class="notice">[{ oxmultilang ident="FORM_USER_NEWSLETTER_MESSAGE" }]</span>
        </li>
    </ul>
</form>