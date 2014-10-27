// http://developer.yahoo.com/yui/articles/hosting/?button&connection&container&datatable&dragdrop&json&menu&utilities&MIN&nocombine&norollup&basepath&[{$shop-%3Ebasetpldir}]yui/build/

YAHOO.namespace( 'YAHOO.oxid' );

var $  = YAHOO.util.Dom.get,
    $D = YAHOO.util.Dom,
    $E = YAHOO.util.Event;

//--------------------------------------------------------------------------------

YAHOO.oxid.aoc = function( elContainer , aColumnDefs , sDataSource , oConfigs )
{
    YAHOO.widget.DataTable.MSG_EMPTY = "";

    /**
     * Count of items per screen
     *
     * @var int
     */
    this.viewSize = 25;

    /**
     * Count of views to cache
     *
     * @var int
     */
    this.viewCount = 100;

    /**
     * Count of DB records to fetch from DB, includes cache
     *
     * @var int
     */
    this.dataSize  = this.viewSize * this.viewCount;

    /**
     * Maximum number of cached pages
     *
     * @var int
     */
    this.maxCacheEntries = 20;

    /**
     * Response data array
     *
     * @var array
     */
    this.viewResponse = {};


    /**
     * Request array
     *
     * @var array
     */
    this.aRequests = {};


    /**
     * Actual object copy
     *
     * @var object
     */
    var me = this;

    /**
     * Internal counter
     *
     * @var int
     */
    this.evtCtr = 0;

    /**
     * Internal counter
     *
     * @var int
     */
    this.inpCtr = 0;

    /**
     *
     */
    this.focusCtr = 0;

    /**
     * Container name
     *
     * @var string
     */
    this.elContainer = elContainer;

    this._elColGroup = null;

    this._iVisibleCount = 0;
    this.aColsTohide = [];

    /**
     * Overridable method to add specific parameters to server request.
     * Useful when creating specific additional functionalities like
     * filters, category selectors or so.
     *
     * @param string sRequest initial request
     *
     * @return string
     */
    this.modRequest = function( sRequest )
    {
        return sRequest;
    };

    /**
     * Formats request URL which is executes by AJAX request. URL is
     * formatted by adding:
     *   - start index ( startIndex );
     *   - qty. of results ( results );
     *   - sorting information ( dir, sort );
     *   - filter data array ( aFilter[column] );
     *   - displayable columns info array ( aCols[] )
     *   - adding user defined params ( this.modRequest() ).
     *
     * @param int iStartIndex
     *
     * @return string
     */
    this.getRequest = function( iStartIndex )
    {
        me.iStartIndex = iStartIndex;
        var sRequest = '&startIndex=' + iStartIndex + '&results=' + this.dataSize;

        // attaching ordering
        if(me.sortCol){
            sRequest += '&dir=' + me.sortDir + '&sort=' + me.sortCol;
        }

        // attaching filter
        if ( me._aFilters ) {
            for ( var i=0; i < me._aFilters.length; i++ ) {
                if ( me._aFilters[i].name && me._aFilters[i].value ) {
                    sRequest += '&aFilter[' + me._aFilters[i].name + ']=' + encodeURIComponent( me._aFilters[i].value );
                }
            }
        }

        // only visible columns
        for ( var i=0, col; col = aColumnDefs[i]; i++ ) {
            if ( col.visible ) {
                sRequest += '&aCols[]='+ col.key;
            }
        }

        sRequest = me.modRequest( sRequest );
        return sRequest;
    };

    // if config information is not passed by user - creating empty object
    if ( !oConfigs ){
        oConfigs = {};
    }

    /**
     * Initial request URL which is executed to fetch initial data for data table
     *
     * @var string
     */
    oConfigs.initialRequest = this.getRequest( 0 ) + '&results=' + ( this.viewSize );

    /**
     * Scrollbar renderer, which is used for data paging
     *
     * @return null
     */
    this.renderScrollBar = function()
    {
        // selecting height calculation object
        if ( this._elTbody.rows.length ) {
            var oReg = this._elTbody.rows;
        } else {
            var oReg = this._elMsgTbody.rows;
        }
        var aReg = $D.getRegion( oReg[0] );
        this.rowHeight = (aReg.bottom - aReg.top - 1);

        // setting scroll div layer
        $D.setStyle( this._elScroll, 'height', ( this.rowHeight * this.viewSize )+ 'px' );
        $D.setStyle( this._elScrollView, 'height',  ( this.rowHeight * this.totalRecords ) + 'px' );

        var aTReg   = $D.getRegion( this.getTheadEl() );
        var hHeight = ( aTReg.bottom - aTReg.top );

        if ( this.elFilterHead ) {
            var aTReg   = $D.getRegion( this.elFilterHead );
            hHeight += ( aTReg.bottom - aTReg.top );
        }

        $D.setStyle( this._elScroll, 'margin-top', ( hHeight + 1 ) + 'px' );

        // subscribing event listener on scroll bar
        $E.on( this._elScroll, 'scroll', this.scrollTo, this );

        this.iScrollOffset = $D.getY( this._elScroll );
    };

    /**
     * Resets scrollbar info after data reload
     *
     * @return null
     */
    this.resetScrollBar = function()
    {
        if ( this._elTbody.rows.length ) {
            var oReg = this._elTbody.rows;
        } else {
            var oReg = this._elMsgTbody.rows;
        }

        var aReg = $D.getRegion( oReg[0] );
        this.rowHeight = ( aReg.bottom - aReg.top - 1);

        $D.setStyle( this._elScrollView, 'height',  (this.rowHeight*this.totalRecords)+ 'px' );
    };

    /**
     * Scrollbar manager
     *
     * @param object e event
     * @param object oScroll scrollbar object
     *
     * @return null
     */
    this.scrollTo = function( e, oScroll )
    {
        me.evtCtr++;
        sCall = me.evtCtr + 0;
        setTimeout( function () { me.scrollDo( sCall ); }, 100 );
    };


    /**
     * Calculates scroll direction + offset and loads new data to render
     *
     * @param int evtCtr counter to reduce server load
     *
     * @return null
     */
    this.scrollDo = function( evtCtr )
    {
        if ( me.evtCtr == evtCtr )
        {
            // initial scroll
            var iOffset = Math.round( ( $D.getY( me ._elScrollView ) - me.iScrollOffset ) / me.rowHeight );
            iOffset = Math.min( iOffset, 0 );

            var iStartRecordIndex = iOffset * - 1;
            var dataSize = me.viewSize * me.viewCount;

            var page1 = Math.floor( iStartRecordIndex / dataSize );
            var page2 = Math.floor( ( iStartRecordIndex + me.viewSize ) / dataSize );

            var sRequest = me.getRequest( page1 * dataSize );
            if ( page1 != page2 )
                var sKey = sRequest + "initDataView_ff";
            else {
                var sKey = sRequest + "initDataView_fl";
            }

            if ( sKey in me.aRequests ) {
                return;
            }

            // Do we need second page ?
            if ( page1 != page2 ) {
                sKey = me.getRequest(page2 * dataSize) + "me.initDataView_l";
            }

            if ( sKey in me.aRequests ) {
                return;
            }

            me.evtCtr = 0;
            me.getPage( iOffset * - 1 );
        }
    };

    /**
     * Calculates page position information and send request to fetch data
     * to render in data table
     *
     * @param int iStartRecordIndex
     *
     * @return null
     */
    this.getPage = function( iStartRecordIndex )
    {
        var dataSize = me.viewSize * me.viewCount;

        var page1 = Math.floor( iStartRecordIndex / dataSize );
        var page2 = Math.floor( ( iStartRecordIndex + me.viewSize ) / dataSize );

        me.viewResponse = {results:[]};

        me.iStartRecordIndex = Math.min( iStartRecordIndex, Math.max( ( me.totalRecords - me.viewSize ), 0 ) );

        var sRequest = me.getRequest( page1 * dataSize );
        if ( page1 != page2 ) {
            me.aRequests[sRequest + "initDataView_ff"] = 1;
            me.oDataSource.sendRequest( sRequest, me.initDataView_ff, me );
        } else {
            me.aRequests[sRequest + "initDataView_fl"] = 1;
            me.oDataSource.sendRequest( sRequest, me.initDataView_fl, me );
        }



        // Do we need second page ?
        if ( page1 != page2 ) {
            var sRequest = me.getRequest( page2 * dataSize );
            me.aRequests[sRequest + "initDataView_l"] = 1;
            me.oDataSource.sendRequest( sRequest, me.initDataView_l, me );
        }
    };

    this.initDataView_fl = function( sRequest, oResponse )
    {
        me.initDataView( sRequest, oResponse, 1, 1, "initDataView_fl" );
    };
    this.initDataView_ff = function( sRequest, oResponse )
    {
        me.initDataView( sRequest, oResponse, 1, 2, "initDataView_ff" );
    };
    this.initDataView_l = function( sRequest, oResponse )
    {
        me.initDataView( sRequest, oResponse, 2, 2, "initDataView_l" );
    };

    /**
     * Initiates data response data, which further is delivered and populated by
     * datatable object
     *
     * @param string sRequest action request URL
     * @param object oResponse response object
     * @param int nr response page number
     * @param int    total total number of responses
     * @param string request type
     *
     * @return null
     */
    this.initDataView = function( sRequest, oResponse, nr, total, sRequestType )
    {
        var page =  Math.floor( me.iStartRecordIndex / (me.viewSize * me.viewCount ) ) + ( nr - 1 );

        var cacheSize  = me.viewSize * me.viewCount;
        var startIndex = me.iStartRecordIndex;

        if ( ( startIndex - cacheSize ) > 0 ) {
            startIndex = startIndex - Math.floor( me.iStartRecordIndex / cacheSize ) * cacheSize;
        }

        var iCnt = 0;
        for ( var i = startIndex; i < startIndex + me.viewSize; i++ ) {
            me.viewResponse.results[iCnt] = oResponse.results[i];
            iCnt++;
        }

        if ( nr == total ) {
            me.totalRecords = oResponse.totalRecords;
            me.sortCol = oResponse.sortCol;
            me.sortDir = oResponse.sortDir;

            me.onDataReturnInitializeTable(sRequest, me.viewResponse);
        }

        var sKey = sRequest + sRequestType;
        if ( sKey in me.aRequests ) {
            delete me.aRequests[sKey];
        }

    };

    /**
     * Executes sorting call
     *
     * @param object oColumn sortable column
     *
     * @return null
     */
    this.sortColumn = function( oColumn )
    {

        // Which direction
        var sDir = 'asc';

        // Already sorted?
        if ( oColumn.key === me.sortCol ) {
            sDir = ( me.sortDir === 'asc' ) ? 'desc' : 'asc';
        }
        me.sortCol = oColumn.key;
        me.sortDir = sDir;
        me.set( 'sortedBy', { key:oColumn.key, dir:(sDir === 'asc') ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC, column:oColumn } );

        me.getPage( 0 );
    };

    /**
     * Forms HTML container to store data table and scroll components
     *
     * @return null
     */
    this.addScrollBar = function()
    {
        $(elContainer).innerHTML = "<table class=\"oxid-aoc\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td  id=\""+elContainer+"_bg\" class=\"oxid-aoc-table\" valign=\"top\"><div id=\""+elContainer+"_c\"><\/div><\/td><td valign=\"top\" height=\"100%\"><div dir=RTL class=\"oxid-aoc-scrollbar\" id=\""+elContainer+"_s\"><div id=\""+elContainer+"_v\"><\/div><\/div><\/td><\/tr><\/table>";

        this._elScroll     = $(elContainer + '_s' );
        this._elScrollView = $(elContainer + '_v' );
    };

    /**
     * Adds filters into data table component
     *
     * @return null
     */
    this.addFilters = function()
    {

        this._aFilters = [];
        var elTr = document.createElement( 'tr' );

        // just adding class the same as headers
        $D.addClass( elTr, 'yui-dt-first yui-dt-last' );
        elTr.id = "yui-dt" + this._nIndex + "-hdrow1";
        for ( var i=0,col; col=aViewCols[i]; i++ ) {

            var elInput = document.createElement( 'input' );
            elInput.setAttribute( "name", aViewCols[i].key );
            elInput.setAttribute( "value", "" );
            elInput.style.width = '95%';

            this._aFilters[i] = elInput;
            $E.on( elInput, 'keyup', this.waitForfilter, this );
            $E.on( elInput, 'click', elInput.focus );

            elInput.focused = false;

            var elTd = document.createElement( 'th' );
            elTd.id = "yui-dt" + this._nIndex + "-filter_" + i;

            $D.addClass( elTd,  'yui-dt-resizeable yui-dt-sortable' );

            var elDiv = document.createElement( 'div' );

            elDiv.style.padding = '2px';
            elDiv.style.overflow = 'hidden';

            elDiv.appendChild( elInput );

            elTd.appendChild( elDiv );
            elTr.appendChild( elTd );
        }

        this.elFilterHead = document.createElement( 'thead' );
        this.elFilterHead.appendChild( elTr );

        // adding filters
        var elThead = this.getTheadEl();
        elThead.parentNode.insertBefore( this.elFilterHead, elThead.parentNode.firstChild );
    };

    /**
     * Checks if user input is allowed. Returns false if not
     *
     * @param int nKeyCode key code
     *
     * @return bool
     */
    this.isIgnoreKey = function( nKeyCode )
    {
        if ( ( nKeyCode == 9 ) || ( nKeyCode == 13 )  || // tab, enter
                (nKeyCode == 16) || (nKeyCode == 17) ||  // shift, ctl
                (nKeyCode >= 18 && nKeyCode <= 20) ||    // alt,pause/break,caps lock
                (nKeyCode == 27) || // esc
                (nKeyCode >= 33 && nKeyCode <= 35) ||    // page up,page down,end
                (nKeyCode >= 36 && nKeyCode <= 40) ||    // home,left,up, right, down
                (nKeyCode >= 44 && nKeyCode <= 45)) {    // print screen,insert
            return true;
        }
        return false;
    };

    /**
     * Filter manager
     *
     * @param object e event
     *
     * @return null
     */
    this.waitForfilter = function( e )
    {
        if ( me.isIgnoreKey( e.keyCode ) )
            return false;

        me.inpCtr++;
        sCall = me.inpCtr + 0;
        var sSecondParam = $E.getTarget( e, false );
        setTimeout( function( ) { me.filterBy( sCall, sSecondParam ); }, 100 );
    };

    /**
     * Performs filter call and loads new data to render
     *
     * @param int inpCtr counter to reduce server load
     *
     * @return null
     */
    this.filterBy = function( inpCtr, oTarget )
    {
        if ( me.inpCtr == inpCtr ) {
            me.inpCtr = 0;
            me.getPage( 0 );

            if ( me._aFilters && oTarget ) {
                for ( var i=0; i < me._aFilters.length; i++ ) {
                    if ( me._aFilters[i] == oTarget ) {
                        me._aFilters[i].focused = true;
                    } else {
                        me._aFilters[i].focused = false;
                    }
                }
            }

        }
    };

    this.onMouseDown = function( e )
    {
        if ( !(e.shiftKey || e.event.ctrlKey || ((navigator.userAgent.toLowerCase().indexOf("mac") != -1) && e.event.metaKey)) && !this.isSelected(e.target) ) {
            this.onEventSelectRow(e);
        }
    };

    /**
     * Initializes and mounts drag&drop on dataTable component
     *
     * @return null
     */
    this.addDD = function()
    {
        this.subscribe( 'rowClickEvent', this.onEventSelectRow );
        this.subscribe( 'rowMousedownEvent', this.onMouseDown );

        YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;
        me.dd = new YAHOO.util.DDProxy( this.elContainer+'_bg', 'aoc', { resizeFrame: false , centerFrame  :true} );

        me.dd.endDrag      = this.endDrag;
        me.dd.onDragEnter  = this.onDragEnter;
        me.dd.onDragOut    = this.onDragOut;
        me.dd.onDragDrop   = this.onDragDrop;

        me.dd.me = me;
        me.dd.b4StartDrag = this.b4StartDrag;
    };

    /**
     *
     */
    this.onDragDrop = function( e, DDArray )
    {
        me.afterDrop( me, DDArray[0].me );
    };

    /**
     * Overridable method to add specific parameters to server request.
     * Useful when creating specific additional functionalities like
     * filters, category selectors or so.
     *
     * @return null
     */
    this.getDropAction = function()
    {
    };

    /**
     * Overridable callback function for user defined functionality after
     * failure/success d&d action
     *
     * @param object oResponse response object
     *
     * @return null
     */
    this.onFailureCalback = function( oResponse )
    {
    };

    /**
     * Overridable callback function for user defined functionality after
     * failure/success d&d action
     *
     * @param object oResponse response object
     *
     * @return null
     */
    this.onSuccessCalback = function( oResponse )
    {
    };

    /**
     * On success drop action refreshes data tables
     *
     * @param object oResponse response object
     *
     * @return null
     */
    this.onSuccess = function( oResponse )
    {
        me.onSuccessCalback( oResponse );
        var oSrc = oResponse.argument[0];
            oSrc.getDataSource().flushCache();
            oSrc.getPage( 0 );

        var oTrg = oResponse.argument[1];
            oTrg.getDataSource().flushCache();
            oTrg.getPage( 0 );
    };

    /**
     * On failure executes onFailureCalback function
     *
     * @param object oResponse response object
     *
     * @return null
     */
    this.onFailure = function( oResponse )
    {
        me.onFailureCalback( oResponse );
    };

    /**
     * Returns drop action URL. Usefull when there is a need to pass different DD action
     *
     * @return string
     */
    this.getDropUrl = function()
    {
        return me.sDataSource;
    };

    /**
     * Adds and returns query parameters for drop action URL
     *
     * @return string
     */
    this.getDropParams = function() {
        var sNextAction = '';

        var oBtn = $( me.elContainer + '_btn' );
        var blChecked = $D.hasClass( oBtn, 'oxid-aoc-button-checked' );

        if ( blChecked ) {
            sNextAction += '&all=1';
        } else {

            var aSelRows = me.getSelectedRows();
            for ( var i=0, aRow; aRow = aSelRows[i]; i++ ) {
                var oRecord = me.getRecord( aRow );
                if ( oRecord ) {
                    var oData = oRecord.getData();

                    // building action url
                    if ( me.aIdentFields ) { //
                        for ( var c=0, sIdent; sIdent = me.aIdentFields[c]; c++ ) {
                            if ( oData[sIdent] ) {
                                var dataString = oData[sIdent];
                                //T2010-01-06
                                //fixing #1580
                                dataString = escape(dataString);
                                sNextAction += '&'+sIdent + '[]=' + dataString;
                            }
                        }
                    }
                }
            }

        }

        return sNextAction;
    };

    /**
     * Executes "after drop" action
     *
     * @param object oSource drop source
     * @param object oTarget drop target
     *
     * @return null
     */
    this.afterDrop = function( oSource, oTarget )
    {
        var callback = { success:  me.onSuccess,
                         failure:  me.onFailure,
                         argument: [ oSource, oTarget ]
                       };

        YAHOO.util.Connect.asyncRequest( 'GET', me.getDropUrl() + '&' + me.getDropParams() + '&' + me.getDropAction(), callback, null );
    };

    /**
     * Clears CSS classes from D&D after dropping DD proxy
     *
     * @return null
     */
    this.endDrag = function()
    {
        if ( me.ddtarget ) {
            $D.removeClass( me.ddtarget.me._elTbody, 'ddtarget' );
            $D.removeClass( me.ddtarget.elContainer, 'ddtarget' );
            $D.removeClass( $( me.ddtarget.me.elContainer + '_bg' ), 'ddtarget' );
        }
    };

    /**
     * Adds CSS class on drag component
     *
     * @param object e mouse event
     * @param array DDArray array of DD objects
     *
     * @return null
     */
    this.onDragEnter = function( e, DDArray )
    {
        var tar = YAHOO.util.DragDropMgr.getBestMatch( DDArray );
        if ( me.ddtarget ) {
            $D.removeClass( me.ddtarget.me._elTbody, 'ddtarget' );

            $D.removeClass( $( elContainer ), 'ddtarget' );
        }

        $D.addClass( tar.me._elTbody, 'ddtarget' );
        $D.addClass( $( tar.me.elContainer + '_bg' ), 'ddtarget' );
        me.ddtarget = tar;
    };

    /**
     * Removes CSS class from drag component
     *
     * @param object e mouse event
     * @param array DDArray array of DD objects
     *
     * @return null
     */
    this.onDragOut = function( e, DDArray )
    {
        var tar = YAHOO.util.DragDropMgr.getBestMatch( DDArray );
        $D.removeClass( tar.me._elTbody, 'ddtarget' );
        $D.removeClass( $(tar.me.elContainer + '_bg' ), 'ddtarget' );

        me.ddtarget = null;
    };

    /**
     * D&D: cancels drag process if no cells are selected. Otherwise shows frame
     *
     * @param int x position by x axis
     * @param int y position by y axis
     *
     * @return bool
     */
    this.b4StartDrag = function( x, y )
    {
        if ( !me.getSelectedRows().length ) {
            me.dd.endDrag();
            return false;
        }

        // show the drag frame
        this.showFrame(x, y);
    };

    /**
     * Initializes and adds menu object on column headers
     *
     * @return null
     */
    this.addColumnSelector = function()
    {
        me.oContextMenu = new YAHOO.widget.ContextMenu( elContainer + '_m', { zindex: 1000, trigger: this._elThead.childNodes } );
        me.oContextMenu.clearContent();

        var aItems = [];
        for ( var i=1,col; col = aColumnDefs[i]; i++ ) {
            if ( col.label ) {
                aItems[i] = { target:i, text:col.label, checked: col.visible  };
            }
        }

        me.oContextMenu.addItems( aItems );
        me.oContextMenu.render( document.body );
        me.oContextMenu.clickEvent.subscribe( me.onFieldMenuClick );
    };


    /**
     * Iterates through
     */
    this.hideNumberedColumn = function( nr, blShow, startIdx )
    {
        var aCols = [ "yui-dt" + this._nIndex + "-th-_" + nr , "yui-dt" + this._nIndex + "-filter_" + nr ];

        if ( blShow ) {
            this._iVisibleCount++;
            this.showColumn( this.getColumn( nr ) );
            $D.removeClass( aCols, 'yui-dt-hidden' );

            // somehow some elements keeps its state
            $D.setStyle( $D.getElementsByClassName( 'yui-dt-sortable'), 'display', '');
            $D.setStyle( $D.getElementsByClassName( 'yui-dt-col-'+ nr ), 'width', 'auto' );

        } else {
            this._iVisibleCount--;
            this.hideColumn( this.getColumn( nr ) );
            $D.addClass( aCols, 'yui-dt-hidden' );
        }
    };

    /**
     * Hides/displays data table column after uses choose one from menu
     *
     * @param object e click event
     * @param array args array with arguments
     *
     * @return bool
     */
    this.onFieldMenuClick = function( e, args ) {

        var item    = args[1],
            checked = !$D.hasClass(item._oAnchor,'yuimenuitemlabel-checked'),
            nr      = item.cfg.getProperty('target');

        item.cfg.setProperty( 'checked', checked );

        aColumnDefs[nr].visible = checked;

        //Set class for headers / cells
        var oColumn = me._oColumnSet.getColumn( aColumnDefs[nr].key );
        oColumn.hidden = !checked;

        me.hideNumberedColumn( nr, checked, me.getColumn(0)._sId );

        me.getPage( 0 );
        me.set( 'sortedBy', {} );


        return false;
    };

    /**
     * Processing response data to load information:
     *   - total records found;
     *   - sorting column;
     *   - sorting direction.
     * Returns parsed response object (which is used by data source class further).
     *
     * @param object oRequest request object
     * @param object oRawResponse response object
     * @param object oParsedResponse parsed response object
     *
     * @return object
     */
    this.doBeforeCallback = function( oRequest, oRawResponse, oParsedResponse )
    {
        oParsedResponse.totalRecords = me.totalRecords = oRawResponse.totalRecords;
        oParsedResponse.sortCol      = me.sortCol = oRawResponse.sort; // Which column is sorted
        oParsedResponse.sortDir      = me.sortDir = oRawResponse.dir;  // Which sort direction

        return oParsedResponse;
    };

    /**
     * Default cell formatter. Surrounds cell HTML with div's to make view nicer (adds
     * hidden overflow property).
     *
     * @param object elCell  table cell element
     * @param object oRecord data record object
     * @param object oColumn table column object
     * @param object oData   responce data object
     *
     * @return null
     */
    this.defaultFormatter = function( elCell, oRecord, oColumn, oData )
    {
        if ( oData ) {
            elCell.innerHTML = '<div>'+oData.toString()+'</div>';
        } else { // avoiding empty broken cells
            elCell.innerHTML = '&nbsp;';
        }
    };

    /**
     * Binds event listener on assign all button
     */
    this.initAssignBtn = function()
    {
        // only if this action is allowed
        var oBtn,sBtn = $( this.elContainer + '_btn' );
        if ( sBtn != null ) {
            oBtn = new YAHOO.widget.Button(sBtn);
            oBtn.on("click", this.assignAll, this );
        }
    };

    /**
     * Returns URL which are executen on assignment action
     * Usefult when you need to pass different action URL than default
     *
     * @return string
     */
    this.getAssignUrl = function()
    {
        return me.sDataSource;;
    };

    /**
     * Overridable method to add user defined assign parameters
     *
     * @return string
     */
    this.getAssignParams = function()
    {
        return '';
    };

    /**
     * Returns assign action parameters which are included in action URL
     *
     * @return string
     */
    this.getAssignAction = function()
    {
        sRequest = me.getDropAction();
        // attaching filter
        if ( me._aFilters ) {
            for ( var i=0; i < me._aFilters.length; i++ ) {
                if ( me._aFilters[i].name && me._aFilters[i].value ) {
                    sRequest += '&aFilter['+me._aFilters[i].name+']='+ encodeURIComponent( me._aFilters[i].value );
                }
            }
        }

        sRequest = me.modRequest( sRequest );
        return sRequest+'&all=1';
    };

    /**
     * Refreshing data tables after assign succeded
     *
     * @param object oResponse response object
     *
     * @return null
     */
    this.onAssignSuccess = function( oResponse )
    {
        me.onSuccessCalback( oResponse );
        var oSrc = oResponse.argument[0];
            oSrc.getDataSource().flushCache();
            oSrc.getPage( 0 );

        var oTrg = oResponse.argument[1];
            oTrg.getDataSource().flushCache();
            oTrg.getPage( 0 );
    };

    /**
     * Overridable method which is called when some failure raises while
     * assigning object
     *
     * @return null
     */
    this.onAssignFailure = function()
    {
    };

    /**
     * Returns assignment target.
     * NOTICE: override this on special cases
     *
     * @return null
     */
    this.getAssignTarget = function()
    {
        // not nice hack ...
        if ( me != YAHOO.oxid.container1 )
            return YAHOO.oxid.container1;
        else
            return YAHOO.oxid.container2;
    };

    /**
     * Returns object which was the source for assignment
     *
     * @return object
     */
    this.getAssignSource = function()
    {
        return me;
    };

    /**
     * Executes "assign all" request
     *
     * @return null
     */
    this.assignAll = function()
    {
        var callback = { success:  me.onAssignSuccess,
                         failure:  me.onAssignFailure,
                         argument: [ me.getAssignSource(), me.getAssignTarget() ]
                       };
        YAHOO.util.Connect.asyncRequest( 'GET', me.getAssignUrl() + '&' + me.getAssignParams() + '&' + me.getAssignAction(), callback, null );
    };


    this.setFocusOnFilter = function()
    {
        // saving information about field which has focus
        if ( this._aFilters ) {
            for ( var i=0; i < this._aFilters.length; i++ ) {
                if ( this._aFilters[i].focused ) {
                    this._aFilters[i].focus();
                }
            }
        }
    };

    this.waitForFocus = function( focusCtr )
    {
        var blContinue = false;
        // saving information about field which has focus
        if ( me._aFilters ) {
            for ( var i=0; i < me._aFilters.length; i++ ) {
                if ( me._aFilters[i].focused ) {
                    blContinue = true;
                }
            }
        }

        if ( !blContinue ) {
            return;
        }

        if ( me.focusCtr == focusCtr ) {
            me.setFocusOnFilter();
        } else {
            me.focusCtr++;
            sCall = me.focustCtr + 0;
            setTimeout( function( ) { me.waitForFocus( sCall ); }, 100 );
        }
    };

    /**
     * All data columns which somes from server serialized in JSON style
     *
     * @return array
     */
    var aDataCols = [];

    /**
     * Columns which are displayed
     *
     * @return array
     */
    var aViewCols = [];

    /**
     * Name of fields which are used as identifiers. They are not usefull for
     * displaying, but is handy while performing soecial actions
     *
     * @var array
     */
    this.aIdentFields = [];

    /**
     * Data source URL
     */
    this.sDataSource = sDataSource;

    // just counting
    var iCtr = 0;
    var iIdentCtr = 0;
    var iHiddenColCtr = 0;

    // collecting data/view/ident fields information arrays
    for ( var i=0,col; col = aColumnDefs[i]; i++ ) {
        if ( !col.ident ) {

            if ( col.formatter ) {
                sFormatters = col.formatter;
            } else {
                sFormatters = me.defaultFormatter;
            }

            var blSortable  = true;
            if ( col.sortable != null )
                blSortable = col.sortable;

            col.sortable   = blSortable;
            col.formatter  = sFormatters;
            col.resizeable = true;

            aViewCols[iCtr] = col;

            if (!col.visible) {
                this.aColsTohide[iHiddenColCtr] = i;
                iHiddenColCtr++;
            }
        } else {
            this.aIdentFields[iIdentCtr] = col.key;
            iIdentCtr++;
        }

        aDataCols[iCtr] = col.key;
        iCtr++;
    }

    this._iVisibleCount = aViewCols.length;

    // initiating data source
    this.oDataSource = new YAHOO.util.DataSource( sDataSource, { maxCacheEntries: this.maxCacheEntries } );
    this.oDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    this.oDataSource.doBeforeCallback = this.doBeforeCallback;
    this.oDataSource.responseSchema = {
                                       resultsList: 'records',
                                       fields: aDataCols
                                      };



    // adding scrollbar divs
    this.addScrollBar( elContainer );

    // constructing datatable component
    YAHOO.oxid.aoc.superclass.constructor.call( this, elContainer + '_c', aViewCols, this.oDataSource, oConfigs );

    // adding data filters
    this.addFilters();

    // initializing menu - column selector
    this.addColumnSelector();

    // initializind drag&drop
    this.addDD();

    // adding listener on "assign all" button
    this.initAssignBtn();

    for ( var i=0; i < this.aColsTohide.length; i++ ) {
        this.hideNumberedColumn( this.aColsTohide[i], false, i );
    }

    // table must be 100% width ..
    $D.addClass( this.getTableEl(), 'yui-dt-table' );
 };

YAHOO.lang.extend( YAHOO.oxid.aoc, YAHOO.widget.DataTable );

/**
 * Overriding default data set call to add additional functionality
 * which renders scrollbar after data is fetched from server
 *
 * @param string sRequest request URL
 * @param object oResponse responce object
 *
 * @return null
 */
YAHOO.oxid.aoc.prototype.onDataReturnSetRows = function( sRequest, oResponse, oPayload )
{
     YAHOO.oxid.aoc.superclass.onDataReturnSetRows.call( this, sRequest, oResponse, oPayload );
     if ( this.blInitScrollBar == null ) {
         this.renderScrollBar();
         this.blInitScrollBar = true;
     } else {
         this.resetScrollBar();
     }

     var oColumn = this.getColumn( this.sortCol );
     if ( this.sortDir && this.sortCol && oColumn ) {
         this.set( 'sortedBy', { key: this.sortCol, dir:(this.sortDir === 'asc') ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC, column:oColumn } );
     }

     me = this;
     setTimeout( function () { me.waitForFocus( 0 ); }, 100 );

     this.hideTableMessage();
};