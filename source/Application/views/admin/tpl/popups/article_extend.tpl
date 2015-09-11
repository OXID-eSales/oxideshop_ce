[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    initAoc = function()
    {

        YAHOO.oxid.container1 = new YAHOO.oxid.aoc( 'container1',
                                                    [ [{foreach from=$oxajax.container1 item=aItem key=iKey}]
                                                       [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                                                       [{if !$aItem.4}],
                                                       label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                       visible: [{if $aItem.2}]true[{else}]false[{/if}],
                                                       sortable:true
                                                       [{/if}]}
                                                      [{/strip}]
                                                      [{assign var="sSep" value=","}]
                                                      [{/foreach}] ],
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=article_extend&synchoxid=[{$oxid}]'
                                                    );

        YAHOO.oxid.aoc.custFormatter = function( elCell, oRecord, oColumn, oData )
        {
            // checking if all needed data is set
            if ( elCell && oRecord ) {
                if ( oData ) {
                    elCell.innerHTML = '<div>'+oData.toString()+'</div>';
                }
                if ( oData = oRecord.getData() ) {
                    if ( oData._4 == "0" ) {
                        $D.addClass( elCell, "oxid-aoc-primary-cat" );
                    } else {
                        $D.removeClass( elCell, "oxid-aoc-primary-cat" );
                    }
                }
            }
        };

        [{assign var="sSep" value=""}]

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc( 'container2',
                                                    [ [{foreach from=$oxajax.container2 item=aItem key=iKey}]
                                                       [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                                                       [{if !$aItem.4}],
                                                       label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                                                       visible: [{if $aItem.2}]true[{else}]false[{/if}],
                                                       sortable:true,
                                                       formatter: YAHOO.oxid.aoc.custFormatter
                                                       [{/if}]}
                                                      [{/strip}]
                                                      [{assign var="sSep" value=","}]
                                                      [{/foreach}] ],
                                                    '[{$oViewConf->getAjaxLink()}]cmpid=container2&container=article_extend&oxid=[{$oxid}]'
                                                    );

        YAHOO.oxid.container1.getDropAction = function()
        {
            return 'fnc=addcat';
        }

        YAHOO.oxid.container2.getDropAction = function()
        {
            return 'fnc=removecat';
        }

        YAHOO.oxid.container2.oActiveBtn = new YAHOO.widget.Button('makeact');

        YAHOO.oxid.container2.subscribe( "rowSelectEvent", function( oParam )
        {
            if ( oData = oParam.record.getData() ) {
                if ( oData._4 ) {
                    $('defcat').value = oData._5;
                    YAHOO.oxid.container2.oActiveBtn.set("disabled", false);
                }
            }
        });

        YAHOO.oxid.container2.onActive = function()
        {
            YAHOO.oxid.container1.getDataSource().flushCache();
            YAHOO.oxid.container1.getPage( 0 );
            YAHOO.oxid.container2.getDataSource().flushCache();
            YAHOO.oxid.container2.getPage( 0 );
        }
        YAHOO.oxid.container2.onFailure = function() { /* currently does nothing */}

        YAHOO.oxid.container2.makeActive = function()
        {
            var callback = {
                success: YAHOO.oxid.container2.onActive,
                failure: YAHOO.oxid.container2.onFailure,
                scope:   YAHOO.oxid.container2
            };

            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container2&container=article_extend&fnc=setasdefault&oxid=[{$oxid}]'+'&defcat='+$('defcat').value, callback );
            YAHOO.oxid.container2.oActiveBtn.disable();
        }


        YAHOO.oxid.container2.subscribe( "dataReturnEvent", function() { YAHOO.oxid.container2.oActiveBtn.on("click", YAHOO.oxid.container2.makeActive, this );} );
    }
    $E.onDOMReady( initAoc );
</script>

    <table width="100%">
        <colgroup>
            <col span="2" width="50%" />
        </colgroup>
        <tr class="edittext">
            <td colspan="2">[{oxmultilang ident="GENERAL_AJAX_DESCRIPTION"}]<br>[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center"><b>[{oxmultilang ident="ARTICLE_EXTEND_ALLCATS"}]</b></td>
            <td align="center"><b>[{oxmultilang ident="ARTICLE_EXTEND_ARTINCATS"}]</b></td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
            <td valign="top" id="container2"></td>
        </tr>
        <tr>
            <td class="oxid-aoc-actions"><input type="button" value="[{oxmultilang ident="GENERAL_AJAX_ASSIGNALL"}]" id="container1_btn"></td>
            <td class="oxid-aoc-actions">
              <input type="button" value="[{oxmultilang ident="GENERAL_AJAX_UNASSIGNALL"}]" id="container2_btn">
              <input type="hidden" id="defcat">
              <input type="button" id="makeact" value="[{oxmultilang ident="ARTICLE_EXTEND_DEFAULT"}]" style="width:140px;" disabled>
            </td>
        </tr>
    </table>

</body>
</html>

