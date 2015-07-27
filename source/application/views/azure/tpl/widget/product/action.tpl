[{ assign var="oActionProducts" value=$oView->getAction() }]
[{ if $oActionProducts }]
    [{include file="widget/product/list.tpl" type=$oView->getListType() head=$oView->getActionName() listId="articles" products=$oActionProducts showMainLink=true }]
[{ /if }]