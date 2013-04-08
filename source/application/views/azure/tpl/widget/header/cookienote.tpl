[{if $oView->isEnabled()}]
    [{oxscript add="$.cookie('testing', 'yes'); if(!$.cookie('testing')) $('#cookieNote').hide(); else{ $('#cookieNote').show(); $.cookie('testing', null, -1);}"}]
    [{oxscript include="js/libs/cookie/jquery.cookie.js"}]
    [{oxscript include="js/widgets/oxcookienote.js"}]
    <div id="cookieNote">
        <div class="notify">
            [{oxmultilang ident='COOKIE_NOTE'}]
            <span class="cancelCookie"><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=clearcookies" }]" title="[{oxmultilang ident='COOKIE_NOTE_DISAGREE'}]">[{oxmultilang ident='COOKIE_NOTE_DISAGREE'}]</a></span>
            <span class="dismiss"><a href="#" title="[{oxmultilang ident='CLOSE'}]">x</a></span>
        </div>
    </div>
    [{oxscript add="$('#cookieNote').oxCookieNote();"}]
[{/if}]
[{oxscript widget=$oView->getClassName()}]