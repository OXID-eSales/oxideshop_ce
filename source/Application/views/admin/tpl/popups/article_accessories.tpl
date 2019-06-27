[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    initAoc = function () {

        YAHOO.oxid.container1 = new YAHOO.oxid.aoc('container1',
            [[{foreach from=$oxajax.container1 item=aItem key=iKey}]
                [{$sSep}][{strip}]{
                key: '_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                    [{if !$aItem.4}],
                label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                visible: [{if $aItem.2}]true[{else}]false[{/if}]
                    [{/if}]
            }
                [{/strip}]
                [{assign var="sSep" value=","}]
                [{/foreach}]],
            '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=article_accessories&synchoxid=[{$oxid}]'
        );

        [{assign var="sSep" value=""}]

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc('container2',
            [[{foreach from=$oxajax.container2 item=aItem key=iKey}]
                [{$sSep}][{strip}]{
                key: '_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                    [{if !$aItem.4}],sortable: false,
                label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                visible: [{if $aItem.2}]true[{else}]false[{/if}],
                formatter: YAHOO.oxid.aoc.custFormatter
                    [{/if}]
            }
                [{/strip}]
                [{assign var="sSep" value=","}]
                [{/foreach}]],
            '[{$oViewConf->getAjaxLink()}]cmpid=container2&container=article_accessories&oxid=[{$oxid}]'
        )
        YAHOO.oxid.container1.modRequest = function (request) {
            var selectedArticleCategory = $('artcat');
            if (selectedArticleCategory.selectedIndex) {
                request += '&oxid=' + selectedArticleCategory.options[selectedArticleCategory.selectedIndex].value + '&synchoxid=[{$oxid}]';
            }
            return request;
        }
        YAHOO.oxid.container1.filterCat = function () {
            YAHOO.oxid.container1.getPage(0);
        }
        YAHOO.oxid.container1.getDropAction = function () {
            return 'fnc=addarticleacc';
        }

        YAHOO.oxid.container2.getDropAction = function () {
            return 'fnc=removearticleacc';
        }

        // sort Accessories

        YAHOO.oxid.orderBtn = function (upId, downId, autoHide) {
            YAHOO.oxid.orderBtn.oUp = new YAHOO.widget.Button(upId);
            YAHOO.oxid.orderBtn.oDown = new YAHOO.widget.Button(downId);

            YAHOO.oxid.orderBtn.hide = function () {
                $D.setStyle(YAHOO.oxid.orderBtn.oUp, 'visibility', 'hidden');
                $D.setStyle(YAHOO.oxid.orderBtn.oDown, 'visibility', 'hidden');
            };

            YAHOO.oxid.orderBtn.show = function () {
                $D.setStyle(YAHOO.oxid.orderBtn.oUp, 'visibility', 'visible');
                $D.setStyle(YAHOO.oxid.orderBtn.oDown, 'visibility', 'visible');
            };

            YAHOO.oxid.orderBtn.addOn = function (onUp, onDown) {
                YAHOO.oxid.orderBtn.oUp.on("click", onUp);
                YAHOO.oxid.orderBtn.oDown.on("click", onDown);
            };

            if (autoHide) {
                YAHOO.oxid.orderBtn.hide();
            }

        };

        YAHOO.oxid.orderBtn('orderup', 'orderdown', true);

        YAHOO.oxid.container2.subscribe("rowClickEvent", function () {
            var visibility = 'hidden';
            if (YAHOO.oxid.container2.getSelectedRows().length) {
                visibility = '';
            }
            $D.setStyle($('orderup'), 'visibility', visibility);
            $D.setStyle($('orderdown'), 'visibility', visibility);
        })

        YAHOO.oxid.container2.subscribe("dataReturnEvent", function () {
            $D.setStyle($('orderup'), 'visibility', 'hidden');
            $D.setStyle($('orderdown'), 'visibility', 'hidden');
        })

        YAHOO.oxid.container2.setOrderUp = function () {

            var selectedRows = YAHOO.oxid.container2.getSelectedRows();
            var oxidId = YAHOO.oxid.container2.getRecord(selectedRows[0])._oData._7;

            if (selectedRows.length) {
                YAHOO.oxid.container2.modRequest = function (request) {
                    return request + '&fnc=sortAccessoriesList&sortoxid=' + oxidId + '&direction=up';
                }
                YAHOO.oxid.container2.getDataSource().flushCache();
                YAHOO.oxid.container2.getPage(0);
                YAHOO.oxid.container2.modRequest = function (request) {
                    return request;
                }
            }
        }
        YAHOO.oxid.container2.setOrderDown = function () {
            var selectedRows = YAHOO.oxid.container2.getSelectedRows();
            var oxidId = YAHOO.oxid.container2.getRecord(selectedRows[0])._oData._7;

            if (selectedRows.length) {
                YAHOO.oxid.container2.modRequest = function (request) {
                    return request + '&fnc=sortAccessoriesList&sortoxid=' + oxidId + '&direction=down';
                }
                YAHOO.oxid.container2.getDataSource().flushCache();
                YAHOO.oxid.container2.getPage(0);
                YAHOO.oxid.container2.modRequest = function (request) {
                    return request;
                }
            }
        }

        $E.addListener($('orderup'), "click", YAHOO.oxid.container2.setOrderUp, $('orderup'));
        $E.addListener($('orderdown'), "click", YAHOO.oxid.container2.setOrderDown, $('orderdown'));
        $E.addListener($('artcat'), "change", YAHOO.oxid.container1.filterCat, $('artcat'));
    }
    $E.onDOMReady(initAoc);


</script>

    <table width="100%">
        <colgroup>
            <col span="2" width="50%" />
        </colgroup>
        <tr class="edittext">
            <td colspan="2">[{oxmultilang ident="GENERAL_AJAX_DESCRIPTION"}]<br>[{oxmultilang ident="GENERAL_FILTERING"}]<br /><br /></td>
        </tr>
        <tr class="edittext">
            <td align="center"><b>[{oxmultilang ident="ARTICLE_CROSSSELLING_ALLITEMS"}]</b></td>
            <td align="center"><b>[{oxmultilang ident="ARTICLE_CROSSSELLING_EXTRAS"}]</b></td>
        </tr>
        <tr>
            <td class="oxid-aoc-category">
                <select name="artcat" id="artcat">
                [{foreach from=$artcattree->aList item=pcat}]
                <option value="[{$pcat->oxcategories__oxid->value}]">[{$pcat->oxcategories__oxtitle->value}]</option>
                [{/foreach}]
                </select>
            </td>
            <td></td>
        </tr>
        <tr>
            <td valign="top" id="container1"></td>
            <td valign="top" id="container2"></td>
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

