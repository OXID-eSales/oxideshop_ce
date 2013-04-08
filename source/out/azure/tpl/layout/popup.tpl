[{capture append="oxidBlock_pageBody"}]
    <div id="content">
        [{foreach from=$oxidBlock_content item="_block"}][{$_block}][{/foreach}]
    </div>
[{/capture}]
[{include file="layout/base.tpl"}]