[{capture append="oxidBlock_pageBody"}]
    <div id="page">
        <section id="content">
            [{foreach from=$oxidBlock_content item="_block"}][{$_block}][{/foreach}]
        </section>
    </div>
[{/capture}]
[{include file="layout/base.tpl"}]