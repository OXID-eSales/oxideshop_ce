module('Slider');

var sSlider =

'<ul id="promotionSlider">'+
	'<li class="panel">aaa'+
		'<span class="promoBox" style="display: none;"> tekstas aaa </span>'+
	'</li>'+
	'<li class="panel">bbb'+
		'<span class="promoBox" style="display: none;"> tekstas bbb </span>'+
	'</li>'+
'</ul>';

test('showTextSpan()', function() {

	var oElement = $( sSlider );

	oElement = oxSlider.showTextSpan(oElement, '.promoBox');

	equals( oElement.css( "visibility" ) == "visible", true, "shows text message");


});

test('hideTextSpan()', function() {

	var oElement = $( sSlider );

	oElement = oxSlider.hideTextSpan(oElement, '.promoBox');

	equals( oElement.css( "visibility" ) == "hidden", true, "hide text message");

});

test('showControlWithOpacity()', function() {

	var sSlider =
		'<div><a href=# class="start-stop">aaa</a></div>' +
		'<ul id="promotionSlider">'+
			'<li class="panel">aaa'+
				'<span class="promotionText" style="display: none;"> tekstas aaa </span>'+
			'</li>'+
			'<li class="panel">bbb'+
				'<span class="promotionText" style="display: none;"> tekstas bbb </span>'+
			'</li>'+
		'</ul>';

	var oElement = $( sSlider );

	oElement = oxSlider.showControlWithOpacity(oElement, '.start-stop', 1);

	equals(oElement.css( "opacity" ) == 1, true, "opacity 1");

	oElement = $( sSlider );

	oElement = oxSlider.showControlWithOpacity(oElement, '.start-stop', 0);

	equals(oElement.css( "opacity" ) == 0, true, "opacity 0");
});

test('hideControl()', function() {

	var sSlider =
		'<div><a href=# class="start-stop">aaa</a></div>' +
		'<ul id="promotionSlider">'+
			'<li class="panel">aaa'+
				'<span class="promotionText" style="display: none;"> tekstas aaa </span>'+
			'</li>'+
			'<li class="panel">bbb'+
				'<span class="promotionText" style="display: none;"> tekstas bbb </span>'+
			'</li>'+
		'</ul>';

	var oElement = $( sSlider );

	oElement = oxSlider.hideControl(oElement, '.start-stop');

	equals(oElement.css( "display" ) == "none", true, "hide start button");

});

test('hideControl()', function() {

	var sSlider =
		'<div><a href=# class="start-stop" style="display: none;">aaa</a></div>' +
		'<ul id="promotionSlider">'+
			'<li class="panel">aaa'+
				'<span class="promotionText" style="display: none;"> tekstas aaa </span>'+
			'</li>'+
			'<li class="panel">bbb'+
				'<span class="promotionText" style="display: none;"> tekstas bbb </span>'+
			'</li>'+
		'</ul>';

	var oElement = $( sSlider );

	oElement = oxSlider.showControl(oElement, '.start-stop');

	equals(oElement.css( "display" ) == "inline", true, "hide start button");
});

test('getNavigationTabsArray()', function() {

	var oElement = $( sSlider );

	var aNav = oxSlider.getNavigationTabsArray(oElement, 'li');

	equals( aNav.length == 2, true, "2 panels");

});


