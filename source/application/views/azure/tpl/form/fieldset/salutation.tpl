<select name="[{$name}]" [{if $class}]class="[{$class}]"[{/if}]>
    <option value="MR"  [{if $value|lower  == "mr"  or $value2|lower == "mr" }]SELECTED[{/if}]>[{ oxmultilang ident="MR"  }]</option>
    <option value="MRS" [{if $value|lower  == "mrs" or $value2|lower == "mrs"}]SELECTED[{/if}]>[{ oxmultilang ident="MRS" }]</option>
</select>