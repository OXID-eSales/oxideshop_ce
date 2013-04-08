// To do, check if there is no dublication with oxscript includes.
function load_scripts_dynamically( aScripts ) {
    aScripts = $.unique( aScripts );
    $.each( aScripts, function( key, sScript ) {
        document.write('<script type="text/javascript" src="'+ sScript +'"></script>');
    } );
}

if ( load_oxwidgets == undefined ) { 
    var load_oxwidgets = new Array(); 
}
load_scripts_dynamically( load_oxwidgets );