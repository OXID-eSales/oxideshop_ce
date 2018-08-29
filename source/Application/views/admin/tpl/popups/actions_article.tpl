[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    initAoc = function()
    {

        YAHOO.oxid.container1 = new YAHOO.oxid.aoc( 'container1',
                                                    [ [{foreach from=$oxajax.container1 item=aItem key=iKey}]
                                                       [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                                                       [{if !$aItem.4}],
                                                       label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                       visible: [{if $aItem.2}]true[{else}]false[{/if}]
                                                       [{/if}]}
                                                      [{/strip}]
                                                      [{assign var="sSep" value=","}]
                                                      [{/foreach}] ],
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=actions_article&synchoxid=[{$oxid}]',
                                                     { selectionMode: "single" }
                                                    );

        YAHOO.oxid.container1.modRequest = function( sRequest )
        {
            oSelect = $('artcat');
            if ( oSelect.selectedIndex ) {
                sRequest += '&oxid='+oSelect.options[oSelect.selectedIndex].value+'&synchoxid=[{$oxid}]';
            }
            return sRequest;
        }
        YAHOO.oxid.container1.filterCat = function( e, OObj )
        {
            YAHOO.oxid.container1.getPage( 0 );
        }
        YAHOO.oxid.container1.onSave = function()
        {
            var aSelRows= YAHOO.oxid.container1.getSelectedRows();
            if ( aSelRows.length ) {
                oParam = YAHOO.oxid.container1.getRecord(aSelRows[0]);
                $('actionarticle_artnum').innerHTML = oParam._oData._0;
                $('actionarticle_title').innerHTML  = oParam._oData._1;
                $('remBtn').disabled = false;
                $D.setStyle( $('_article'), 'visibility', '' );

                updateParentFrame( oParam._oData._0 + ' ' + oParam._oData._1 );
            }
        }
        YAHOO.oxid.container1.addArticle = function()
        {
            var callback = {
                success: YAHOO.oxid.container1.onSave,
                failure: YAHOO.oxid.container1.onFailure,
                scope:   YAHOO.oxid.container1
            };
            var aSelRows= YAHOO.oxid.container1.getSelectedRows();
            if ( aSelRows.length ) {
                oParam = YAHOO.oxid.container1.getRecord(aSelRows[0]);
                sRequest = '&oxarticleid=' + oParam._oData._6;
            }
            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container1&container=actions_article&fnc=setactionarticle&oxid=[{$oxid}]'+sRequest, callback );
        }
        YAHOO.oxid.container1.onRemove = function()
        {
            $('actionarticle_artnum').innerHTML = '';
            $('actionarticle_title').innerHTML  = '';
            $('remBtn').disabled = true;
            $D.setStyle( $('_article'), 'visibility', 'hidden' );

            updateParentFrame( "---" );
        }
        YAHOO.oxid.container1.onFailure = function() { /* currently does nothing */ }
        YAHOO.oxid.container1.remArticle = function()
        {
            var callback = {
                success: YAHOO.oxid.container1.onRemove,
                failure: YAHOO.oxid.container1.onFailure,
                scope:   YAHOO.oxid.container1
            };
            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container1&container=actions_article&fnc=removeactionarticle&oxid=[{$oxid}]', callback );

        }
        // subscribint event listeners on buttons
        $E.addListener( $('artcat'), "change", YAHOO.oxid.container1.filterCat, $('artcat') );
        $E.addListener( $('remBtn'), "click", YAHOO.oxid.container1.remArticle, $('remBtn') );
        $E.addListener( $('saveBtn'), "click", YAHOO.oxid.container1.addArticle, $('saveBtn') );
    }
    $E.onDOMReady( initAoc );

    // updating parent frame after assignment..
    function updateParentFrame( sArticleTitle )
    {
        try {
            if (window.opener && window.opener.document && window.opener.document.myedit) {
                window.opener.document.getElementById("assignedArticleTitle").innerHTML = sArticleTitle;
            }
        } catch ( oErr ) {}
    }

</script>

    <table width="100%">
        <tr class="edittext">
            <td >[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center"><b>[{oxmultilang ident="PROMOTIONS_ARTICLE_ALLITEMS"}]</b></td>
        </tr>
        <tr>
            <td style="padding-left:4px;padding-right:10px">
                <select name="artcat" id="artcat" style="width:100%;" class="editinput">
                [{foreach from=$artcattree->aList item=pcat}]
                <option value="[{$pcat->oxcategories__oxid->value}]">[{$pcat->oxcategories__oxtitle->value}]</option>
                [{/foreach}]
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
        </tr>
        <tr>
            <td>
                <input id="saveBtn" type="button" class="edittext oxid-aoc-button" value="[{oxmultilang ident="PROMOTIONS_ARTICLE_ASSIGNARTICLE"}]">
                <input id="remBtn" type="button" class="edittext oxid-aoc-button" value="[{oxmultilang ident="PROMOTIONS_ARTICLE_UNASSIGNARTICLE"}]" [{if !$actionarticle_artnum}] disabled [{/if}]>
            </td>
        </tr>
        <tr>
            <td valign="top" class="edittext" id="_article" [{if !$actionarticle_artnum}] style="visibility:hidden" [{/if}]>
              <b>[{oxmultilang ident="PROMOTIONS_ARTICLE_ASSIGNEDARTICLE"}]:</b>
              <b id="actionarticle_artnum">[{$actionarticle_artnum}]</b> <b id="actionarticle_title">[{$actionarticle_title}]</b>
            </td>
        </tr>
    </table>

</body>
</html>

