<div class="agb">
    [{if $oView->isActive('PsLogin') || !$oView->isConfirmAGBActive()}]
        <input type="hidden" name="ord_agb" value="1">
    [{else}]
        <input type="hidden" name="ord_agb" value="0">
    [{/if}]
    <input type="hidden" name="oxdownloadableproductsagreement" value="0">
    <input type="hidden" name="oxserviceproductsagreement" value="0">

    [{if !$hideButtons}]

        [{if !$oView->isActive('PsLogin')}]
            [{if $oView->isConfirmAGBActive()}]
                [{oxifcontent ident="oxrighttocancellegend" object="oContent"}]
                    <h3 class="section">
                        <strong>[{$oContent->oxcontents__oxtitle->value}]</strong>
                    </h3>
                    <p class="agbConfirmation">
                        <input id="checkAgbTop" class="checkbox" type="checkbox" name="ord_agb" value="1">
                        <label for="checkAgbTop">[{$oContent->oxcontents__oxcontent->value}]</label>
                    </p>
                [{/oxifcontent}]
            [{else}]
                [{oxifcontent ident="oxrighttocancellegend2" object="oContent"}]
                    <h3 class="section">
                        <strong>[{$oContent->oxcontents__oxtitle->value}]</strong>
                    </h3>
                    <p class="agbConfirmation">
                        [{$oContent->oxcontents__oxcontent->value}]
                    </p>
                [{/oxifcontent}]
            [{/if}]
        [{/if}]

        [{if $oViewConf->isFunctionalityEnabled('blEnableIntangibleProdAgreement')}]
            [{assign var="oExplanationMarks" value=$oView->getBasketContentMarkGenerator()}]
            [{if $oxcmp_basket->hasArticlesWithDownloadableAgreement()}]
                [{oxifcontent ident="oxdownloadableproductsagreement" object="oContent"}]
                    <p id="noteForDownloadableArticles" class="agbConfirmation">
                        <input id="oxdownloadableproductsagreement" class="checkbox" type="checkbox" name="oxdownloadableproductsagreement" value="1">
                        <label for="oxdownloadableproductsagreement">[{$oExplanationMarks->getMark('downloadable')}] [{$oContent->oxcontents__oxcontent->value}]</label>
                    </p>
                [{/oxifcontent}]
            [{/if}]
            [{if $oxcmp_basket->hasArticlesWithIntangibleAgreement()}]
                [{oxifcontent ident="oxserviceproductsagreement" object="oContent"}]
                    <p id="noteForIntangibleArticles" class="agbConfirmation">
                        <input id="oxserviceproductsagreement" class="checkbox" type="checkbox" name="oxserviceproductsagreement" value="1">
                        <label for="oxserviceproductsagreement">[{$oExplanationMarks->getMark('intangible')}] [{$oContent->oxcontents__oxcontent->value}]</label>
                    </p>
                [{/oxifcontent}]
            [{/if}]
        [{/if}]
    [{/if}]
</div>

[{oxscript add="$('#checkAgbTop').click(function(){ $('input[name=ord_agb]').val(parseInt($('input[name=ord_agb]').val())^1);});"}]
[{oxscript add="$('#oxdownloadableproductsagreement').click(function(){ $('input[name=oxdownloadableproductsagreement]').val(parseInt($('input[name=oxdownloadableproductsagreement]').val())^1);});"}]
[{oxscript add="$('#oxserviceproductsagreement').click(function(){ $('input[name=oxserviceproductsagreement]').val(parseInt($('input[name=oxserviceproductsagreement]').val())^1);});"}]