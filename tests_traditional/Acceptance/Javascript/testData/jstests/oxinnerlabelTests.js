module('oxInnerLabel', {

    setup : function() {

        var oBody = $( '#fixture' );

        var oElement = $(
                '<button id="userChangeAddress">Change</button>'+
                '<label id="oxDayLabel" for="oxDay">Day</label>'+
                '<input id="oxDay" type="text" value="" style="position:absolute;top:10px;left:15px;" />'
        );
        oBody.html( oElement );
    },

    teardown : function() {
        var oBody = $( '#fixture' );
        oBody.html( "" );
    }

});

test("main", function() {
    equals( typeof(oxInnerLabel), "object", "Check object" );

    // methods
    equals( oxInnerLabel.hasOwnProperty("_reload"), true, "Check existing method" );
});

/*test("testPosition", function (){
    // We expect one asynchronous test ( stop() start() )
    asyncTest('test1', function() {
      expect(1);
      ok(true);
      start();
    });

    // setting widget
    $('#oxDay').oxInnerLabel( {sReloadElement:'#userChangeAddress'} );

    // Check if label is on input after creation
    equals( $('#oxDayLabel').css( 'top' ), '10px', "Label is in place after creation" );
    equals( $('#oxDayLabel').css( 'left' ), '15px', "Label is in place after creation" );

    $('#oxDay').css( 'top', '20px' );
    $('#oxDay').css( 'left', '5px' );

    // Check if label is not on input after input change it's position but reload not triggered
    notEqual( $('#oxDayLabel').css( 'top' ), '20px', "Label is not on input after input change place and no reload triggered" );
    notEqual( $('#oxDayLabel').css( 'left' ), '5px', "Label is not on input after input change place and no reload triggered" );
    $('#userChangeAddress').trigger( 'click' );

    // Pause the test firs as there is timout after reload.
    stop();

    setTimeout(function() {
        equals( $('#oxDayLabel').css( 'top' ), '20px', "Label is in place after reload triggered" );
        equals( $('#oxDayLabel').css( 'left' ), '5px', "Label is in place after reload triggered" );
        // After the assertion has been called,
        // continue the test
        start();
    }, 200);
});*/