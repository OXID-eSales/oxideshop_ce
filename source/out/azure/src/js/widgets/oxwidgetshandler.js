var WidgetsHandler = (function() {
    var oRegister = { files: [], functions: [] }, oWidgetRegister = {}, blLoaded = false;

    var obj = {
        /**
         * Registers function if it was not already registered
         *
         * @param sFunction function text
         * @param sWidget widget name
         */
        registerFunction: function( sFunction, sWidget ) {
            _register( oRegister, sFunction, 'functions' );
            _initWidget( sWidget );
            _register( oWidgetRegister[ sWidget ], sFunction, 'functions' );
        },

        /**
         * Registers files if it was not already registered
         *
         * @param sFile file name
         * @param sWidget widget name
         */
        registerFile: function( sFile, sWidget ) {
            _register( oRegister, sFile, 'files' );
            _initWidget( sWidget );
            _register( oWidgetRegister[ sWidget ], sFile, 'files' );
        },

        /**
         * Loads all registered functions
         */
        load: function() {
            _loadAll();
        },

        /**
         * Loads all registered functions
         *
         * @param sWidget widget name
         */
        reloadWidget: function( sWidget ) {
            _loadWidget( sWidget );
        }
    }

    /**
     * Initiates widget
     * @param sWidget
     * @private
     */
    function _initWidget( sWidget )
    {
        if ( !( sWidget in oWidgetRegister ) ) {
            oWidgetRegister[ sWidget ] = { files: [], functions: [] };
        }
    }

    /**
     * Registers given value to given register
     *
     * @param oRegister
     * @param sValue
     * @param sGroup
     */
    function _register( oRegister, sValue, sGroup ) {
        if ( !_isRegistered( oRegister[ sGroup ], sValue ) ) {
            oRegister[ sGroup ].push( sValue );
        }
    }

    /**
     * Checks whether given needle is registered in given register
     *
     * @param oRegister
     * @param sNeedle
     * @returns {boolean}
     */
    function _isRegistered( oRegister, sNeedle ) {
        for ( var i in oRegister ) {
            if ( oRegister[i].indexOf( sNeedle ) > -1 ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Loads all functions and files in register
     *
     * @private
     */
    function _loadAll() {
        if ( blLoaded ) return;
        _load( oRegister );
        blLoaded = true;
    }

    /**
     * Loads all widget functions and files from register
     *
     * @private
     */
    function _loadWidget( sWidget ) {
        if ( sWidget in oWidgetRegister ) {
            _load( oWidgetRegister[ sWidget ] );
        }
    }

    /**
     *
     * @param oRegister
     * @private
     */
    function _load( oRegister ) {
        var iFilesLoaded = 0;
        var iFilesTotal = oRegister[ 'files' ].length;
        for ( var i in oRegister[ 'files' ] ) {
            $.getScript( oRegister[ 'files' ][ i ], function() {
                iFilesLoaded++
                if ( iFilesLoaded == iFilesTotal) {
                    _loadFunctions( oRegister );
                }
            });
            //document.write('<script type="text/javascript" src="'+ oRegister[ sWidget ][ 'files' ][ i ] +'"></script>');
        }
    }

    /**
     * Loads widget functions from register
     *
     * @private
     */
    function _loadFunctions( oRegister ) {
        for ( var i in oRegister[ 'functions' ] ) {
            $.globalEval( oRegister[ 'functions' ][ i ] );
        }
    }

    return obj;
})();

// do not change to $.ready(), as widget functions will not yet be registered
window.addEventListener("load", function() {
    WidgetsHandler.load();
}, false );