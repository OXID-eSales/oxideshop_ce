[{capture append="oxidBlock_pageBody"}]
    [{assign var="template_title" value=$oView->getTitle()}]
    [{if $oView->showRDFa()}]
        [{include file="rdfa/rdfa.tpl"}]
    [{/if}]
    <div id="page"[{if $sidebar}] class="sidebar-[{$sidebar|lower}]"[{/if}]>
        [{block name="layout_header"}]
            [{include file="layout/header.tpl"}]
        [{/block}]
        [{if $oView->getClassName() ne "start" && !$blHideBreadcrumb}]
            [{block name="layout_breadcrumb"}]
               [{include file="widget/breadcrumb.tpl"}]
            [{/block}]
        [{/if}]
        <section id="content">
            [{block name="content_main"}]
                [{include file="message/errors.tpl"}]
                [{foreach from=$oxidBlock_content item="_block"}]
                    [{$_block}]
                [{/foreach}]
            [{/block}]
        </section>
        [{if $sidebar}]
            <aside id="sidebar">
                [{include file="layout/sidebar.tpl"}]
            </aside>
        [{/if}]
        [{include file="layout/footer.tpl"}]
    </div>

    [{block name="layout_init_social"}]
    [{/block}]

    [{if $oView->isPriceCalculated()}]
        [{block name="layout_page_vatinclude"}]
        [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
            <aside id="incVatMessage">
                [{if $oView->isVatIncluded()}]
                    * <span class="deliveryInfo">[{oxmultilang ident="PLUS_SHIPPING"}]<a href="[{$oCont->getLink()}]" rel="nofollow">[{oxmultilang ident="PLUS_SHIPPING2"}]</a></span>
                [{else}]
                    * <span class="deliveryInfo">[{oxmultilang ident="PLUS"}]<a href="[{$oCont->getLink()}]" rel="nofollow">[{oxmultilang ident="PLUS_SHIPPING2"}]</a></span>
                [{/if}]
            </aside>
        [{/oxifcontent}]
        [{/block}]
    [{/if}]
    [{if $oView->getClassName() != "details"}]
        [{insert name="oxid_tracker" title=$template_title}]
    [{/if}]
[{/capture}]
[{include file="layout/base.tpl"}]
