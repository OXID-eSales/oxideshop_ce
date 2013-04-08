<script type="text/fbml">
    <fb:fbml>
        <fb:request-form action="[{$oView->getCanonicalUrl()}]" method="GET" invite="true" type="Facebook" content="[{oxmultilang ident="FACEBOOK_INVITETEXT"}]<fb:req-choice url='[{$oDetailsProduct->getLink()}]' label='[{oxmultilang ident="FACEBOOK_OPEN_WEBSITE"}]'></fb:req-choice>">
            <fb:multi-friend-selector showborder="false" rows="3" cols="3" max="20" width="560" actiontext="[{oxmultilang ident="INVITE_YOUR_FRIENDS"}]">
            </fb:multi-friend-selector>
        </fb:request-form>
    </fb:fbml>
</script>