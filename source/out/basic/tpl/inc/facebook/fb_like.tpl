        [{if $oView->isActive('FbLike') && $oViewConf->getFbAppId()}]
        <br><br>
         <fb:like href="[{$oView->getCanonicalUrl()}]" layout="standard" show_faces="false" width="270" action="like" colorscheme="light"></fb:like>
        [{/if}]