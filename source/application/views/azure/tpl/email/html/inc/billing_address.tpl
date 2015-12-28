[{$order->oxorder__oxbillcompany->value}]<br>
[{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value}] [{$order->oxorder__oxbilllname->value}]<br>
[{if $order->oxorder__oxbilladdinfo->value}][{$order->oxorder__oxbilladdinfo->value}]<br>[{/if}]
[{$order->oxorder__oxbillstreet->value}] [{$order->oxorder__oxbillstreetnr->value}]<br>
[{$order->oxorder__oxbillstateid->value}]
[{$order->oxorder__oxbillzip->value}] [{$order->oxorder__oxbillcity->value}]<br>
[{$order->oxorder__oxbillcountry->value}]<br>
[{if $order->oxorder__oxbillustid->value}][{oxmultilang ident="VAT_ID_NUMBER" suffix="COLON"}] [{$order->oxorder__oxbillustid->value}]<br>[{/if}]
[{oxmultilang ident="PHONE" suffix="COLON"}] [{$order->oxorder__oxbillfon->value}]<br><br>
