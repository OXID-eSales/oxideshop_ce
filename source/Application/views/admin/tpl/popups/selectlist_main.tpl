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
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=selectlist_main&synchoxid=[{$oxid}]'
                                                    );

        [{assign var="sSep" value=""}]

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc( 'container2',
                                                    [ [{foreach from=$oxajax.container2 item=aItem key=iKey}]
                                                       [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                                                       [{if !$aItem.4}],
                                                       label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                       visible: [{if $aItem.2}]true[{else}]false[{/if}],
                                                       formatter: YAHOO.oxid.aoc.custFormatter
                                                       [{/if}]}
                                                      [{/strip}]
                                                      [{assign var="sSep" value=","}]
                                                      [{/foreach}] ],
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container2&container=selectlist_main&oxid=[{$oxid}]'
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
            return 'fnc=addarttosel';
        }
        YAHOO.oxid.container2.getDropAction = function()
        {
            return 'fnc=removeartfromsel';
        }
        $E.addListener( $('artcat'), "change", YAHOO.oxid.container1.filterCat, $('artcat') );

        YAHOO.oxid.container3 = null;
        YAHOO.oxid.container2.subscribe( "dataReturnEvent", function( oParam ) {
            if ( YAHOO.oxid.container3.oContextMenu ) {
                YAHOO.oxid.container3.oContextMenu.destroy();
            }
            YAHOO.oxid.container3 = null;
            $('container3').innerHTML = '';
            $D.setStyle( $('orderup'), 'visibility', 'hidden' );
            $D.setStyle( $('orderdown'), 'visibility', 'hidden' );
        })
        //
        YAHOO.oxid.container2.subscribe( "rowSelectEvent", function( oParam )
        {
            var sOxid = oParam.record._oData._7;
            if ( YAHOO.oxid.container3 == null) {
                YAHOO.oxid.container3 = new YAHOO.oxid.aoc( 'container3',
                                                [ [{foreach from=$oxajax.container3 item=aItem key=iKey}]
                                                   { key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                                                   [{if !$aItem.4}],
                                                   label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                   visible: [{if $aItem.2}]true[{else}]false[{/if}],
                                                   formatter: YAHOO.oxid.aoc.custFormatter,
                                                   sortable: false
                                                   [{/if}]},
                                                  [{/foreach}] ],
                                                '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=selectlist_order&oxid='+sOxid,
                                                { selectionMode:'single' }
                                                )
                //
                YAHOO.oxid.container3._lastRecord = false;
                YAHOO.oxid.container3.subscribe( "dataReturnEvent", function()
                {
                    $D.setStyle( $('orderup'), 'visibility', 'hidden' );
                    $D.setStyle( $('orderdown'), 'visibility', 'hidden' );
                })
                YAHOO.oxid.container3.subscribe( "rowClickEvent", function( oParam )
                {
                    var sVisibility = 'hidden';
                    if ( YAHOO.oxid.container3.getSelectedRows().length ) {
                        sVisibility = '';
                    }
                    $D.setStyle($('orderup'), 'visibility', sVisibility );
                    $D.setStyle($('orderdown'), 'visibility', sVisibility );
                })
                YAHOO.oxid.container3.setOrderUp = function()
                {
                    var aSelRows = YAHOO.oxid.container3.getSelectedRows();
                    if ( aSelRows.length ) {
                        sOxid = YAHOO.oxid.container3.getRecord(aSelRows[0])._oData._4;
                        YAHOO.oxid.container3.modRequest = function( sRequest ) { return sRequest+'&fnc=setSorting&sortoxid='+sOxid+'&direction=up'; }
                        YAHOO.oxid.container3.getDataSource().flushCache();
                        YAHOO.oxid.container3.getPage( 0 );
                    }
                }
                YAHOO.oxid.container3.setOrderDown = function()
                {
                    var aSelRows = YAHOO.oxid.container3.getSelectedRows();
                    if ( aSelRows.length ) {
                        sOxid = YAHOO.oxid.container3.getRecord(aSelRows[0])._oData._4;
                        YAHOO.oxid.container3.modRequest = function( sRequest ) { return sRequest+'&fnc=setSorting&sortoxid='+sOxid+'&direction=down'; }
                        YAHOO.oxid.container3.getDataSource().flushCache();
                        YAHOO.oxid.container3.getPage( 0 );
                    }
                }

                $E.addListener( $('orderup'), "click", YAHOO.oxid.container3.setOrderUp, $('orderup') );
                $E.addListener( $('orderdown'), "click", YAHOO.oxid.container3.setOrderDown, $('orderdown') );
                YAHOO.oxid.container3._lastRecord = oParam.record;
            } else if ( !YAHOO.oxid.container3._lastRecord || YAHOO.oxid.container3._lastRecord != oParam.record ) {
                YAHOO.oxid.container3._lastRecord = oParam.record;
                YAHOO.oxid.container3.modRequest = function( sRequest ) { return sRequest+'&oxid='+sOxid; }
                YAHOO.oxid.container3.getPage( 0 );
            }
        })
    }
    $E.onDOMReady( initAoc );
</script>

    <table width="100%">
        <colgroup>
            <col span="3" width="32%" />
            <col span="1" width="4%" />
        </colgroup>
        <tr class="edittext">
            <td colspan="4">[{oxmultilang ident="GENERAL_AJAX_DESCRIPTION"}]<br>[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center" valign="top"><b>[{oxmultilang ident="GENERAL_ALLITEMS"}]</b></td>
            <td align="center" valign="top">
              <b>[{oxmultilang ident="SELECTLIST_MAIN_ITEMSWITHCHOSLIST"}]</b><br />
              <b>[{oxmultilang ident="GENERAL_CLICKFORDETAILS"}]</b>
            </td>
            <td align="center" valign="top">[{oxmultilang ident="SELECTLIST_MAIN_CHOSENITEMSLIST"}]</td>
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
            <td colspan="3"></td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
            <td valign="top" id="container2"></td>
            <td valign="top" id="container3"></td>
            <td valign="middle" rowspan="2">
            <input class="edittext" type="button" id="orderup" value="/\" style="visibility:hidden"><br><br>
            <input class="edittext" type="button" id="orderdown" value="\/" style="visibility:hidden">
            </td>
        </tr>
        <tr>
            <td class="oxid-aoc-actions"><input type="button" value="[{oxmultilang ident="GENERAL_AJAX_ASSIGNALL"}]" id="container1_btn"></td>
            <td class="oxid-aoc-actions"><input type="button" value="[{oxmultilang ident="GENERAL_AJAX_UNASSIGNALL"}]" id="container2_btn"></td>
        </tr>
    </table>

</body>
</html>

