[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="GUESTBOOK"|oxmultilangassign}]
    <h1 class="pageHead">[{ oxmultilang ident="GUESTBOOK" }]</h1>
    <div class="listRefine clear bottomRound">
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() sort=true}]
    </div>
    <div class="reviews">
        [{include file="form/guestbook.tpl"}]
        <dl>
            [{if $oView->getEntries()}]
                [{foreach from=$oView->getEntries() item=entry}]
                    <dt class="clear item">
                        <span>[{ $entry->oxuser__oxfname->value }] [{oxmultilang ident="WRITES" suffix="COLON" }] <span>[{$entry->oxgbentries__oxcreate->value|date_format:"%d.%m.%Y"}] [{ $entry->oxgbentries__oxcreate->value|date_format:"%H:%M" }]<span></span></span>
                    </dt>
                    <dd>
                        <div class="description">[{ $entry->oxgbentries__oxcontent->value|nl2br }]</div>
                    </dd>
                [{/foreach}]
            [{else}]
                <dt>
                    [{oxmultilang ident="NO_ENTRY_AVAILABLE"}]
                </dt>
                <dd></dd>
            [{/if}]
        </dl>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
    </div>
    [{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
