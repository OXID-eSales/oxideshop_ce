[{*
  * @params:
  *                 sPersParamKey
  *                 sPersParamValue
*}]

[{block name="persparams_input"}]
	
	
	[{if $oView->showPersParam($sPersParamKey) }]
	
		
		[{if $tpl == "page_checkout_inc_basketcontents_editable" }]
		
			<p>
				<label class="persParamLabel">
					[{ oxmultilang ident=$sPersParamKey suffix="COLON" }]
					[{* or $oView->getPersParamText($sPersParamKey) }] ?! *}]
				</label>
				<input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sPersParamKey }]]" value="[{ $sPersParamValue }]">
			</p>
					
		[{/if}]
		
	
		[{if $tpl == "page_details_inc_productmain" }]
	
			<div class="persparamBox clear">
				<label for="persistentParam">[{ oxmultilang ident=$sPersParamKey suffix="COLON" }]</label>
				<input type="text" id="persistentParam" name="persparam[details]" value="[{ $sPersParamValue }]" size="35">
			</div>
		
		[{/if}]


	[{else}]


		[{if $tpl == "page_checkout_inc_basketcontents_editable" }]
			<input type="hidden" name="aproducts[[{ $basketindex }]][persparam][[{ $sPersParamKey }]]" value="[{ $sPersParamValue }]">
		[{/if}]
		

	[{/if}]


[{*     page_details_inc_productmain
<div class="persparamBox clear">
	<label for="persistentParam">[{ oxmultilang ident="LABEL" suffix="COLON" }]</label><input type="text" id="persistentParam" name="persparam[details]" value="[{ $oDetailsProduct->aPersistParam.text }]" size="35">
</div>
*}]

[{*     page_checkout_inc_basketcontents_editable
	<p>
		<label class="persParamLabel">
			[{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
				[{ oxmultilang ident="LABEL" suffix="COLON" }]
			[{else}]
				[{ $sVar }]:
			[{/if}]
		</label>
		<input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sVar }]]" value="[{ $aParam }]">
	</p>
*}]


[{/block}]
