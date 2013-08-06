var WidgetsHandler = (function() {
    var aRegister= [], isLoaded = false;

    var obj = {
        /**
         * Registers function if it was not already registered
         *
         * @param sFunction function text
         */
        registerFunction: function( sFunction ) {
            if ( !_isRegistered( sFunction ) ) {
                _registerFunction( sFunction );
            }
        },

        /**
         * Loads all registered functions
         */
        load: function() {
            _loadFunctions();
        }
    }

    /**
     * Checks whether given function is registered
     *
     * @param sFunction
     * @returns {boolean}
     */
    function _isRegistered( sFunction ) {
        return aRegister.indexOf(sFunction) > -1;
    }

    /**
     * Registers given function
     *
     * @param sFunction
     * @private
     */
    function _registerFunction( sFunction ) {
        aRegister.push( sFunction );
    }

    /**
     * Loads all functions in registry
     *
     * @private
     */
    function _loadFunctions() {
        if ( isLoaded ) return;
        for ( var i in aRegister ) {
            $.globalEval( aRegister[i] );
        }
        isLoaded = true;
    }

    return obj;
})();

// do not change to $.ready(), as widget functions will not yet be registered
window.addEventListener("load", function() {
    WidgetsHandler.load();
}, false );