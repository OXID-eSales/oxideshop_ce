[{*
  * @params:
  *                 sPersParamKey
  *                 sPersParamValue
*}]

[{block name="admin_persparams_output"}]


	[{ oxmultilang ident=$sPersParamKey suffix="COLON" }]
	[{ $sPersParamValue }]




	[{*     admin__order_article
		[{if !$smarty.foreach.persparams.first}]&nbsp;&nbsp;,&nbsp;[{/if}]
		<em>
			[{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
				[{ oxmultilang ident="GENERAL_LABEL" }]
			[{else}]
				[{$sVar}] :
			[{/if}]
			[{$aParam}]
		</em>
	*}]

[{*     admin__order_article
    &nbsp;&nbsp;,&nbsp;<em>
        [{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
            [{ oxmultilang ident="GENERAL_LABEL" }]
        [{else}]
            [{$sVar}] :
        [{/if}]
        [{$aParam}]
    </em>
*}]

[{*     admin__order_package
    [{if $aParam }]
        <br />
        [{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
            [{ oxmultilang ident="ORDER_PACKAGE_DETAILS" }] 
        [{else}]
            [{$sVar}] : 
        [{/if}]
        [{$aParam}]
    [{/if }]
*}]


[{/block}]
