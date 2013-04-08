[{if $oView->showSorting()}]
  <!--Native Language-->
  [{assign var="columnnames_oxtitle" value="INC_SORT_GUESTBOOK_DATE"|oxmultilangassign }]
  [{assign var="columnnames_oxprice" value="INC_SORT_GUESTBOOK_AUTHOR"|oxmultilangassign }]
  [{assign_adv var="columnnames" value="array
  (
    'oxgbentries.oxcreate' => '$columnnames_oxtitle',
    'oxuser.oxusername' => '$columnnames_oxprice'
  )
  "}]

  <span class="sort_row">
      [{ oxmultilang ident="INC_SORT_GUESTBOOK_SORTBY" }]

      [{foreach from=$oView->getSortColumns() item=sortcolumn}]

        [{assign var="neworder" value="asc"}]
        [{if $oView->getGbSortBy() == $sortcolumn}]
          [{if $oView->getGbSortDir() == "asc"}]
            [{assign var="sort_order" value="asc active" }]
            [{assign var="neworder" value="desc"}]
          [{else}]
            [{assign var="sort_order" value="desc active" }]
            [{assign var="neworder" value="asc"}]
          [{/if}]
        [{else}]
            [{assign var="sort_order" value="none" }]
        [{/if}]

        &nbsp;
        <a href="[{ $oViewConf->getSelfLink() }]gborderby=[{$sortcolumn}]&amp;gborder=[{$neworder}]&amp;[{$oView->getAdditionalParams()}]" class="[{$sort_order}]" rel="nofollow">[{if $columnnames.$sortcolumn}][{$columnnames.$sortcolumn}][{else}][{$sortcolumn}][{/if}]</a>
      [{/foreach}]
  </span>

[{/if}]
