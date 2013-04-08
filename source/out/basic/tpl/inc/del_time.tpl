[{if $product->oxarticles__oxmindeltime->value || $product->oxarticles__oxmaxdeltime->value }]
    <div id="test_product_deltime" class="deltime">
    <b>[{ oxmultilang ident="DETAILS_DELIVERYTIME" }]</b>
    [{if $product->oxarticles__oxmindeltime->value && $product->oxarticles__oxmindeltime->value != $product->oxarticles__oxmaxdeltime->value }]
        [{ $product->oxarticles__oxmindeltime->value }] -
    [{/if}]
    [{if $product->oxarticles__oxmaxdeltime->value  }]
        [{assign var="unit" value=$product->oxarticles__oxdeltimeunit->value}]
        [{assign var="ident" value=DETAILS_$unit }]
        [{if $product->oxarticles__oxmaxdeltime->value > 1 }]
            [{assign var="ident" value=$ident|cat:"S" }]
        [{/if}]
        [{ $product->oxarticles__oxmaxdeltime->value }] [{ oxmultilang ident=$ident }]
    [{/if}]
    </div>
[{/if}]