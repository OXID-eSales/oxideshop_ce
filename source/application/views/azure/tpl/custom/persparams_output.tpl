[{*
  * @params:
  *                 sPersParamKey
  *                 sPersParamValue
*}]

[{block name="persparams_output"}]

	
	[{if $oView->showPersParam($sPersParamKey) }]

			
		[{if $tpl == "page_checkout_inc_basketcontents_noteditable" }]
			
			<br />
			<strong>
				[{ oxmultilang ident=$sPersParamKey suffix="COLON" }]
				[{* or $oView->getPersParamText($sPersParamKey) }] ?! *}]
			</strong>
			[{ $sPersParamValue }]
			[{* or $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }] ?! *}]
		[{/if}]
	
	
		[{if $tpl == "page_checkout_inc_basketcontents_editable_empty" }]
			<p>[{ oxmultilang ident=$sPersParamKey suffix="COLON" }] <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][details]" value=""></p>
		[{/if}]
	
	
		[{if $tpl == "email_html_order_cust" }]
			<li style="padding: 3px;">[{ $sPersParamKey }] : [{ $sPersParamValue }]</li>
		[{/if}]
	
		[{if $tpl == "email_html_order_owner" }]
			,&nbsp;<em>[{ $sPersParamKey }] : [{ $sPersParamValue }]</em>
		[{/if}]
	
		[{if $tpl == "email_plain_order_cust" || $tpl == "email_plain_order_owner" }]
			[{ $sPersParamKey }] : [{ $sPersParamValue }]
		[{/if}]


	[{/if}]


[{*     email_html_order_cust
	<li style="padding: 3px;">[{$sVar}] : [{$aParam}]</li>
*}]

[{*     email_html_order_owner
	,&nbsp;<em>[{$sVar}] : [{$aParam}]</em>
*}]
	
[{*     email_plain_order_cust
	[{ $sVar }] : [{ $aParam }]
*}]

[{*     email_plain_order_owner
	[{$sVar}] : [{$aParam}]
*}]

[{*     page_checkout_inc_basketcontents_noteditable
	[{if !$smarty.foreach.persparams.first}]<br />[{/if}]
	<strong>
		[{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
			[{ oxmultilang ident="LABEL" suffix="COLON" }]
		[{else}]
			[{ $sVar }] :
		[{/if}]
	</strong> [{ $aParam }]
*}]

[{*     page_checkout_inc_basketcontents_editable_empty
	 <p>[{ oxmultilang ident="LABEL" suffix="COLON" }] <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][details]" value=""></p>
*}]


[{/block}]
