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
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=actions_main&synchoxid=[{$oxid}]'
                                                    );

        [{assign var="sSep" value=""}]

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc( 'container2',
                                                    [ [{foreach from=$oxajax.container2 item=aItem key=iKey}]
                                                       [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}], sortable: false
                                                       [{if !$aItem.4}],
                                                       label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                       visible: [{if $aItem.2}]true[{else}]false[{/if}],
                                                       formatter: YAHOO.oxid.aoc.custFormatter
                                                       [{/if}]}
                                                      [{/strip}]
                                                      [{assign var="sSep" value=","}]
                                                      [{/foreach}] ],
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container2&container=actions_main&oxid=[{$oxid}]'
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
        YAHOO.oxid.container1.getDropAction = function()
        {
            return 'fnc=addarttoact';
        }

        YAHOO.oxid.container2.getDropAction = function()
        {
            return 'fnc=removeartfromact';
        }
        YAHOO.oxid.container2.subscribe( "dataReturnEvent", function()
        {
            $D.setStyle( $('orderup'), 'visibility', 'hidden' );
            $D.setStyle( $('orderdown'), 'visibility', 'hidden' );
        })
        YAHOO.oxid.container2.subscribe( "rowClickEvent", function( oParam )
        {
            var sVisibility = 'hidden';
            if ( YAHOO.oxid.container2.getSelectedRows().length ) {
                sVisibility = '';
            }
            $D.setStyle($('orderup'), 'visibility', sVisibility );
            $D.setStyle($('orderdown'), 'visibility', sVisibility );
        })
        YAHOO.oxid.container2.setOrderUp = function()
        {
            var aSelRows = YAHOO.oxid.container2.getSelectedRows();

            if ( aSelRows.length ) {
                sOxid = YAHOO.oxid.container2.getRecord(aSelRows[0])._oData._7;
                YAHOO.oxid.container2.modRequest = function( sRequest ) { return sRequest+'&fnc=setSorting&sortoxid='+sOxid+'&direction=up'; }
                YAHOO.oxid.container2.getDataSource().flushCache();
                YAHOO.oxid.container2.getPage( 0 );
                YAHOO.oxid.container2.modRequest = function( sRequest ) { return sRequest; }
            }
        }
        YAHOO.oxid.container2.setOrderDown = function()
        {
            var aSelRows = YAHOO.oxid.container2.getSelectedRows();
            if ( aSelRows.length ) {
                sOxid = YAHOO.oxid.container2.getRecord(aSelRows[0])._oData._7;
                YAHOO.oxid.container2.modRequest = function( sRequest ) { return sRequest+'&fnc=setSorting&sortoxid='+sOxid+'&direction=down'; }
                YAHOO.oxid.container2.getDataSource().flushCache();
                YAHOO.oxid.container2.getPage( 0 );
                YAHOO.oxid.container2.modRequest = function( sRequest ) { return sRequest; }
            }
        }
        $E.addListener( $('orderup'), "click", YAHOO.oxid.container2.setOrderUp, $('orderup') );
        $E.addListener( $('orderdown'), "click", YAHOO.oxid.container2.setOrderDown, $('orderdown') );
        $E.addListener( $('artcat'), "change", YAHOO.oxid.container1.filterCat, $('artcat') );
    }
    $E.onDOMReady( initAoc );
</script>

    <table width="100%">
        <colgroup>
            <col span="2" width="45%" />
            <col width="1%" />
        </colgroup>
        <tr class="edittext">
            <td colspan="3">[{oxmultilang ident="GENERAL_AJAX_DESCRIPTION"}]<br>[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center"><b>[{oxmultilang ident="ACTIONS_MAIN_ALLARTICLES"}]</b></td>
            <td align="center"><b>[{oxmultilang ident="ACTIONS_MAIN_ALLARTICLESWITHATTR"}]</b></td>
            <td></td>
        </tr>
        <tr>
            <td class="oxid-aoc-category">
                <select name="artcat" id="artcat">
                [{foreach from=$artcattree->aList item=pcat}]
                <option value="[{$pcat->oxcategories__oxid->value}]">[{$pcat->oxcategories__oxtitle->value}]</option>
                [{/foreach}]
                </select>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
            <td valign="top" id="container2"></td>
            <td valign="middle">
                <input class="edittext" type="button" id="orderup" value="/\" style="visibility:hidden"><br><br>
                <input class="edittext" type="button" id="orderdown" value="\/" style="visibility:hidden">
            </td>
        </tr>
        <tr>
            <td class="oxid-aoc-actions"><input type="button" value="[{oxmultilang ident="GENERAL_AJAX_ASSIGNALL"}]" id="container1_btn"></td>
            <td class="oxid-aoc-actions"><input type="button" value="[{oxmultilang ident="GENERAL_AJAX_UNASSIGNALL"}]" id="container2_btn"></td>
            <td></td>
        </tr>
    </table>

</body>
</html>

