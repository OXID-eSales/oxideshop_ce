<!-- page locator -->
[{if $oView->getArticleCount() }]
  [{include file="inc/list_locator.tpl" PageLoc="Top" where="Top" sLocatorCaption="INC_LIST_LOCATOR_RECOMLISTSPERPAGE"|oxmultilangassign }]
[{/if}]
[{assign var="currency" value=$oView->getActCurrency() }]
<strong id="test_recomListHeader2" class="boxhead">[{$template_title}]</strong>
<div id="test_recommlists" class="box info">
  [{assign var="searchrecomm" value=$oView->getRecommSearch() }]
  [{foreach from=$oView->getRecommLists() item=recommlist name="testRecList"}]
    [{if $blShowLine }]
      <div class="dot_sep mid"></div>
      <br>
    [{else }]
      [{assign var="blShowLine" value=true }]
    [{/if }]

    <dl class="recommlistdesc">
        <dt>
            <b><a id="test_recomListTitle_[{$smarty.foreach.testRecList.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="recommid=`$recommlist->oxrecommlists__oxid->value`&amp;searchrecomm=`$searchrecomm`" }]">[{ $recommlist->oxrecommlists__oxtitle->value }]</a>:</b> [{ oxmultilang ident="INC_RECOMM_LISTS_LISTBY" }] [{ $recommlist->oxrecommlists__oxauthor->value }]
        </dt>
        <dd>
            [{ $recommlist->oxrecommlists__oxdesc->value }] [{if $recommlist->oxrecommlists__oxdesc->value && !$blEdit}]<a id="test_recomListMore_[{$smarty.foreach.testRecList.iteration}]" href="[{ $recommlist->getLink()|oxaddparams:"searchrecomm=`$searchrecomm`"}]">[{ oxmultilang ident="INC_RECOMM_LISTS_MORE" }]</a>[{/if}]
        </dd>
    </dl>

    [{assign var="start" value=0 }]
    [{assign var="end" value=4 }]

    <div class="recommitems">
    [{foreach from=$recommlist->getArticles($start,$end) item=recommitem }]
      <div class="article">
        <div class="picture">
          <a id="test_recomListPic_[{$smarty.foreach.testRecList.iteration}]_[{$recommitem->oxarticles__oxid->value}]" href="[{ $recommlist->getLink()|oxaddparams:"searchrecomm=`$searchrecomm`"}]">
            <img src="[{$recommitem->getIconUrl()}]" alt="">
          </a>
        </div>
        <a id="test_recomListArtTitle_[{$smarty.foreach.testRecList.iteration}]_[{$recommitem->oxarticles__oxid->value}]" href="[{ $recommlist->getLink()|oxaddparams:"searchrecomm=`$searchrecomm`"}]">[{ $recommitem->oxarticles__oxtitle->value }]</a>
        [{if $recommitem->getFPrice() }]
          <br><b id="test_recomListArtPrice_[{$smarty.foreach.testRecList.iteration}]_[{$recommitem->oxarticles__oxid->value}]">[{ $recommitem->getFPrice() }] [{ $currency->sign}]<a href="#delivery_link">*</a></b>
        [{/if}]
      </div>
    [{/foreach }]
    <div class="clear_left"></div>
    <br>

    [{ if $blEdit }]
      <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div class="right">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="account_recommlist">
            <input type="hidden" name="fnc" value="editList">
            <input type="hidden" name="recommid" value="[{$recommlist->getId()}]">
            <span class="btn"><input id="test_recomListDelete_[{$smarty.foreach.testRecList.iteration}]" type="submit" name="deleteList" value="[{ oxmultilang ident="INC_RECOMM_LISTS_REMOVE" }]"></span>
            <span class="btn"><input id="test_recomListEdit_[{$smarty.foreach.testRecList.iteration}]" type="submit" name="editList" value="[{ oxmultilang ident="INC_RECOMM_LISTS_EDIT" }]"></span>
        </div>
      </form>
    [{/if}]

    </div>
    <div class="clear_right"></div>

  [{/foreach }]
  [{if !$blShowLine }][{ oxmultilang ident="INC_RECOMM_LISTS_NOLISTS" }][{/if }]
</div>

<!-- page locator -->
[{if $oView->getArticleCount() }]
  [{include file="inc/list_locator.tpl" PageLoc="Bottom" where="Bottom" sLocatorCaption="INC_LIST_LOCATOR_RECOMLISTSPERPAGE"|oxmultilangassign }]
[{/if}]