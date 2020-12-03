module('oxEqualizer', {

    setup : function() {

        var oBody = $('#fixture');

        var oElement = $(
        '<div id="container1">'+
            '<div class="column1" id="column11"></div>'+
            '<div class="column1" id="column12" style="height:100px;"></div>'+
            '<div class="column1" id="column13"></div>'+
        '</div>'+
        '<div id="container2">'+
            '<div class="column2" id="column21" style="height:50px;"></div>'+
            '<div class="column2" id="column22" style="height:100px;"></div>'+
            '<div class="column2" id="column23" style="height:150px;"></div>'+
        '</div>'+
        '<div id="container3">'+
            '<div class="column3 catPicOnly" id="column31" style="height:100px;padding:0px"></div>'+
            '<div class="column3" id="column32"></div>'+
            '<div class="column3" id="column33" style="height:70px; padding:10px"></div>'+
        '</div>'+
        '<div id="target41" style="height:50px;"></div>'+
        '<div id="target42" style="height:200px;"></div>'+
        '<div id="container4">'+
            '<div class="column4" id="column41"></div>'+
            '<div class="column4" id="column42" style="height:100px;"></div>'+
            '<div class="column4" id="column43"></div>'+
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

    equals( typeof(oxEqualizer), "object", "Check object" );

    // methods
    equals(oxEqualizer.hasOwnProperty("equalHeight"), true, "Check existing method.");
    equals(oxEqualizer.hasOwnProperty("getTallest"), true, "Check existing method.");
});

test("equalizeColumns", function (){
    // setting widget 1
    oxEqualizer.equalHeight($('.column1'));

    equals( $('#column11').css("height"), '100px', "case1: Added heigh");
    equals( $('#column12').css("height"), '100px', "case1: Added heigh");
    equals( $('#column13').css("height"), '100px', "case1: Added heigh");

    // setting widget 2
    oxEqualizer.equalHeight($('.column2'));

    equals( $('#column21').css("height"), '150px', "case2: Added heigh");
    equals( $('#column22').css("height"), '150px', "case2: Added heigh");
    equals( $('#column23').css("height"), '150px', "case2: Added heigh");
});

test("equalizeColumnsWithPics", function (){
    // setting widget 3
    oxEqualizer.equalHeight($('.column3'));

    equals( $('#column31').css("height"), '100px', "case3: Added heigh");
    equals( $('#column32').css("height"), '100px', "case3: Added heigh");
    equals( $('#column33').css("height"), '80px', "case3: Added heigh");
});


test("equalizeColumnsWithTarget", function (){
    oxEqualizer.equalHeight($('.column4'), $('#target41'));

    equals( $('#column41').css("height"), '100px', "case4: Added heigh");
    equals( $('#column42').css("height"), '100px', "case4: Added heigh");
    equals( $('#column43').css("height"), '100px', "case4: Added heigh");

    // setting widget 4
    oxEqualizer.equalHeight($('.column4'), $('#target42'));

    equals( $('#column41').css("height"), '200px', "case4: Added heigh");
    equals( $('#column42').css("height"), '200px', "case4: Added heigh");
    equals( $('#column43').css("height"), '200px', "case4: Added heigh");
});

test("equalizeColumnsMultipleTimes", function (){
    // setting widget 1
    oxEqualizer.equalHeight($('.column1'));

    equals( $('#column11').css("height"), '100px', "case1: Added heigh");
    equals( $('#column12').css("height"), '100px', "case1: Added heigh");
    equals( $('#column13').css("height"), '100px', "case1: Added heigh");

    // setting widget 1x2
    $('#column12').height(50).removeClass('oxEqualized');
    oxEqualizer.equalHeight($('.column1'));

    equals( $('#column11').css("height"), '50px', "case1x2: Added heigh");
    equals( $('#column12').css("height"), '50px', "case1x2: Added heigh");
    equals( $('#column13').css("height"), '50px', "case1x2: Added heigh");

    // setting widget 1x3
    $('#column12').height(150).removeClass('oxEqualized');
    oxEqualizer.equalHeight($('.column1'));

    equals( $('#column11').css("height"), '150px', "case1x3: Added heigh");
    equals( $('#column12').css("height"), '150px', "case1x3: Added heigh");
    equals( $('#column13').css("height"), '150px', "case1x3: Added heigh");
});

