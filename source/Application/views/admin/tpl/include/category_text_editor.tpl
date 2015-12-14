[{include file="include/editor.tpl"}]

<table>
    <tr>
        <td valign="top" class="edittext">
            [{if $languages}]<b>[{oxmultilang ident="GENERAL_LANGUAGE"}]</b>
                <select name="catlang" class="editinput" onchange="Javascript:loadLang(this)" [{$readonly}]>
                    [{foreach key=key item=item from=$languages}]
                        <option value="[{$key}]"[{if $catlang == $key}] SELECTED[{/if}]>[{$item->name}]</option>
                    [{/foreach}]
                </select>
            [{/if}]
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="CATEGORY_TEXT_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'">
        </td>
    </tr>
</table>