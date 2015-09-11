[{capture append="oxidBlock_content"}]
    <p>[{oxifcontent ident="oxblocked" object="oCont"}]
           [{$oCont->oxcontents__oxcontent->value}]
      [{/oxifcontent}]</p>
[{/capture}]
[{include file="layout/page.tpl"}]