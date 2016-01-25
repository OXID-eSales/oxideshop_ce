module('Inline form validation');

test('isEqual()', function() {
    equals(oxInputValidator.isEqual("aa", "aa" ), true, "equal: aa -> aa");
    equals(oxInputValidator.isEqual("aa", "AA"), false, "not equal case sensitive: aa -> AA");
    equals(oxInputValidator.isEqual("abc", "ab"), false, "not equal: abc -> ab");
    equals(oxInputValidator.isEqual(" abb ", "abb"), true, "equal, but with spaces: ' abb ' -> 'abb'");
});

test('isEmail()', function() {
    equals(oxInputValidator.isEmail( "n+a-m_e@surname.commuseum" ), true, "n+a-m_e@surname.commuseum");
    equals(oxInputValidator.isEmail( "n@m.c" ), true, "n@m.c");
    equals(oxInputValidator.isEmail( "namesurname.com" ), false, "namesurname.com");
    equals(oxInputValidator.isEmail( "namesurname@com" ), false, "namesurname@com");
    equals(oxInputValidator.isEmail( "namesurname@.com" ), false, "namesurname@.com");
    equals(oxInputValidator.isEmail( "@namesurname.com" ), false, "@namesurname.com");
    equals(oxInputValidator.isEmail( "@." ), false, "@.");
});

test('hasLength()', function() {
    equals(oxInputValidator.hasLength( "aabaa", 5 ), true, "length 5: aabaa");
    equals(oxInputValidator.hasLength( " abaa", 5 ), false, "length 4 with space: ' abaa'");
    equals(oxInputValidator.hasLength( "abc aa", 5), true, "lenght more 5: 'abc aa'");
    equals(oxInputValidator.hasLength( "abb", 5), false, "less 5: 'abb'");
    equals(oxInputValidator.hasLength( "abc aa asdas ", 5), true, "lenght more 5: 'abc aa asdas '");
});



test('showErrorMessage()', function() {

    var sHTMLelement =
            '<li class="oxValid">' +
                        '<label>label 1 </label>' +
                        '<input type="text" class="js-oxValidate js-oxValidate_notEmpty">' +
                        '<p class="oxValidateError" style="display: none;">' +
                            '<span class="js-oxError_notEmpty" style="display: none;"> not empty error message </span>' +
                            '<span class="js-oxError_email" style="display: none;"> bad email error message </span>' +
                        '</p>' +
                    '</li>';

    var oFormElement = $( sHTMLelement );

    oFormElement = oxInputValidator.showErrorMessage( oFormElement, 'js-oxError_email');
    equals(oFormElement.hasClass( "oxInValid" ), true, "List element shows error");

    oErrorParagraf = oFormElement.children("p.oxValidateError");
    equals(oErrorParagraf.css( "display" ) == "block" || oErrorParagraf.css( "display" ) == ""  , true, "Show error paragraf");

    oNotEmailErrorSpan = oErrorParagraf.children( "span.js-oxError_email" );
    equals(oNotEmailErrorSpan.css( "display" ) == "inline" || oNotEmailErrorSpan.css( "display" ) == "" , true, "Show bad email error");

    oNotEmptyErrorSpan = oErrorParagraf.children("span.js-oxError_notEmpty");
    equals(oNotEmptyErrorSpan.css( "display" ), "none", "Not empty error still hidden");

});

test('hideErrorMessage()', function() {

    var sHTMLelement =
            '<li class="oxInValid">' +
                        '<label>label 1 </label>' +
                        '<input type="text" class="js-oxValidate js-oxValidate_notEmpty">' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="js-oxError_notEmpty" style="display: none;"> not empty error message </span>' +
                            '<span class="js-oxError_email" style="display: inline;"> bad email error message </span>' +
                        '</p>' +
                    '</li>';

    var oFormElement = $( sHTMLelement );

    oFormElement = oxInputValidator.hideErrorMessage( oFormElement );
    equals(oFormElement.hasClass( "oxValid" ), true, "List element don't shows errors");

    oErrorParagraf = oFormElement.children("p.oxValidateError");
    equals(oErrorParagraf.css( "display" ), "none", "Don't Show error paragraf");

    oNotEmailErrorSpan = oErrorParagraf.children( "span.js-oxError_email" );
    equals(oNotEmailErrorSpan.css( "display" ), "none", "Bad email error hidden");

    oNotEmptyErrorSpan = oErrorParagraf.children("span.js-oxError_notEmpty");
    equals(oNotEmptyErrorSpan.css( "display" ), "none", "Not empty error still hidden");

});

test('setDefaultState()', function() {

    var sHTMLelement =
            '<li class="oxInValid">' +
                        '<label>label 1 </label>' +
                        '<input type="text" class="js-oxValidate js-oxValidate_notEmpty">' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                            '<span class="js-oxError_email" style="display: inline;"> bad email error message </span>' +
                        '</p>' +
                    '</li>';

    var oFormElement = $( sHTMLelement );

    oFormElement = oxInputValidator.setDefaultState( oFormElement.children("input"));
    equals(!oFormElement.hasClass( "oxValid" ) && !oFormElement.hasClass( "oxInValid" ) , true, "List element has default state");

    oErrorParagraf = oFormElement.children("p.oxValidateError");
    equals(oErrorParagraf.css( "display" ), "none", "Don't Show error paragraf");

    oNotEmailErrorSpan = oErrorParagraf.children( "span.js-oxError_email" );
    equals(oNotEmailErrorSpan.css( "display" ), "none", "Bad email error hidden");

    oNotEmptyErrorSpan = oErrorParagraf.children("span.js-oxError_notEmpty");
    equals(oNotEmptyErrorSpan.css( "display" ), "none", "Not empty error still hidden");

});

test('inputValidation()', function() {
    var sHTMLelement =
            '<li class="oxInValid">' +
                        '<label>label 1 </label>' +
                        '<input type="text" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email">' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                            '<span class="js-oxError_email" style="display: inline;"> bad email error message </span>' +
                        '</p>' +
                    '</li>';

    var oFormElement = $( sHTMLelement );
    var oInput = oFormElement.children("input");

    equals( oxInputValidator.inputValidation( oInput ) , 'js-oxError_notEmpty', "Not valid element: empty");

    oInput.val('aaa');
    equals( oxInputValidator.inputValidation( oInput ) , 'js-oxError_email', "Not valid element: not empty but not email");

    oInput.val('aaa@aaa.lt');
    equals( oxInputValidator.inputValidation( oInput ) , true, "Valid element");

});

test('submitValidation()', function() {

    var sElement =
        '<form>' +
            '<ul>' +
                '<li>' +
                        '<label> label 1 </label>' +
                        '<input id="first" type="text" class="js-oxValidate js-oxValidate_notEmpty>' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                        '</p>' +
                    '</li>'+
                    '<li>' +
                            '<label> label 2 </label>' +
                            '<input type="text">' +
                        '</li>'+
                        '<li>' +
                            '<label> label 3 </label>' +
                            '<input type="text" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email">' +
                            '<p class="oxValidateError" style="display: block;">' +
                                '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                                '<span class="js-oxError_email" style="display: inline;"> bad email error message </span>' +
                            '</p>' +
                        '</li>' +
            '</ul>' +
        '</form>';

    var oForm = $( sElement );

    //equals( oxInputValidator.submitValidation( oForm ) , false, "has empty inputs");

    $( "input", oForm ).each(   function(index) {
        $( this ).val('aaa@aaa.lt');
    });


    equals( oxInputValidator.submitValidation( oForm ) , true, "all inputs filed ok");

});


test('getLength()', function() {

    var sElement =
        '<form>' +
            '<input id="passwordLength" type="hidden" value="5">' +
            '<ul>' +
                '<li>' +
                        '<label> label 1 </label>' +
                        '<input id="first" type="text" class="oxValidate oxValidate_notEmpty>' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                        '</p>' +
                    '</li>'+
                '<li>' +
                            '<label> label 2 </label>' +
                            '<input type="text">' +
                        '</li>'+
                        '<li>' +
                            '<label> label 3 </label>' +
                            '<input type="text" class="oxValidate oxValidate_notEmpty oxValidate_email">' +
                            '<p class="oxValidateError" style="display: block;">' +
                                '<span class="oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                                '<span class="oxError_email" style="display: inline;"> bad email error message </span>' +
                            '</p>' +
                        '</li>' +
            '</ul>' +
        '</form>';

    var oForm = $( sElement );

    equals( oxInputValidator.getLength( oForm ), 5, "password length correct");

    var sElement =
        '<form>' +
            '<ul>' +
                '<li>' +
                        '<label> label 1 </label>' +
                        '<input id="first" type="text" class="oxValidate oxValidate_notEmpty>' +
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                        '</p>' +
                    '</li>'+
                '<li>' +
                            '<label> label 2 </label>' +
                            '<input type="text">' +
                        '</li>'+
                        '<li>' +
                            '<label> label 3 </label>' +
                            '<input type="text" class="oxValidate oxValidate_notEmpty oxValidate_email">' +
                            '<p class="oxValidateError" style="display: block;">' +
                                '<span class="oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                                '<span class="oxError_email" style="display: inline;"> bad email error message </span>' +
                            '</p>' +
                        '</li>' +
            '</ul>' +
        '</form>';

    var oForm = $( sElement );

    equals( oxInputValidator.getLength( oForm ), undefined, "form hasn't hidden value");

});

test('selectValidate()', function() {
    var sHTMLelement =
            '<li class="oxInValid">' +
                        '<label>label 1 </label>' +
                        '<select type="text" class="js-oxValidate js-oxValidate_notEmpty">' +
                            '<option value=""></option>'+
                            '<option value="1"></option>'+
                        '</select>'+
                        '<p class="oxValidateError" style="display: block;">' +
                            '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                        '</p>' +
                    '</li>';

    var oFormElement = $( sHTMLelement );
    var oSelect = oFormElement.children("select");

    //Check if validation faile when empty option selected.
    equals( oxInputValidator.inputValidation( oSelect ), 'js-oxError_notEmpty', "Not valid element: empty");
    
    // Chenge to option with value and check if validation not faile.
    oSelect.children(" option[value='1']").attr('selected', 'selected');
    oSelect.trigger('change');
    notEqual( oxInputValidator.inputValidation( oSelect ), 'js-oxError_notEmpty', "Not valid element: empty");
    
    // Chenge to option with no value and check if validation faile.
    oSelect.children(" option[value='']").attr('selected', 'selected');
    oSelect.trigger('change');
    equals( oxInputValidator.inputValidation( oSelect ), 'js-oxError_notEmpty', "Not valid element: empty");

});

test('dateSelectSelectionValidate()', function() {
    var sHTMLelement =
            '<li class="oxInValid">' +
                '<label>label 1 </label>' +
                '<select type="text" class="oxMonth js-oxValidate js-oxValidate_date">' +
                    '<option value="">-</option>' +
                    '<option value="1">January</option>' +
                    '<option value="2">February</option>' +
                    '<option value="3">March</option>' +
                    '<option value="4">April</option>' +
                '</select>' +
                '<input type="text" value="" maxlength="2" data-fieldsize="xsmall"' +
                    'name="invadr[oxuser__oxbirthdate][day]" class="oxDay js-oxValidate" id="oxDay" />' +
                '<input type="text" value="" maxlength="4" data-fieldsize="small"' +
                    'name="invadr[oxuser__oxbirthdate][year]" class="oxYear js-oxValidate" id="oxYear" />' +
                '<p class="oxValidateError" style="display: block;">' +
                    '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                    '<span class="js-oxError_incorrectDate">Incorrect date</span>' +
                '</p>' +
            '</li>';

    oFormElement = $( sHTMLelement );
    oDay = oFormElement.children(".oxDay");
    oMonth = oFormElement.children(".oxMonth");
    oYear = oFormElement.children(".oxYear");
    
    // Check if validate while all empty by default.
    notEqual( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Valid element while empty by default");
    
    // Change day to option with value and check if validation faile as month and year without value.
    oDay.val("1");
    oDay.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while only day selected");
    
    // Change month to option with value and check if validation faile as year still without value.
    oMonth.children(" option[value='1']").attr('selected', 'selected');
    oMonth.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while no year selected");
    
    // Change month to no value and year to with value to check if failure if only month not selected.
    oMonth.children(" option[value='']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("1");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while no month selected");
    
    // Change month to value and day to no value to check if validate while only month without value.
    oMonth.children(" option[value='1']").attr('selected', 'selected');
    oMonth.trigger('change');
    oDay.val("");
    oDay.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while no day selected");
    
    // Change day month and year to value to check if validation pass.
    oDay.val("1");
    oDay.trigger('change');
    oMonth.children(" option[value='1']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("1");
    oYear.trigger('change');
    notEqual( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Valid while all selected");
    
    // Change day month and year to no value to check if validation pass.
    oDay.val("");
    oDay.trigger('change');
    oMonth.children(" option[value='']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("");
    oYear.trigger('change');
    notEqual( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Valid while none selected");
    
    // Change month to value to check if faile while day and year without value.
    oMonth.children(" option[value='1']").attr('selected', 'selected');
    oMonth.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while only month selected");
    
    // Change year to value and month to no to check if faile while day and month without value
    oMonth.children(" option[value='']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("1");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_notEmpty', "Not valid element while only year selected");

});

test('dateSelectDateValidate()', function() {
    var sHTMLelement =
            '<li class="oxInValid">' +
                '<label>label 1 </label>' +
                '<select type="text" class="oxMonth js-oxValidate js-oxValidate_date">' +
                    '<option value="">-</option>' +
                    '<option value="1">January</option>' +
                    '<option value="2">February</option>' +
                    '<option value="3">March</option>' +
                    '<option value="4">April</option>' +
                '</select>' +
                '<input type="text" value="" maxlength="2" data-fieldsize="xsmall"' +
                    'name="invadr[oxuser__oxbirthdate][day]" class="oxDay js-oxValidate" id="oxDay" />' +
                '<input type="text" value="" maxlength="4" data-fieldsize="small"' +
                    'name="invadr[oxuser__oxbirthdate][year]" class="oxYear js-oxValidate" id="oxYear" />' +
                '<p class="oxValidateError" style="display: block;">' +
                    '<span class="js-oxError_notEmpty" style="display: inline;"> not empty error message </span>' +
                    '<span class="js-oxError_incorrectDate">Incorrect date</span>' +
                '</p>' +
            '</li>';

    oFormElement = $( sHTMLelement );
    oDay = oFormElement.children(".oxDay");
    oMonth = oFormElement.children(".oxMonth");
    oYear = oFormElement.children(".oxYear");

    // Change to valid date to check if valid
    oDay.val("31");
    oDay.trigger('change');
    oMonth.children(" option[value='1']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("1991");
    oYear.trigger('change');
    notEqual( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Valid as january always has 31 days");

    // Change to invalid date to check if validation faile
    oDay.val("31");
    oDay.trigger('change');
    oMonth.children(" option[value='4']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("1991");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as april do not has 31 days");

    // Change to leap year to check if february has 29 days
    oDay.val("29");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("2012");
    oYear.trigger('change');
    notEqual( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Valid as february has 29 days in leap years");

    // Change to not leap year to check if february has 28 days
    oDay.val("29");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("2011");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as february has 28 days in non leap years");
    
    // Change day to zero numbers to check if faile
    oDay.val("0");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("2011");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as day must be above 0");
    
    // Change year to zero numbers to check if faile
    oDay.val("2");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("0");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as day must be above 0");
    
    // Change day to non numbers to check if faile
    oDay.val("2.");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("2011");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as day must contain only digits");
    
    // Change year to non numbers to check if faile
    oDay.val("2");
    oDay.trigger('change');
    oMonth.children(" option[value='2']").attr('selected', 'selected');
    oMonth.trigger('change');
    oYear.val("201.");
    oYear.trigger('change');
    equal( oxInputValidator.inputValidation( oMonth ), 'js-oxError_incorrectDate', "Invalid as year must contain only digits");

});