<form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
  <div class="newsletter corners">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="fnc" value="fill">
    <input type="hidden" name="cl" value="newsletter">
    [{if $oView->getProduct()}]
        [{assign var="product" value=$oView->getProduct() }]
        <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
    [{/if}]
    <label>[{ oxmultilang ident="NEWSLETTER" }]</label>
    <input class="textbox" type="text" name="editval[oxuser__oxusername]" value="">
    <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="SUBSCRIBE" }]</button>
  </div>
</form>