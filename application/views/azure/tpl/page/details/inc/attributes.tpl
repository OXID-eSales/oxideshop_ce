<table class="attributes">
    [{foreach from=$oView->getAttributes() item=oAttr name=attribute}]
    <tr>
        <th id="attrTitle_[{$smarty.foreach.attribute.iteration}]"><strong>[{$oAttr->title}]</strong></th>
        <td id="attrValue_[{$smarty.foreach.attribute.iteration}]">[{$oAttr->value}]</td>
    </tr>
    [{/foreach}]
</table>