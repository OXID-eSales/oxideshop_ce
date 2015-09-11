[{include file="widget/minibasket/minibasket.tpl" _prefix="modal"}]
[{oxscript add="$('#modalbasketFlyout').oxModalPopup({ target: '#modalbasketFlyout', openDialog: true, width: 'auto'});"}]
    [{oxscript add="if ($('.scrollable .scrollbarBox').length > 0) { $('.scrollable .scrollbarBox').jScrollPane({showArrows: true, verticalArrowPositions: 'split' });}"}]
    [{oxscript add="$('#modalbasketFlyout').css('z-index','inherit');"}]