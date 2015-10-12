[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{$oView->getTitle()}]</h1>
    [{foreach from=$oView->getLinksList() item=link name=linksList}]
        <h3>
            <span>[{$link->oxlinks__oxinsert->value|date_format:"%d.%m.%Y"}] - </span> <a href="[{$link->oxlinks__oxurl->value}]" >[{$link->oxlinks__oxurl->value}]</a>
        </h3>
        <div>[{$link->oxlinks__oxurldesc->value}]</div>
    [{/foreach}]
[{/capture}]

[{include file="layout/page.tpl" sidebar="Left"}]