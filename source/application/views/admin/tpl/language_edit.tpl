[{block name="admin_language_edit"}]
    [{ if $oxid != "-1"}]
        <table cellspacing="2" cellpadding="2" border="0">
        <tr>
        <td align="left" class="saveinnewlangtext">
            [{ oxmultilang ident="GENERAL_LANGUAGE" }]
        </td>
        <td align="left">
            <select name="editlanguage" id="test_editlanguage" class="saveinnewlanginput" onChange="Javascript:document.myedit.submit();" [{$custreadonly}]>
            [{foreach from=$otherlang key=lang item=olang}]
            <option value="[{ $lang }]"[{ if $olang->selected}]SELECTED[{/if}]>[{ $olang->sLangDesc }]</option>
            [{/foreach}]
            </select>
        </td>
        </tr>
        [{ if $posslang }]
        <tr>
        <td align="left">
            <input type="submit" name="save" value="[{ oxmultilang ident="GENERAL_SAVEIN" }]" class="saveinnewlangtext" style="width: 100;" onClick="Javascript:document.myedit.fnc.value='saveinnlang'" [{$readonly}] [{$readonly_fields}] [{$custreadonly}]>
        </td>
        <td align="left">
            <select name="new_lang" class="saveinnewlanginput" [{$readonly}] [{$readonly_fields}]>
            [{foreach from=$posslang key=lang item=desc}]
            <option value="[{ $lang }]">[{ $desc}]</option>
            [{/foreach}]
            </select>
        </td>
        </tr>
        [{/if}]
        </table>
    [{/if}]
[{/block}]