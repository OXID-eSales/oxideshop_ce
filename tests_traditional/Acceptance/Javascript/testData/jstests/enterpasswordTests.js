module('enter password');

test( 'showInput()', function() {
    var oSource  = $( '<input type="text" class="testTarget[test1,test2]">' );
    var oBody = $( '#fixture' );
    oBody.html( '<input type="text" class="test1" style="display:none"><input type="text" class="test2" style="display:none"><input type="text" class="test3" style="display:none">' );

    equals( $( '.test1' ).css( "display" ), "none" );
    equals( $( '.test2' ).css( "display" ), "none" );
    equals( $( '.test3' ).css( "display" ), "none" );

    oxInputValidator.showInput( oSource, true, "test" );

    equals( $( '.test1' ).css( "display" ), "inline" );
    equals( $( '.test2' ).css( "display" ), "inline" );
    equals( $( '.test3' ).css( "display" ), "none" );

    oBody.html("");
});


