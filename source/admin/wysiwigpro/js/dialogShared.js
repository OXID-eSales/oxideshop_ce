
var wp_current_obj = null
if (window.dialogArguments) {
obj = dialogArguments.wp_current_obj
wp_current_obj = obj
parentWindow = dialogArguments
} else if (parent.window.dialogArguments) {
obj = parent.window.obj
wp_current_obj = obj
parentWindow = parent.window.parentWindow
} else if (window.opener) {
obj = window.opener.wp_current_obj
wp_current_obj = obj
parentWindow = window.opener
} else if (parent.window.opener) {
obj = parent.window.obj
wp_current_obj = obj
parentWindow = parent.window.parentWindow
} else {
obj = null
wp_current_obj = null
parentWindow = null
}