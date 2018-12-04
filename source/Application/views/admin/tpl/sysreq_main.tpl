[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<ul class="req">
    <h3>[{oxmultilang ident="SYSREQ_DESCRIPTION_REQ"}]:</h3>
    [{foreach from=$aInfo item=aModules key=sGroupName}]
    <li class='group'>[{oxmultilang ident="SYSREQ_"|cat:$sGroupName|oxupper}]
        [{foreach from=$aModules item=iModuleState key=sModule}]
            <ul>
                [{assign var="class" value=$oView->getModuleClass($iModuleState)}]
                <li id="[{$sModule}]" class="[{$class}]"><a href=[{$oView->getReqInfoUrl($sModule)}] target="_blank">[{oxmultilang ident="SYSREQ_"|cat:$sModule|oxupper}]</a></li>
            </ul>
        [{/foreach}]
    </li>
    [{/foreach}]
    <li class="clear"></li>
</ul>

[{if $aCollations}]
    <ul class="req">
        <h3>[{oxmultilang ident="SYSREQ_DESCRIPTION_COLL"}]:</h3>
        [{foreach from=$aCollations item=aColumns key=sTable}]
        <li class="coll">[{$sTable}]
            [{foreach from=$aColumns item=sCollation key=sColumn}]
                <ul>
                    <li id="[{$sColumn}]" class="fail">[{$sColumn}] - [{$sCollation}]</li>
                </ul>
            [{/foreach}]
        </li>
        [{/foreach}]
        <li class="clear"></li>
    </ul>
[{/if}]

<ul class="req">
    <li class="pass"> - [{oxmultilang ident="SYSREQ_DESCRIPTION_PASS"}]</li>
    <li class="pmin"> - [{oxmultilang ident="SYSREQ_DESCRIPTION_PMIN"}]</li>
    <li class="fail"> - [{oxmultilang ident="SYSREQ_DESCRIPTION_FAIL"}]</li>
    <li class="null"> - [{oxmultilang ident="SYSREQ_DESCRIPTION_NULL"}]</li>
</ul>

[{assign var="_tplBlockErr" value=$oView->getMissingTemplateBlocks()}]
[{if $_tplBlockErr|count}]
    <ul class="req">
        <h3>[{oxmultilang ident="SYSREQ_MODULE_BLOCKS_REQ"}]:</h3>
        <table class="moduleBlockErrorsTable">
            <thead>
                <tr>
                    <td>[{oxmultilang ident="SYSREQ_MODULE_BLOCKS_MODNAME"}]</td>
                    <td>[{oxmultilang ident="SYSREQ_MODULE_BLOCKS_BLOCKNAME"}]</td>
                    <td>[{oxmultilang ident="SYSREQ_MODULE_BLOCKS_TPLFILE"}]</td>
                </tr>
            </thead>
            <tbody>
                [{foreach from=$_tplBlockErr item="_err"}]
                <tr>
                    <td>[{$_err.module|escape}]</td>
                    <td>[{$_err.block|escape}]</td>
                    <td>[{$_err.template|escape}]</td>
                </tr>
                [{/foreach}]
            </tbody>
        </table>
    </ul>
[{/if}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
