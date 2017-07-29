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
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=article_attribute&synchoxid=[{$oxid}]'
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
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container2&container=article_attribute&oxid=[{$oxid}]'
                                                    )
        YAHOO.oxid.container1.getDropAction = function()
        {
            return 'fnc=addattr';
        }

        YAHOO.oxid.container2.getDropAction = function()
        {
            return 'fnc=removeattr';
        }
        YAHOO.oxid.container2.subscribe( "rowClickEvent", function( oParam )
        {
            var aSelRows= YAHOO.oxid.container2.getSelectedRows();
            if ( aSelRows.length ) {
                oParam = YAHOO.oxid.container2.getRecord(aSelRows[0]);
                $('_attrname').innerHTML = oParam._oData._0;
                $('attr_value').value    = oParam._oData._2;
                $('attr_oxid').value     = oParam._oData._3;
                $D.setStyle( $('arrt_conf'), 'visibility', '' );
            } else {
                $D.setStyle( $('arrt_conf'), 'visibility', 'hidden' );
            }
        })
        YAHOO.oxid.container2.subscribe( "dataReturnEvent", function()
        {
            $D.setStyle( $('arrt_conf'), 'visibility', 'hidden' );
        })
        YAHOO.oxid.container2.onSave = function()
        {
            YAHOO.oxid.container1.getDataSource().flushCache();
            YAHOO.oxid.container1.getPage( 0 );
            YAHOO.oxid.container2.getDataSource().flushCache();
            YAHOO.oxid.container2.getPage( 0 );
        }
        YAHOO.oxid.container2.onFailure = function() { /* currently does nothing */ }
        YAHOO.oxid.container2.saveAttribute = function()
        {
            var callback = {
                success: YAHOO.oxid.container2.onSave,
                failure: YAHOO.oxid.container2.onFailure,
                scope:   YAHOO.oxid.container2
            };
            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container2&container=article_attribute&fnc=saveAttributeValue&oxid=[{$oxid}]&attr_value=' + encodeURIComponent( $('attr_value').value ) + '&attr_oxid=' + encodeURIComponent( $('attr_oxid').value ), callback );

        }
        // subscribint event listeners on buttons
        $E.addListener( $('saveBtn'), "click", YAHOO.oxid.container2.saveAttribute, $('saveBtn') );
    }
    $E.onDOMReady( initAoc );
</script>

    <table width="100%">
        <colgroup>
            <col span="2" width="40%" />
            <col width="20%" />
        </colgroup>
        <tr class="edittext">
            <td colspan="3">[{oxmultilang ident="GENERAL_AJAX_DESCRIPTION"}]<br>[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center" valign="top"><b>[{oxmultilang ident="ARTICLE_ATTRIBUTE_NOATTRIBUTE"}]</b></td>
            <td align="center" valign="top"><b>[{oxmultilang ident="ARTICLE_ATTRIBUTE_ITEMSATTRIBUTE"}]</b></td>
            <td align="center" valign="top">[{oxmultilang ident="ARTICLE_ATTRIBUTE_SELECTONEATTR"}]:</td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
            <td valign="top" id="container2"></td>
            <td valign="top" align="center" class="edittext" id="arrt_conf" style="visibility:hidden">
              <br><br>
              <b id="_attrname">[{$attr_name}]</b>:<br><br>
              <input id="attr_oxid" type="hidden">
              <input id="attr_value" class="editinput" type="text"><br><br>
              <input id="saveBtn" type="button" class="edittext" value="[{oxmultilang ident="ARTICLE_ATTRIBUTE_SAVE"}]">
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

