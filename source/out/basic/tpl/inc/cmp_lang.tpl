[{ assign var="blSep" value=""}]
[{foreach from=$oxcmp_lang item=lang}]
  [{ if $blSep == "y"}]|[{/if}]
    <a id="test_Lang_[{$lang->name}]" href="[{ $lang->link|oxaddparams:$oView->getDynUrlParams() }]" class="[{if $lang->selected}]lang_active[{else}]lang[{/if}]" hreflang="[{ $lang->abbr }]" title="[{ $lang->name }]">[{ $lang->name }]</a>
  [{ assign var="blSep" value="y"}]
[{/foreach}]
