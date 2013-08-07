var WidgetsHandler = (function() {
    var oRegister= {}, blLoaded = false;

    var obj = {
        /**
         * Registers function if it was not already registered
         *
         * @param sFunction function text
         * @param sWidget widget name
         */
        registerFunction: function( sFunction, sWidget ) {
            if ( !_isRegistered( sFunction ) ) {
                _registerFunction( sFunction, sWidget );
            }
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
     * Checks whether given function is registered in all widgets
     *
     * @param sFunction
     * @returns {boolean}
     */
    function _isRegistered( sFunction ) {
        for ( var i in oRegister ) {
            if ( oRegister[i].indexOf(sFunction) > -1 ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Registers given function
     *
     * @param sFunction
     * @param sWidget
     * @private
     */
    function _registerFunction( sFunction, sWidget ) {
        if ( !( sWidget in oRegister ) ) {
            oRegister[ sWidget ] = [];
        }
        oRegister[ sWidget ].push( sFunction );
    }

    /**
     * Loads all functions in registry
     *
     * @private
     */
    function _loadAll() {
        if ( blLoaded ) return;
        for ( var sWidget in oRegister ) {
            _loadWidget( sWidget );
        }
        blLoaded = true;
    }

    /**
     * Loads all functions in registry
     *
     * @private
     */
    function _loadWidget( sWidget ) {
        if ( ! sWidget in oRegister ) return;
        for ( var i in oRegister[ sWidget ] ) {
            $.globalEval( oRegister[ sWidget ][ i ] );
        }
    }

    return obj;
})();

// do not change to $.ready(), as widget functions will not yet be registered
window.addEventListener("load", function() {
    WidgetsHandler.load();
}, false );