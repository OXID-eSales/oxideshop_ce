[{if $oView->getShowPromotionList()}]
    <div class="promotionsRow">
      [{foreach from=$oView->getPromoFinishedList() item=promo}]
        <div class="promotion promotionFinished" id="promo[{$promo->getId()}]">
            <div class="finishedText"><img alt="[{oxmultilang ident="PROMO_SOLDOUT"}]" src="[{$oViewConf->getImageUrl()}]promo_soldout_[{ $oView->getActiveLangAbbr() }].png" /></div>
            [{oxeval var=$promo->oxactions__oxlongdesc}]
        </div>
      [{/foreach}]
      [{foreach from=$oView->getPromoCurrentList() item=promo}]
        <div class="promotion promotionCurrent" id="promo[{$promo->getId()}]">
            <div class="finishedText"><img alt="[{oxmultilang ident="PROMO_SOLDOUT"}]" src="[{$oViewConf->getImageUrl()}]promo_soldout_[{ $oView->getActiveLangAbbr() }].png" /></div>
            [{oxeval var=$promo->oxactions__oxlongdesc}]
            [{if $promo->oxactions__oxactiveto->value && $promo->oxactions__oxactiveto->value != "0000-00-00 00:00:00"}]
                <div class="timeouttext">
                  [{oxmultilang ident="PROMO_WILLENDIN_PREFIX"}]
                  [{if 86400 > $promo->getTimeLeft()}]
                    <span class="promoTimeout">[{$promo->getTimeLeft()|oxformattime}]</span>[{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                  [{elseif 172800 > $promo->getTimeLeft()}]
                    [{oxmultilang ident="PROMO_ONEDAY"}][{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                  [{else}]
                    [{math equation="x1/x2" x1=$promo->getTimeLeft() x2=86400 assign="_days"}]
                    [{$_days|floor}] [{oxmultilang ident="PROMO_DAYS"}][{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                  [{/if}]
                </div>
            [{/if}]
        </div>
      [{/foreach}]
      [{foreach from=$oView->getPromoFutureList() item=promo}]
        <div class="promotion promotionFuture" id="promo[{$promo->getId()}]">
            <div class="finishedText"><img alt="[{oxmultilang ident="PROMO_SOLDOUT"}]" src="[{$oViewConf->getImageUrl()}]promo_soldout_[{ $oView->getActiveLangAbbr() }].png" /></div>
            <div class="upcomingText"><img alt="[{oxmultilang ident="PROMO_UPCOMING"}]" src="[{$oViewConf->getImageUrl()}]promo_upcoming_[{ $oView->getActiveLangAbbr() }].png" /></div>
            [{oxeval var=$promo->oxactions__oxlongdesc}]
            [{if $promo->oxactions__oxactiveto->value && $promo->oxactions__oxactiveto->value != "0000-00-00 00:00:00"}]
              <div class="timeouttext">[{oxmultilang ident="PROMO_WILLENDIN_PREFIX"}]
                [{if 86400 > $promo->getTimeLeft()}]
                  <span class="promoTimeout">[{$promo->getTimeLeft()|oxformattime}]</span>[{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                [{elseif 172800 > $promo->getTimeLeft()}]
                  [{oxmultilang ident="PROMO_ONEDAY"}][{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                [{else}]
                    [{math equation="x1/x2" x1=$promo->getTimeLeft() x2=86400 assign="_days"}]
                    [{$_days|floor}] [{oxmultilang ident="PROMO_DAYS"}][{oxmultilang ident="PROMO_WILLENDIN_SUFFIX"}]
                [{/if}]
              </div>
            [{/if}]
            <div class="activationtext">[{oxmultilang ident="PROMO_WILLSTARTIN_PREFIX"}]
              [{if 86400 > $promo->getTimeUntilStart()}]
                <span class="promoTimeout">[{$promo->getTimeUntilStart()|oxformattime}]</span>
              [{elseif 172800 > $promo->getTimeUntilStart()}]
                [{oxmultilang ident="PROMO_ONEDAY"}]
              [{else}]
                [{math equation="x1/x2" x1=$promo->getTimeUntilStart() x2=86400 assign="_days"}]
                [{$_days|floor}] [{oxmultilang ident="PROMO_DAYS"}]
              [{/if}]
            </div>
        </div>
      [{/foreach}]
    </div>
    [{oxscript include="jquery.min.js"}]
    [{oxscript include="countdown.jquery.js"}]
    [{oxscript include="promotions.js"}]
[{/if}]
