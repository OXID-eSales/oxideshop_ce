[{assign var="template_title" value="ACCOUNT_NOTICELIST_TITLE"|oxmultilangassign }]

[{include file="_header.tpl" title=$template_title location="ACCOUNT_NOTICELIST_LOCATION"|oxmultilangassign|cat:$template_title}]

[{include file="inc/account_header.tpl" active_link=5 }]

    <br>
    [{if $oView->getNoticeProductList() }]

        [{assign var="noticelist_head" value="ACCOUNT_NOTICELIST_MYWISHLIST"|oxmultilangassign}]

        [{foreach from=$oView->getNoticeProductList() name=noticelist item=product}]

          [{if $smarty.foreach.noticelist.first && !$smarty.foreach.noticelist.last }]
            [{assign var="noticelist_class" value="firstinlist"}]
          [{elseif $smarty.foreach.noticelist.last}]
            [{assign var="noticelist_class" value="lastinlist"}]
          [{else}]
            [{assign var="noticelist_class" value="inlist"}]
          [{/if}]

          [{include file="inc/product.tpl" product=$product size="thin" head=$noticelist_head class=$noticelist_class removeFunction="tonoticelist" owishid=$oxcmp_user->oxuser__oxid->value testid="NoticeList_`$smarty.foreach.noticelist.iteration`"}]

          [{assign var="noticelist_head" value=""}]
          [{if !$smarty.foreach.noticelist.last }]
            <div class="separator"></div>
          [{/if}]


        [{/foreach}]

    [{else }]
        <strong id="test_smallHeader" class="boxhead">[{ oxmultilang ident="ACCOUNT_NOTICELIST_MYWISHLIST" }]</strong>
        <div class="box info">
          [{ oxmultilang ident="ACCOUNT_NOTICELIST_EMPTYWISHLIST" }]
        </div>
    [{/if }]

    <div class="bar prevnext">
        <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              <input type="hidden" name="cl" value="start">
              <div class="right">
                  <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_NOTICELIST_BACKTOSHOP" }]">
              </div>
          </div>
        </form>
    </div>

   &nbsp;

[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
