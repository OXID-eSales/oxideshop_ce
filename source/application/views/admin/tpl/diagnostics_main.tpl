
<h1>[{oxmultilang ident='OXDIAG_HOME'}]</h1>

[{if $runAnalysis }]

<span>[{oxmultilang ident='OXDIAG_GOTO'}]:</span>
	<ul>
		<li><a href="#shopbasics">[{oxmultilang ident='OXDIAG_BASICS'}]</a></li>
		[{if $oxdiag_frm_modules }]
			<li><a href="#modules">[{oxmultilang ident='OXDIAG_MODULES'}]</a></li>
		[{/if}]
		[{if $oxdiag_frm_health }]
			<li><a href="#health">[{oxmultilang ident='OXDIAG_HEALTH'}]</a></li>
		[{/if}]
		[{if $oxdiag_frm_php }]
			<li><a href="#phpinfo">[{oxmultilang ident='OXDIAG_PHPINFO'}]</a>
			<li><a href="#phpext">[{oxmultilang ident='OXDIAG_PHPINFO_EXTENSIONS'}]</a></li>
		[{/if}]
		[{if $oxdiag_frm_server }]
			<li><a href="#serverinfo">[{oxmultilang ident='OXDIAG_SERVERINFO'}]</a></li>
		[{/if}]
		[{if $oxdiag_frm_chkvers }]
			<li><a href="#chkversion">[{oxmultilang ident='OXDIAG_CHKVERSION'}]</a></li>
		[{/if}]
	</ul>
<hr>



<h3><a id="shopbasics"></a>[{oxmultilang ident='OXDIAG_BASICS'}]</h3>
<table border="0" cellpadding="3">
	<tr class="h">
	    <th>[{oxmultilang ident='OXDIAG_SERVERINFO_COMPONENT'}]</th>
	    <th>[{oxmultilang ident='OXDIAG_PHPINFO_VALUE'}]</th>
	</tr>
	[{foreach from=$aShopDetails key=param item=value}]
		[{if $value == ''}]
            [{assign var="value" value="OXDIAG_PHPINFO_OFF"|oxmultilangassign}]
		[{/if}]
		<tr>
		<td>[{$param}]:</td><td>[{$value}]</td>
		</tr>
	[{/foreach}]
</table>



[{if $oxdiag_frm_modules }]
	<h3><a id="modules"></a>[{oxmultilang ident='OXDIAG_MODULES'}]</h3>
	<table border="0" cellpadding="3">
		<tr>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_STATE'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_NAME'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_ID'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_VERSION'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_VENDOR'}]</th>
		</tr>
		
		[{foreach from=$mylist item=listitem}]
		<tr>
			<td>[{if $listitem->isActive()}]A[{else}]x[{/if}]</td>
			<td>[{$listitem->getTitle()}]</td>
			<td>[{$listitem->getId()}]</td>
			<td>[{$listitem->getInfo('version')}]</td>
			<td>[{$listitem->getInfo('author')}]</td>
		</tr>
		[{/foreach}]
	</table>
[{/if}]



[{if $oxdiag_frm_health }]
	<h3><a id="health"></a>[{oxmultilang ident='OXDIAG_HEALTH'}]</h3>
	<table>
	    [{foreach from=$aInfo item=aModules key=sGroupName}]
	    <tr>
	    	<th colspan="2">[{ oxmultilang ident="SYSREQ_"|cat:$sGroupName|oxupper }]</th>
	    </tr>
	        [{foreach from=$aModules item=iModuleState key=sModule}]
	            <tr>
	                <td>
						[{if $iModuleState == 2 }]
                            [{oxmultilang ident='OXDIAG_HEALTH_OK'}]
						[{elseif $iModuleState == 1 }]
                            [{oxmultilang ident='OXDIAG_HEALTH_MIN'}]
						[{else}]
							[{oxmultilang ident='OXDIAG_HEALTH_FAIL'}]
						[{/if}]
					</td>
					
					<td>                
		                [{if $sModule == "memory_limit" }]
							[{ oxmultilang ident="SYSREQ_MEMORY_LIMIT" }]
		                [{else}]
							[{ oxmultilang ident="SYSREQ_"|cat:$sModule|oxupper }]
		                [{/if}]
					</td>
	            </tr>
	        [{/foreach}]
	    [{/foreach}]
	</table>
[{/if}]



[{if $oxdiag_frm_php }]
	<h3><a id="phpinfo"></a>[{oxmultilang ident='OXDIAG_PHPINFO'}]</h3>
	<table border="0" cellpadding="3">
		<tr class="h">
		    <th>[{oxmultilang ident='OXDIAG_PHPINFO_PARAM'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_PHPINFO_VALUE'}]</th>
		</tr>
		[{foreach from=$aPhpConfigparams key=param item=value}]
			[{if $value == ''}]
                [{assign var="value" value="OXDIAG_PHPINFO_OFF"|oxmultilangassign}]
			[{/if}]
			<tr>
			<td>[{$param}]:</td><td>[{$value}]</td>
			</tr>
		[{/foreach}]
	</table>
	
	<h3><a id="phpext"></a>[{oxmultilang ident='OXDIAG_PHPINFO_EXTENSIONS'}]</h3>
	[{oxmultilang ident='OXDIAG_PHPINFO_ZENDEX'}]: [{$sPhpDecoder}]
[{/if}]



[{if $oxdiag_frm_server }]
	<h3><a id="serverinfo"></a>[{oxmultilang ident='OXDIAG_SERVERINFO'}]</h3>
        <p>[{oxmultilang ident='OXDIAG_SRVINF_NOTE'}]</p>
	[{if !$isExecAllowed }]
		<p><span style="border-bottom:1px solid #f00;">[{oxmultilang ident='OXDIAG_SRVINF_NOTALL'}]</span></p>
	[{/if}]
	
	<table border="0" cellpadding="3">
		<tr class="h">
		    <th>[{oxmultilang ident='OXDIAG_SERVERINFO_COMPONENT'}]</th>
		    <th>[{oxmultilang ident='OXDIAG_MODULES_VERSION'}]</th>
		</tr>
		[{foreach from=$aServerInfo key=param item=value}]
			[{if $value == ''}]
                [{assign var="value" value="OXDIAG_SERVERINFO_NOT_DETECTED"|oxmultilangassign}]
			[{/if}]
			<tr>
			<td>[{$param}]:</td><td>[{$value}]</td>
			</tr>
		[{/foreach}]
	</table>
[{/if}]

[{/if}]



