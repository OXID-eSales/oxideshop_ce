[{oxid_include_widget cl="oxwCookieNote" _parent=$oView->getClassName() nocookie=1}]
[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{ oxmultilang ident="INVITE_YOUR_FRIENDS" }]</h1>
    [{if !$oView->getInviteSendStatus()}]
        <ul>
            <li>[{ oxmultilang ident="MESSAGE_INVITE_YOUR_FRIENDS" }]</li>
            <li>[{ oxmultilang ident="MESSAGE_INVITE_YOUR_FRIENDS_EMAIL" }]</li>
        </ul>
        [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
            <p>[{ oxmultilang ident="MESSAGE_READ_DETAILS" }] <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></p>
        [{/oxifcontent}]
        [{include file="form/privatesales/invite.tpl"}]
    [{else}]
        [{ oxmultilang ident="MESSAGE_INVITE_YOUR_FRIENDS_INVITATION_SENT" }]<br><br>
    [{/if}]
    [{ insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
