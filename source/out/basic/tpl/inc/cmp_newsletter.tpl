<form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
  <div class="form">
      [{ $oViewConf->getHiddenSid() }]
      <input type="hidden" name="fnc" value="fill">
      <input type="hidden" name="cl" value="newsletter">
      [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
      [{/if}]

      <label for="test_RightNewsLetterUsername">[{ oxmultilang ident="INC_CMP_NEWSLETTER_EMAIL" }]</label>
      <input id="test_RightNewsLetterUsername" type="text" name="editval[oxuser__oxusername]" value="" class="txt">

      <span class="btn"><input id="test_RightNewsLetterSubmit" type="submit" name="send" value="[{ oxmultilang ident="INC_CMP_NEWSLETTER_SUBSCRIBE" }]" class="btn"></span>
   </div>
</form>
