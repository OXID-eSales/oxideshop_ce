[{assign var="template_title" value="MY_DOWNLOADS"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="MY_DOWNLOADS"|oxmultilangassign}]

[{include file="inc/account_header.tpl" active_link=10 }]<br>

<!-- page locator -->
[{include file="inc/list_locator.tpl" sLocatorCaption="PRODUCTS_PER_PAGE"|oxmultilangassign }]


<strong class="boxhead" id="test_accOrderHistoryHeader">[{ $template_title }]</strong>
<div class="box info">
[{if $oView->getOrderFilesList()|count }]
    <ul class="downloadList">
        [{foreach from=$oView->getOrderFilesList() item="oOrderArticle"}]
            <li>
                  <dl>
                    <dt>
                        <strong>[{ $oOrderArticle.oxarticletitle }] - [{ oxmultilang ident="ORDER_NUMBER" }]: [{ $oOrderArticle.oxordernr }], [{ $oOrderArticle.oxorderdate}]</strong>
                    </dt>
                    [{foreach from=$oOrderArticle.oxorderfiles item="oOrderFile"}]
                    <dd>
                           [{if $oOrderFile->isPaid() || !$oOrderFile->oxorderfiles__oxpurchasedonly->value }]
                             [{if $oOrderFile->isValid() }]
                                   <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=download" params="sorderfileid="|cat:$oOrderFile->getId() }]" rel="nofollow">[{$oOrderFile->oxorderfiles__oxfilename->value}]</a>
                                   
                                   [{include file="page/account/inc/file_attributes.tpl"}]
                                
                            [{else}]
                                [{$oOrderFile->oxorderfiles__oxfilename->value}]
                                [{oxmultilang ident="DOWNLOAD_LINK_EXPIRED_OR_MAX_COUNT_RECEIVED"}]
                            [{/if}]
                          [{else}]
                            <span>[{$oOrderFile->oxorderfiles__oxfilename->value}]</span>
                            <strong>[{ oxmultilang ident="DOWNLOADS_PAYMENT_PENDING" }]</strong>
                          [{/if}]
                    </dd>
                    [{/foreach}]
                  </dl>
            </li>
        [{/foreach}]
    </ul>
[{else}]
    <div class="box info">
        [{ oxmultilang ident="DOWNLOADS_EMPTY" }]
      </div>
[{/if}]
</div>

<!-- page locator -->
[{include file="inc/list_locator.tpl" sLocatorCaption="PRODUCTS_PER_PAGE"|oxmultilangassign }]

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_ORDER_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>

&nbsp;


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]