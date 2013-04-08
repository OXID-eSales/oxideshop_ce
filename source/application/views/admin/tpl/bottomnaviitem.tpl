</div>

<div class="actions">
[{strip}]

<ul>
[{block name="admin_bottomnaviitem"}]
        [{ assign var="allowSharedEdit" value=true}]

    [{if !$disablenew}]

    [{* user *}]
    [{if $bottom_buttons->user_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWUSER" }]</a> |</li>
    [{/if}]
    [{if $bottom_buttons->user_newremark && $oxid != "-1" }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.newremark" href="#" onClick="Javascript:top.oxid.admin.changeEditBar('user_remark', 3); return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWREMARK" }]</a> |</li>
    [{/if}]
    [{if $bottom_buttons->user_newaddress && $oxid != "-1" }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.newaddress" href="#" onClick="Javascript:top.oxid.admin.changeEditBar('user_address', 4); return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWADDRESS" }]</a> |</li>
    [{ /if }]
    [{* payment *}]
      [{if $bottom_buttons->payment_new }]
      <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWPAYMENT" }]</a> |</li>
      [{/if}]
    [{* newsletter *}]
    [{if $bottom_buttons->newsletter_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWNEWSLETTER" }]</a> |</li>
    [{/if}]
    [{* shop *}]
    [{if $bottom_buttons->shop_new && $oView->isMall() && $malladmin == 1 }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWSHOP" }]</a> |</li>
    [{/if}]
    [{* usergroups *}]
    [{if $bottom_buttons->usergroup_new && $allowSharedEdit }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWUSERGROUP" }]</a> |</li>
    [{/if}]
    [{* category *}]
      [{if $bottom_buttons->category_new }]
      <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWCATEGORY" }]</a> |</li>
      [{/if}]
    [{if $bottom_buttons->category_refresh }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.refresh" href="#" onClick="Javascript:var agree=confirm('[{ oxmultilang ident="BOTTOMNAVIITEM_ATTENTION" }]');if (agree){top.oxid.admin.editThis( -1 );popupWin=window.open('[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=category_update', 'remote', 'scrollbars=yes,width=500,height=400')}" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWCATTREE" }]</a> |</li>
    [{/if}]
    [{if $bottom_buttons->category_resetnrofarticles }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.resetnrofarticles" href="#" onClick="Javascript:document.myedit.fnc.value='resetNrOfCatArticles';document.myedit.submit();" target="edit">[{ oxmultilang ident="TOOLTIPS_RESETNROFARTICLESINCAT" }]</a> |</li>
    [{/if}]
    [{* article *}]
      [{if $bottom_buttons->article_new }]
      <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWARTICLE" }]</a> |</li>
      [{/if}]
    [{if $bottom_buttons->article_preview && $oxid != -1 }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.preview" href="[{if $edit}][{$edit->getStdLink()}][{else}][{$oViewConf->getBaseDir()}]?cl=details&anid=[{$oxid}][{/if}]&amp;preview=[{$oView->getPreviewId()}]" target="new">[{ oxmultilang ident="TOOLTIPS_ARTICLEREVIEW" }]</a> |</li>
    [{/if}]
    [{* attribute *}]
    [{if $bottom_buttons->attribute_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWITEMS" }]</a> |</li>
    [{/if}]
    [{* statistic *}]
    [{if $bottom_buttons->statistic_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWSTATISTIC" }]</a> |</li>
    [{/if}]
    [{* selectlist *}]
    [{if $bottom_buttons->selectlist_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWSELECTLIST" }]</a> |</li>
    [{/if}]
    [{* discount *}]
    [{if $bottom_buttons->discount_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWDISCOUNT" }]</a> |</li>
    [{/if}]
    [{* delivery *}]
    [{if $bottom_buttons->delivery_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWDELIVERY" }]</a> |</li>
    [{/if}]
    [{* deliveryset *}]
    [{if $bottom_buttons->deliveryset_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWDELIVERYSET" }]</a> |</li>
    [{/if}]
    [{* vat *}]
    [{if $bottom_buttons->vat_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWMWST" }]</a> |</li>
    [{/if}]
    [{* news *}]
    [{if $bottom_buttons->news_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWNEWS" }]</a> |</li>
    [{/if}]
    [{* links *}]
    [{if $bottom_buttons->links_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWLINK" }]</a> |</li>
    [{/if}]
    [{* voucher *}]
    [{if $bottom_buttons->voucher_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWVOUCHER" }]</a> |</li>
    [{/if}]
    [{* order *}]
    [{if $bottom_buttons->order_newremark && $oxid!=-1 }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.newremark" href="#" onClick="Javascript:top.oxid.admin.changeEditBar('order_remark', 4);return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWREMARK" }]</a> |</li>
    [{/if}]
    [{* imex *}]
    [{* country *}]
    [{if $bottom_buttons->country_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWCOUNTRY" }]</a> |</li>
    [{/if}]
    [{* language *}]
    [{if $bottom_buttons->language_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWLANGUAGE" }]</a> |</li>
    [{/if}]
    [{* vendor *}]
    [{if $bottom_buttons->vendor_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWVENDOR" }]</a> |</li>
    [{/if}]
    [{if $bottom_buttons->vendor_resetnrofarticles }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.resetnrofarticles" href="#" onClick="Javascript:document.myedit.fnc.value='resetNrOfVendorArticles';document.myedit.submit();" target="edit">[{ oxmultilang ident="TOOLTIPS_RESETNROFARTICLESINVND" }]</a> |</li>
    [{/if}]
    [{* manufacturer *}]
    [{if $bottom_buttons->manufacturer_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWMANUFACTURER" }]</a> |</li>
    [{/if}]
    [{if $bottom_buttons->manufacturer_resetnrofarticles }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.resetnrofarticles" href="#" onClick="Javascript:document.myedit.fnc.value='resetNrOfManufacturerArticles';document.myedit.submit();" target="edit">[{ oxmultilang ident="TOOLTIPS_RESETNROFARTICLESINMAN" }]</a> |</li>
    [{/if}]
    [{* wrapping *}]
    [{if $bottom_buttons->wrapping_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWWRAPPING" }]</a> |</li>
    [{/if}]
    [{* actions *}]
    [{*
    <a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWACTIONS" }]</a> |</li>
    *}]
    [{* content *}]
    [{if $bottom_buttons->content_new }]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEWCONTENT" }]</a> |</li>
    [{/if}]


    [{/if}]

    [{block name="admin_bottomnavicustom"}]
       [{include file="bottomnavicustom.tpl"}]
    [{/block}]

    [{ if $sHelpURL }]
    [{* HELP *}]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.help" href="[{ $sHelpURL }]/[{ $oViewConf->getActiveClassName()|oxlower }].html" OnClick="window.open('[{ $sHelpURL }]/[{ $oViewConf->getActiveClassName()|lower }].html','OXID_Help','width=800,height=600,resizable=no,scrollbars=yes');return false;">[{ oxmultilang ident="TOOLTIPS_OPENHELP" }]</a></li>
    [{/if}]
[{/block}]
</ul>
[{/strip}]
</div>