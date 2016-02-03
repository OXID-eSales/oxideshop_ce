module('oxCompareLinks', {

    setup : function() {

        var oBody = $('#fixture');

        var oElement = $(
        '<div id="container1">'+
            '<a id="test1_add" href="#" class="compare add" data-aid="test1">Add to compare</a>'+
            '<a id="test1_remove" href="#" class="compare remove" data-aid="test1">Remove from compare</a>'+
        '</div>'+
        '<div id="container2">'+
            '<a id="test2_add" href="#" class="compare add" data-aid="test2">Add to compare</a>'+
            '<a id="test2_remove" href="#" class="compare remove" data-aid="test2">Remove from compare</a>'+
        '</div>'
        );
        oBody.html( oElement );
    },

    teardown : function() {
        var oBody = $('#fixture');
        oBody.html("");
    }

});

test("main", function() {

    equals( typeof(oxCompareLinks), "object", "Check object" );

    // methods
    equals(oxCompareLinks.hasOwnProperty("updateLinks"), true, "Check existing method.");
});

test("updateLinks", function (){

    // all add links visible, remove hidden
    ok($('#test1_add').is(':visible'),'Test1 add to compare link visible');
    ok($('#test1_remove').is(':hidden'),'Test1 remove from compare link hidden');

    ok($('#test2_add').is(':visible'),'Test2 add to compare link visible');
    ok($('#test2_remove').is(':hidden'),'Test2 remove from compare link hidden');

    // update test2 links, hide add show remove
    oxCompareLinks.updateLinks({"test2":true});

    ok($('#test1_add').is(':visible'),'Test1 add to compare link visible');
    ok($('#test1_remove').is(':hidden'),'Test1 remove from compare link hidden');

    ok($('#test2_add').is(':hidden'),'Test2 add to compare link hidden');
    ok($('#test2_remove').is(':visible'),'Test2 remove from compare link visible');
});




