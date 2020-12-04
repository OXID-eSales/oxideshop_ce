module('Country state select');

var allStates = new Array();
var allStateIds = new Array();
var allCountryIds = new Object();
var cCount = 0;
var states = new Array();
var ids = new Array();
var i = 0;

allStates[++cCount] = states;
allStateIds[cCount]  = ids;
allCountryIds['a7c40f6320aeb2ec2.72885259']  = cCount;


var states = new Array();
var ids = new Array();
var i = 0;

states[i] = 'Brandenburg';
ids[i] = 'BB';
i++;

states[i] = 'Berlin';
ids[i] = 'BE';
i++;

states[i] = 'Baden-Wurttemberg';
ids[i] = 'BW';
i++;

allStates[++cCount] = states;
allStateIds[cCount]  = ids;
allCountryIds['a7c40f631fc920687.20179984']  = cCount;



var states = new Array();
var ids = new Array();
var i = 0;

allStates[++cCount] = states;
allStateIds[cCount]  = ids;
allCountryIds['a7c40f6321c6f6109.43859248']  = cCount;

var states = new Array();
var ids = new Array();
var i = 0;

allStates[++cCount] = states;
allStateIds[cCount]  = ids;
allCountryIds['a7c40f632a0804ab5.18804076']  = cCount;


var states = new Array();
var ids = new Array();
var i = 0;

states[i] = 'Alabama';
ids[i] = 'AL';
i++;

states[i] = 'Alaska';
ids[i] = 'AK';
i++;

allStates[++cCount] = states;
allStateIds[cCount]  = ids;
allCountryIds['8f241f11096877ac0.98748826']  = cCount;

test('getStates()', function() {
    equals(oxCountryStateSelect.getStates("a7c40f631fc920687.20179984" , allStates, allCountryIds).length, 3, "Germany 3 states");
    equals(oxCountryStateSelect.getStates("a7c40f632a0804ab5.18804076" , allStates, allCountryIds).length, 0, "Without states");
});

test('getStatesValues()', function() {
    equals(oxCountryStateSelect.getStatesValues("a7c40f631fc920687.20179984" , allStateIds, allCountryIds).length, 3, "Germany 3 states");
    equals(oxCountryStateSelect.getStatesValues("a7c40f632a0804ab5.18804076" , allStateIds, allCountryIds).length, 0, "Without states");
});

test('removeSelectOptions()', function() {

     var sHTMLelement =
         '<select>' +
                     '<option>label 1 </option>' +
                     '<option>label 2 </option>' +
                     '<option>label 3 </option>' +
                 '</select>';

     var oSelectElement = $( sHTMLelement );

     oSelectElement = oxCountryStateSelect.removeSelectOptions(oSelectElement);
     equals($('option', oSelectElement).size(), 0, "empty");
});

test('addSelectOptions()', function() {

     var sHTMLelement =
        '<select>' +
                    '<option>label 1 </option>' +
                    '<option selected>label 2 </option>' +
                    '<option>label 3 </option>' +
                '</select>';

     var oSelectElement = $( sHTMLelement );

     oSelectElement = oxCountryStateSelect.addSelectOptions(oSelectElement
             , oxCountryStateSelect.getStates("a7c40f631fc920687.20179984" , allStates, allCountryIds)
             , oxCountryStateSelect.getStatesValues("a7c40f631fc920687.20179984" , allStateIds, allCountryIds)
             , "BE");

     equals($('option', oSelectElement).size(), 6, "empty");
});


test('getStateSelect()', function() {

     var sHTMLelement =
         '<li>' +
             '<select id="countries">' +
                 '<option>label 1 </option>' +
                 '<option>label 2 </option>' +
                 '<option>label 3 </option>' +
             '<select>' +
         '</li>' +
         '<li>' +
             '<span>' +
                 '<select>' +
                    '<option>label 1 </option>' +
                    '<option>label 2 </option>' +
                    '<option>label 3 </option>' +
                    '<option>label 4 </option>' +
                '</select>'+
            '</span>' +
         '</li>' ;

     var oCountryElement = $('#countries', sHTMLelement );

     oSelectElement = oxCountryStateSelect.getStateSelect(oCountryElement);
     equals($('option', oSelectElement).size(), 4, "empty");
});

test('getStateSelectSpan()', function() {

     var sHTMLelement =
         '<li>' +
             '<select id="countries">' +
                 '<option>label 1 </option>' +
                 '<option>label 2 </option>' +
                 '<option>label 3 </option>' +
             '<select>' +
        '</li>' +
        '<li>' +
            '<span>' +
                '<select id="states">' +
                   '<option>label 1 </option>' +
                   '<option>label 2 </option>' +
                   '<option>label 3 </option>' +
                   '<option>label 4 </option>' +
               '</select>'+
           '</span>' +
        '</li>' ;

     var oStatesElement = $('#states', sHTMLelement );
     oSpanElement = oxCountryStateSelect.getStateSelectSpan(oStatesElement);
     equals(oSpanElement.is("span"), true, "empty");
});

test('manageStateSelect()', function() {

     var sHTMLelement =
           '<span>' +
               '<select id="states">' +
              '</select>'+
          '</span>';

     var oStatesElement = $('#states', sHTMLelement );

     oStatesElement = oxCountryStateSelect.manageStateSelect(oStatesElement
             , oxCountryStateSelect.getStates("a7c40f631fc920687.20179984" , allStates, allCountryIds)
             , oxCountryStateSelect.getStatesValues("a7c40f631fc920687.20179984" , allStateIds, allCountryIds)
             , "BE");

     equals($('option', oStatesElement).size(), 3, "empty");

     oStatesElement = oxCountryStateSelect.manageStateSelect(oStatesElement, null, null, null);

     equals($('option', oStatesElement).size(), 0, "empty");

});
