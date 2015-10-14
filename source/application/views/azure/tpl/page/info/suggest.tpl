[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{$oView->getTitle()}]</h1>
    <ul>
        <li>[{oxmultilang ident="MESSAGE_ENTER_YOUR_ADDRESS_AND_MESSAGE"}]</li>
        <li>[{oxmultilang ident="MESSAGE_RECOMMEND_CLICK_ON_SEND"}]</li>
    </ul>
    [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
        <p>[{oxmultilang ident="MESSAGE_READ_DETAILS"}] <a href="[{$oCont->getLink()}]" rel="nofollow">[{$oCont->oxcontents__oxtitle->value}]</a></p>
    [{/oxifcontent}]
    [{include file="form/suggest.tpl"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]