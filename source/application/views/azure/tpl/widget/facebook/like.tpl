[{if $oView->isActive('FbLike') && $oViewConf->getFbAppId()}]
    <fb:like href="[{if $parent != 'footer'}][{$oView->getCanonicalUrl()}][{else}][{$oViewConf->getCurrentHomeDir()}][{/if}]" layout="button_count" action="like" colorscheme="light"></fb:like>
[{/if}]