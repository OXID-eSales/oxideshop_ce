[{if $oView->getProduct()}]
  [{assign var="product" value=$oView->getProduct()}]
  [{assign var="template_title" value=$product->oxarticles__oxtitle->value|cat:" "|cat:$product->oxarticles__oxvarselect->value}]
[{else}]
  [{assign var="template_title" value="REVIEW_YOURREVIEW"|oxmultilangassign}]
[{/if}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{if (!$oxcmp_user->oxuser__oxusername->value) && !$oView->getProduct()}]
  [{include file="inc/cmp_login.tpl" }]
[{else}]
  [{if $oView->getProduct()}]
  [{assign var="product" value=$oView->getProduct()}]
  <strong class="boxhead">[{$template_title}]</strong>
  <div class="box info">
    <table width="100%">
      <colgroup>
        <col width="20%">
        <col width="75%">
      </colgroup>
      <tr>
        <td>
          <a rel="nofollow" href="[{ $product->getLink()|oxaddparams:$oViewConf->getNavUrlParams() }]">
            <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }][{if $product->oxarticles__oxvarselect->value}] [{ $product->oxarticles__oxvarselect->value }][{/if}]">
          </a>
        </td>
        <td>
          <div>
            <a rel="nofollow" href="[{ $product->getLink()|oxaddparams:$oViewConf->getNavUrlParams() }]"><b>[{ $product->oxarticles__oxtitle->value }][{if $product->oxarticles__oxvarselect->value}] [{ $product->oxarticles__oxvarselect->value }][{/if}]</b></a>
          </div>
          <div>[{ oxmultilang ident="REVIEW_ARTNUMBER" }] [{ $product->oxarticles__oxartnum->value }]</div>
          [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
          <div>[{ $product->oxarticles__oxshortdesc->value }]</div>
          [{/oxhasrights}]
        </td>
      </tr>
    </table>
  </div>
  [{/if}]

  [{if $oView->getReviewSendStatus()}]
    <strong class="boxhead">[{ oxmultilang ident="REVIEW_REVIEW" }]</strong>
    <div class="box info">
      [{ oxmultilang ident="REVIEW_THANKYOUFORREVIEW" }]
    </div>
  [{else}]
    <strong class="boxhead">[{ oxmultilang ident="REVIEW_YOURREVIEW" }]</strong>
    <div class="box info">
      <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div>
            [{ if $oView->canRate() }]
              <table>
              [{section name=star start=5 loop=6 step=-1 max=5}]
              <tr title="[{$smarty.section.star.index}] [{if $smarty.section.star.index==1}][{ oxmultilang ident="REVIEW_STAR" }][{else}][{ oxmultilang ident="REVIEW_STARS" }][{/if}]"><td><input type="radio" name="artrating" value="[{$smarty.section.star.index}]" class="rating_review_input"></td><td class="rating s[{$smarty.section.star.index}]">&nbsp;</td></tr>
              [{/section}]
              </table>
            [{/if}]
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            [{oxid_include_dynamic file="dyn/formparams.tpl" }]
            <input type="hidden" name="fnc" value="savereview">
            <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
            [{if $product}]
            <input type="hidden" name="anid" value="[{ $product->oxarticles__oxid->value }]">
            [{/if}]
            <input type="hidden" name="reviewuserhash" value="[{$oView->getReviewUserHash()}]">
            <textarea cols="102" rows="15" name="rvw_txt" class="fullsize"></textarea><br>
            <span class="btn"><input type="submit" value="[{ oxmultilang ident="REVIEW_TOSAVEREVIEW" }]" class="btn"></span>
         </div>
      </form>
    </div>
  [{/if}]

  [{if $oView->isReviewActive() }]
    [{ if $oView->getReviews() }]
        <strong class="boxhead">[{ oxmultilang ident="REVIEW_PASTREVIEW" }]</strong>
        <div class="box info">
            [{foreach from=$oView->getReviews() item=review}]
            <dl class="review">
                <dt>
                    <span class="left"><b>[{ $review->oxuser__oxfname->value }]</b> [{ oxmultilang ident="DETAILS_WRITES" }]</span>
                    <span class="right param"><b>[{ oxmultilang ident="DETAILS_TIME" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%H:%M" }]</span>
                    <span class="right param"><b>[{ oxmultilang ident="DETAILS_DATE" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%d.%m.%Y" }]</span>
                    <span class="right param">[{if $review->oxreviews__oxrating->value }]<b>[{ oxmultilang ident="DETAILS_RATING" }]</b>&nbsp;[{ $review->oxreviews__oxrating->value }][{/if}]</span>
                </dt>
                <dd>
                    [{ $review->oxreviews__oxtext->value }]
                </dd>
            </dl>
            [{/foreach}]
        </div>
    [{/if}]
  [{/if}]

[{/if}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
