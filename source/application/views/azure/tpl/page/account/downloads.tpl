[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="MY_DOWNLOADS"|oxmultilangassign }]
    <h1 class="pageHead">[{ oxmultilang ident="MY_DOWNLOADS" }]</h1>
    [{if $oView->getOrderFilesList()|count }]
    <ul class="downloadList">
      [{foreach from=$oView->getOrderFilesList() item="oOrderArticle"}]
        <li>
          <dl>
                <dt>
                    <strong>[{ $oOrderArticle.oxarticletitle }] - [{ oxmultilang ident="ORDER_NUMBER" suffix="COLON" }] [{ $oOrderArticle.oxordernr }], [{ $oOrderArticle.oxorderdate|date_format:"%d.%m.%Y"}]</strong>
                </dt>
                [{foreach from=$oOrderArticle.oxorderfiles item="oOrderFile"}]
                <dd>
                   [{if $oOrderFile->isPaid() || !$oOrderFile->oxorderfiles__oxpurchasedonly->value  }]
                         [{if $oOrderFile->isValid() }]
                           <a class="downloadableFile" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=download" params="sorderfileid="|cat:$oOrderFile->getId() }]" rel="nofollow">[{$oOrderFile->oxorderfiles__oxfilename->value}]</a>

                            [{include file="page/account/inc/file_attributes.tpl"}]

                        [{else}]
                            [{$oOrderFile->oxorderfiles__oxfilename->value}]
                                [{oxmultilang ident="DOWNLOAD_LINK_EXPIRED_OR_MAX_COUNT_RECEIVED"}]
                        [{/if}]
                  [{else}]
                    <span class="downloadableFile pending">[{$oOrderFile->oxorderfiles__oxfilename->value}]</span>
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

    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="downloads"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]