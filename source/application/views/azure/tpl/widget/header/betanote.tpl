[{oxscript include="js/libs/cookie/jquery.cookie.js"}]
[{oxscript include="js/widgets/oxbetanote.js"}]
[{assign var='beta_note_link' value="<a href='"|cat:$oView->getBetaNoteLink()|cat:"' class=\"external\">FAQ</a>" }]
<div id="betaNote">
    <div class="notify">
        [{oxmultilang ident='BETA_NOTE'}]
        [{ $oxcmp_shop->oxshops__oxversion->value|regex_replace:"/[0-9]+\.[0-9]+\.[0-9]+([_a-zA-Z]+)?/":""}]
        [{oxmultilang ident='BETA_NOTE_MIDDLE'}]
        [{ $oxcmp_shop->oxshops__oxversion->value|regex_replace:"/[_a-zA-Z]+[0-9]+/":""}]
        [{oxmultilang ident='BETA_NOTE_FAQ' args=$beta_note_link}]
        <span class="dismiss"><a href="#" title="[{oxmultilang ident='CLOSE'}]">x</a></span>
    </div>
</div>
[{oxscript add="$('#betaNote').oxBetaNote();"}]
[{oxscript widget=$oView->getClassName()}]