[{assign var="actionProducts" value=$oView->getAction()}]
[{if $actionProducts}]
[{include file="widget/product/list.tpl" type=$oView->getListType() head=$oView->getActionName() listId="articles" products=$actionProducts showMainLink=true}]
[{/if}]
