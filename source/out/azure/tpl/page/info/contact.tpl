[{capture append="oxidBlock_content"}]
    [{if $oView->getContactSendStatus() }]
        [{assign var="_statusMessage" value="PAGE_INFO_CONTACT_THANKYOU1"|oxmultilangassign|cat:" "|cat:$oxcmp_shop->oxshops__oxname->value}]
        [{assign var="_statusMessageSuffix" value="PAGE_INFO_CONTACT_THANKYOU2"|oxmultilangassign}]
        [{include file="message/notice.tpl" statusMessage=$_statusMessage|cat:$_statusMessageSuffix}]
    [{/if }]
    <h1 class="pageHead">[{ $oxcmp_shop->oxshops__oxcompany->value }]</h1>
    <ul>
        <li>[{ $oxcmp_shop->oxshops__oxstreet->value }]</li>
        <li>[{ $oxcmp_shop->oxshops__oxzip->value }]&nbsp;[{ $oxcmp_shop->oxshops__oxcity->value }]</li>
        <li>[{ $oxcmp_shop->oxshops__oxcountry->value }]</li>
        [{ if $oxcmp_shop->oxshops__oxtelefon->value}]
            <li>[{ oxmultilang ident="PAGE_INFO_CONTACT_PHONE" }] [{ $oxcmp_shop->oxshops__oxtelefon->value }]</li>
        [{/if}]
        [{ if $oxcmp_shop->oxshops__oxtelefax->value}]
            <li>[{ oxmultilang ident="PAGE_INFO_CONTACT_FAX" }] [{ $oxcmp_shop->oxshops__oxtelefax->value }]</li>
        [{/if}]
        [{ if $oxcmp_shop->oxshops__oxinfoemail->value}]
            <li>[{ oxmultilang ident="PAGE_INFO_CONTACT_EMAIL" }] [{oxmailto address=$oxcmp_shop->oxshops__oxinfoemail->value encode="javascript"}]</li>
        [{/if}]
    </ul>
    [{include file="form/contact.tpl"}]
    [{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{include file="layout/page.tpl" sidebar="Left"}]
