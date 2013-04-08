[{assign var="template_title" value="LINKS_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong id="test_linksHeader" class="boxhead">[{ oxmultilang ident="LINKS_LINKS" }]</strong>
<div class="box info">
  [{assign var="isFirst" value=true}]
    <dl class="links">
  [{foreach from=$oView->getLinksList() item=link name=linksList}]
        <dt>[{ $link->oxlinks__oxinsert->value|date_format:"%d.%m.%Y" }] - <a href="[{ $link->oxlinks__oxurl->value }]" class="links_link">[{ $link->oxlinks__oxurl->value }]</a></dt>
        <dd [{if $smarty.foreach.linksList.last}]class="last"[{/if}]>[{ $link->oxlinks__oxurldesc->value }]</dd>
  [{ /foreach }]
    </dl>
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
