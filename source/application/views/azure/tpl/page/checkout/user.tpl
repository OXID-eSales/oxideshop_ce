[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value=""}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=2 }]

    [{block name="checkout_user_main"}]
        [{if !$oxcmp_user && !$oView->getLoginOption() }]
            [{include file="page/checkout/inc/options.tpl"}]
        [{/if}]

        [{block name="checkout_user_noregistration"}]
            [{if !$oxcmp_user && $oView->getLoginOption() == 1}]
                [{include file="form/user_checkout_noregistration.tpl"}]
            [{/if}]
        [{/block}]

        [{block name="checkout_user_registration"}]
            [{if !$oxcmp_user && $oView->getLoginOption() == 3}]
                [{include file="form/user_checkout_registration.tpl"}]
            [{/if}]
        [{/block}]

        [{block name="checkout_user_change"}]
            [{if $oxcmp_user}]
                [{include file="form/user_checkout_change.tpl"}]
            [{/if}]
        [{/block}]
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{include file="layout/page.tpl"}]