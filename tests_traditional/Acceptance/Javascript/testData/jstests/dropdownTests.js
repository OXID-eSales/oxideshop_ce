module('oxDropDown', {

    setup : function() {

        var oBody = $('#fixture');

        var oElement = $(
            '<div id="content">' +
                '<div id="selectList" class="dropDown">' +
                    '<p id="selectListHead" class="selectorLabel underlined">' +
                        '<label>Color:</label>' +
                        '<span>Please choose</span>' +
                    '</p>' +
                    '<input type="hidden" value="" name="selectVal">' +
                    '<ul class="drop vardrop FXgradGreyLight shadow">' +
                        '<li class=""><a class="selected" href="#" data-selection-id="cda7a650c5856cf2f6738072447d7825">gray</a></li>' +
                        '<li class=""><a id="val2" class="" href="#" data-selection-id="fe01d67a002dfa0f3ac084298142eccd">orange</a></li>' +
                    '</ul>' +
                '</div>' +
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

    equals(typeof(oxDropDown), "object", "Check object.");

    // methods
    equals(oxDropDown.hasOwnProperty("showDropDown"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("hideDropDown"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("toggleDropDown"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("hideAll"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("action"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("select"), true, "Check existing method.");
    equals(oxDropDown.hasOwnProperty("isDisabled"), true, "Check existing method.");

});


test("showHideDropDownValues", function (){

    // setting widget
    $('#selectListHead').oxDropDown();

    equals( $('ul.drop').css("display"), 'none', "Value list hidden ");

    // clicking on dropdown header
    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'block', "Value list open");
    equals( $('ul.drop li').size(), 3, "Should be one more value ( please choose)");
    equals( $('ul.drop').css('width'), $('#selectList').outerWidth() + 'px' , "Header width shoul be equal value list width");

    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'none', "Checking visibility");

    //disabled dropdown
    $('#selectListHead').addClass('js-disabled');

    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'none', "Checking visibility for disabled dropdown");

});


test("selectingValue", function (){

    $('#selectListHead').oxDropDown();

    equals( $('ul.drop').css("display"), 'none', "Checking visibility");
    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'block', "Checking visibility");

    $('#val2').click();

    equals( $('p.selectorLabel span').html(), 'orange', "Setting label");
    equals( $('input[name=selectVal]').val(), 'fe01d67a002dfa0f3ac084298142eccd', "Setting value");
    equals( $('ul.drop').css("display"), 'none', "Checking visibility");

    //checking if selected value marked
    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'block', "Checking visibility");
    equals( $('#val2').hasClass("selected"), true, "Checking visibility");

    $('#selectListHead').click();
    equals( $('ul.drop').css("display"), 'none', "Checking visibility");

});
