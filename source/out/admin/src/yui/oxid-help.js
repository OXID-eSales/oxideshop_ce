// http://developer.yahoo.com/yui/articles/hosting/?container&dragdrop&utilities&MIN&nocombine&norollup&basepath&[{$shop->basetpldir}]yui/build/

YAHOO.namespace('YAHOO.oxid');

var $ = YAHOO.util.Dom.get;

// --------------------------------------------------------------------------------

YAHOO.oxid.help = new function () {
    var helpBtnIdPrefix   = "helpBtn_";
    var helpTexttIdPrefix = "helpText_";

    this.helpBtntId    = '';
    this.helpTextBody  = '';
    this.helpTextPanel = null;

    /*
     * Show help panel
     */
    this.showPanel = function (helpId) {

        this.helpBtntId   = helpBtnIdPrefix + helpId;
        this.helpTextBody = $(helpTexttIdPrefix + helpId).innerHTML;

        if ( !this.helpTextPanel ) {
            this.helpTextPanel = new YAHOO.widget.Panel("helpPanel");

            //setting general panel properties
            this.setTextPanelProperties();
        }

        // setting panel position next to help button
        this.helpTextPanel.cfg.setProperty("context", [this.helpBtntId, "tl", "tr"]);
        this.helpTextPanel.cfg.setProperty("constraintoviewport", true);

        this.helpTextPanel.setBody(this.helpTextBody);
        this.helpTextPanel.render("helpTextContainer");
        this.helpTextPanel.show();
    }

    /*
     * Set general panel properties
     */
    this.setTextPanelProperties = function () {

        this.helpTextPanel.cfg.setProperty("width", "370px");
        this.helpTextPanel.cfg.setProperty("visible", false);
        this.helpTextPanel.cfg.setProperty("draggable", true);
    }
}
