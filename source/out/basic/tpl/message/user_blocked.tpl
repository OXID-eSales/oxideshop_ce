[{assign var="template_title" value="USER_BLOCHED_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong class="boxhead">[{$template_title}]</strong>
<div class="box info">
  [{oxifcontent ident="oxblocked" object="oCont"}]
           [{ $oCont->oxcontents__oxcontent->value }]
      [{/oxifcontent}]
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]