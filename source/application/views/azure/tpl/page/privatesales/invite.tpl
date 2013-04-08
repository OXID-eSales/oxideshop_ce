[{oxid_include_widget cl="oxwCookieNote" _parent=$oView->getClassName() nocookie=1}]
[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{ oxmultilang ident="PAGE_PRIVATESALES_INVITE_TITLE" }]</h1>
    [{if !$oView->getInviteSendStatus()}]
        <ul>
            <li>[{ oxmultilang ident="PAGE_PRIVATESALES_INVITE_RECOMMENDSITE" }]</li>
            <li>[{ oxmultilang ident="PAGE_PRIVATESALES_INVITE_ENTERFRIENDSEMAILS" }]</li>
        </ul>
        [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
            <p>[{ oxmultilang ident="PAGE_PRIVATESALES_INVITE_ABOUTDATAPROTECTION" }] <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></p>
        [{/oxifcontent}]
        [{include file="form/privatesales/invite.tpl"}]
    [{else}]
        [{ oxmultilang ident="PAGE_PRIVATESALES_INVITE_EMAILWASSENT" }]<br><br>
    [{/if}]
    [{ insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
