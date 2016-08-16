// get the localised values
var text_number_format = itsg_gf_listnumformat_admin_js_settings.text_number_format;
var text_enable_number_format = itsg_gf_listnumformat_admin_js_settings.text_enable_number_format;
var text_currency = itsg_gf_listnumformat_admin_js_settings.text_currency;
var text_rounding_direction = itsg_gf_listnumformat_admin_js_settings.text_rounding_direction;
var text_do_not_round = itsg_gf_listnumformat_admin_js_settings.text_do_not_round;
var text_rounding = itsg_gf_listnumformat_admin_js_settings.text_rounding;
var text_range = itsg_gf_listnumformat_admin_js_settings.text_range;
var text_min = itsg_gf_listnumformat_admin_js_settings.text_min;
var text_max = itsg_gf_listnumformat_admin_js_settings.text_max;
var text_format_currency = itsg_gf_listnumformat_admin_js_settings.text_format_currency;
var text_format_decimal_dot = itsg_gf_listnumformat_admin_js_settings.text_format_decimal_dot;
var text_format_decimal_comma = itsg_gf_listnumformat_admin_js_settings.text_format_decimal_comma;
var text_fixed_point = itsg_gf_listnumformat_admin_js_settings.text_fixed_point;

// ADD drop down options to list field in form editor - hooks into existing GetFieldChoices function.
(function (w){
	var GetFieldChoicesOld = w.GetFieldChoices;

	w.GetFieldChoices = function (){

		var str = GetFieldChoicesOld.apply( this, [field] );

		if( field.choices == undefined )
			return "";

		for( var index = 0; index < field.choices.length; index++ ){
			var inputType = GetInputType( field );
			var isNumber = field.choices[ index ].isNumber ? 'checked' : '';
			var isNumberFixedPoint = field.choices[ index ].isNumberFixedPoint ? 'checked' : '';

			var value = field.enableChoiceValue ? String( field.choices[ index ].value ) : field.choices[ index ].text;
			if ( 'list' == inputType ){
				// first time around add a heading
				if ( 0 == index ){
					str += "<p><strong>" + text_number_format + "</strong></p>";
				}
				str += "<div>";

				// option for enable number format column
				str += "<input type='checkbox' name='choice_number_enable' id='list_choice_number_enable_" + index + "' " + isNumber + " onclick='SetFieldChoiceNumFormat( " + index + " );itsg_gf_list_numformat_init();' /> ";
				str += "<label class='inline' for='list_choice_number_enable_" + index + "'>" + value + " - " + text_enable_number_format + "</label>";
				str += "<div style='display:none; background: rgb(244, 244, 244) none repeat scroll 0px 0px; padding: 10px; border-bottom: 1px solid grey; margin: 10px 0;' class='list_choice_number_options_" + index + "'>";

				// option for number format
				str += "<div style='clear: both;'>";
				str += "<label class='section_label' for='list_choice_number_format_" + index + "'>";
				str += text_number_format + "</label>";
				str += "</div>";

				str += "<select class='choice_number_format' id='list_choice_number_format_" + index + "' onchange='SetFieldChoiceNumFormat( " + index + " );' style='margin-bottom: 10px;' >";
				str += "<option value='decimal_dot'>9,999.99</option>";
				str += "<option value='decimal_comma'>9.999,99</option>";
				str += "<option value='currency'>" + text_currency + "</option>";
				str += "</select>";

				// option for rounding to decimal places
				str += "<div style='clear: both;'>";
				str += "<label class='section_label' for='list_choice_number_rounding_" + index + "'>";
				str += text_rounding + "</label>";
				str += "</div>";

				str += "<select class='choice_number_rounding' id='list_choice_number_rounding_" + index + "' onchange='SetFieldChoiceNumFormat( " + index + " );' style='margin-bottom: 10px;' >";
				str += "<option value='norounding'>Do not round</option>";
				str += "<option value='0'>0</option>";
				str += "<option value='1'>1</option>";
				str += "<option value='2'>2</option>";
				str += "<option value='3'>3</option>";
				str += "<option value='4'>4</option>";
				str += "<option value='5'>5</option>";
				str += "</select>";
				str += "<br>";

				// option for fixed point notation
				str += "<input type='checkbox' id='list_choice_number_fixed_point_" + index + "' " + isNumberFixedPoint + " onclick='SetFieldChoiceNumFormat( " + index + " );' /> ";
				str += "<label class='inline' for='list_choice_number_fixed_point_" + index + "'>" + text_fixed_point + "</label>";
				str += "<br>";
				str += "<br>";

				// option for rounding direction
				str += "<div style='clear: both;'>";
				str += "<label class='section_label' for='list_choice_number_rounding_direction_" + index + "'>";
				str += text_rounding_direction + "</label>";
				str += "</div>";

				str += "<select class='choice_number_rounding_direction' id='list_choice_number_rounding_direction_" + index + "' onchange='SetFieldChoiceNumFormat( " + index + " ); itsg_gf_list_numformat_init();' style='margin-bottom: 10px;' >";
				str += "<option value='roundclosest'>Round closest</option>";
				str += "<option value='roundup'>Round up</option>";
				str += "<option value='rounddown'>Round down</option>";
				str += "</select>";
				str += "<br>";

				// options for range
				str += "<div style='clear: both;'>";
				str += "<label class='section_label'>" + text_range + "</label>";
				str += "</div>";

				// option for min range
				str += "<div class='range_min'>";
				str += "<input type='text' id='list_choice_number_range_min_" + index + "' onchange='SetFieldChoiceNumFormat( " + index + " );' >";
				str += "<label for='list_choice_number_range_min_" + index + "'>";
				str +=  text_min + "</label>";
				str += "</div>";

				// option for max range
				str += "<div class='range_max'>";
				str += "<input type='text' id='list_choice_number_range_max_" + index + "' onchange='SetFieldChoiceNumFormat( " + index + " );' >";
				str += "<label for='list_choice_number_range_max_" + index + "'>";
				str +=  text_max + "</label>";
				str += "</div>";

				str += "</div>";
				str += "</div>";
			}
		}

		itsg_gf_list_numformat_init();

		return str;
	}
})(window || {});

// save field options to field object
function SetFieldChoiceNumFormat( index ) {
	var isNumber = jQuery( '#list_choice_number_enable_' + index ).is( ':checked' );
	var isNumberFormat = jQuery( '#list_choice_number_format_' + index ).val();
	var isNumberRounding = jQuery( '#list_choice_number_rounding_' + index ).val();
	var isNumberFixedPoint = jQuery( '#list_choice_number_fixed_point_' + index ).is( ':checked' );
	var isNumberRoundingDirection = jQuery( '#list_choice_number_rounding_direction_' + index ).val();
	var isNumberRangeMin = jQuery( '#list_choice_number_range_min_' + index ).val();
	var isNumberRangeMax = jQuery( '#list_choice_number_range_max_' + index ).val();

	field = GetSelectedField();

	// set field selections
	field.choices[ index ].isNumber = isNumber;
	field.choices[ index ].isNumberFormat = isNumberFormat;
	field.choices[ index ].isNumberRounding = isNumberRounding;
	field.choices[ index ].isNumberFixedPoint = isNumberFixedPoint;
	field.choices[ index ].isNumberRoundingDirection = isNumberRoundingDirection;
	field.choices[ index ].isNumberRangeMin = isNumberRangeMin;
	field.choices[ index ].isNumberRangeMax = isNumberRangeMax;

	LoadBulkChoices( field );

	UpdateFieldChoices( GetInputType( field ) );

	itsg_gf_list_numformat_format_preview();

	itsg_gf_list_numformat_displayed_options( index );
}

// format the field preview inputs for multi-column list field
function itsg_gf_list_numformat_format_preview() {
	for( var index = 0; index < field.choices.length; index++ ) {
		var isNumber = jQuery( '#list_choice_number_enable_' + index ).is( ':checked' );
		if ( true == isNumber ) {
			var isNumberFormat = ( 'undefined' !== typeof field.choices[ index ].isNumberFormat ) ? field.choices[ index ].isNumberFormat : 'decimal_dot';
			if ( 'currency' == isNumberFormat ) {
				var number_format_text = text_format_currency;
			} else if ( 'decimal_comma' == isNumberFormat ) {
				var number_format_text = text_format_decimal_comma;
			} else {
				var number_format_text = text_format_decimal_dot;
			}
			var new_input = '<input type="text" disabled="disabled" value="' + number_format_text + '">';
			var column = index + 1;
			jQuery( 'li#field_' + field.id + ' table.gfield_list_container tbody tr td:nth-child(' + column + ')' ).html( new_input );
		}
	}
}

// format available options
function itsg_gf_list_numformat_displayed_options( index ) {

	var rounding_select = jQuery( '#list_choice_number_rounding_' + index );
	var fixed_poiont_label = jQuery( 'label[for="list_choice_number_fixed_point_' + index + '"]' );
	var fixed_poiont_input = jQuery( '#list_choice_number_fixed_point_' + index );
	var isNumberFormat = jQuery( '#list_choice_number_format_' + index ).val();

	if ( 'currency' == isNumberFormat ) {
		// hide fixed point notation - does not apply to currency format
		fixed_poiont_label.hide();
		fixed_poiont_label.removeClass( 'inline' );
		fixed_poiont_input.hide();
		// is selected rounding option is more than 2 - select 2
		var rounding_selected_option = rounding_select.find( 'option:selected' ).val();
		if ( rounding_selected_option > 2 || 1 == rounding_selected_option || 'norounding' == rounding_selected_option ) {
			rounding_select.val(2);
		}
		// hide options 1, >2 and 'Do not round'
		rounding_select.find( 'option' ).each( function() {
			var rounding_select_option = jQuery( this );
			var rounding_select_option_value = jQuery( this ).val();
			if ( rounding_select_option_value > 2 || 1 == rounding_select_option_value  || 'norounding' == rounding_select_option_value ) {
				rounding_select_option.hide();
			}
		});
	} else {
		// display fixed point notation
		fixed_poiont_label.show();
		fixed_poiont_label.addClass( 'inline' );
		fixed_poiont_input.show();
		// display all options
		rounding_select.find( 'option' ).each( function() {
			jQuery( this ).show();
		});
	}
}

// handles custom options - runs on field load and when field options change
function itsg_gf_list_numformat_init() {
	setTimeout(function(){
		var field_type = field.type;
		if ( 'list' == field_type ) {
			if ( field.enableColumns ) {
				// hide single column options
				jQuery( '.list_number_settings' ).hide();

				// set up mulit-column options
				for( var index = 0; index < field.choices.length; index++ ) {
					var isNumber = field.choices[ index ].isNumber;
					if ( true == isNumber ) {

						// display options
						if ( field.choices[ index ].isNumber ) {
							jQuery( '.list_choice_number_options_' + index ).show();
						} else {
							jQuery( '.list_choice_number_options_' + index ).hide();
						}

						// set values
						var isNumber = 'undefined' !== typeof field.choices[ index ].isNumber ? field.choices[ index ].isNumber : false;
						var isNumberFormat = 'undefined' !== typeof field.choices[ index ].isNumberFormat ? field.choices[ index ].isNumberFormat : 'decimal_dot';
						var isNumberRounding = 'undefined' !== typeof field.choices[ index ].isNumberRounding ? field.choices[ index ].isNumberRounding : 'norounding';
						var isNumberFixedPoint = 'undefined' !== typeof field.choices[ index ].isNumberFixedPoint ? field.choices[ index ].isNumberFixedPoint : false;
						var isNumberRoundingDirection = 'undefined' !== typeof field.choices[ index ].isNumberRoundingDirection ? field.choices[ index ].isNumberRoundingDirection : 'roundclosest';
						var isNumberRangeMin = 'undefined' !== typeof field.choices[ index ].isNumberRangeMin ? field.choices[ index ].isNumberRangeMin : '';
						var isNumberRangeMax = 'undefined' !== typeof field.choices[ index ].isNumberRangeMax ? field.choices[ index ].isNumberRangeMax : '';

						jQuery( '#field_columns #list_choice_number_enable_' + index ).prop( 'checked', isNumber );
						jQuery( '#field_columns #list_choice_number_format_' + index ).val( isNumberFormat );
						jQuery( '#field_columns #list_choice_number_rounding_' + index ).val( isNumberRounding );
						jQuery( '#field_columns #list_choice_number_fixed_point_' + index ).prop( 'checked', isNumberFixedPoint );
						jQuery( '#field_columns #list_choice_number_rounding_direction_' + index ).val( isNumberRoundingDirection );
						jQuery( '#field_columns #list_choice_number_range_min_' + index ).val( isNumberRangeMin );
						jQuery( '#field_columns #list_choice_number_range_max_' + index ).val( isNumberRangeMax );

						// set drop down options
						itsg_gf_list_numformat_displayed_options( index )
					}
				}
			} else {
				// show single column options
				jQuery( '.list_number_settings' ).show();

				// set values
				var isNumber = 'undefined' !== typeof field.isNumber ? field.isNumber : false;
				var isNumberFormat = 'undefined' !== typeof field.isNumberFormat ? field.isNumberFormat : 'decimal_dot';
				var isNumberRounding = 'undefined' !== typeof field.isNumberRounding ? field.isNumberRounding : 'norounding';
				var isNumberFixedPoint = 'undefined' !== typeof field.isNumberFixedPoint ? field.isNumberFixedPoint : false;
				var isNumberRoundingDirection = 'undefined' !== typeof field.isNumberRoundingDirection ? field.isNumberRoundingDirection : 'roundclosest';
				var isNumberRangeMin = 'undefined' !== typeof field.isNumberRangeMin ? field.isNumberRangeMin : '';
				var isNumberRangeMax = 'undefined' !== typeof field.isNumberRangeMax ? field.isNumberRangeMax : '';

				jQuery( '#field_settings .list_number_settings #list_number_enable' ).prop( 'checked', isNumber );
				jQuery( '#field_settings .list_number_settings #list_choice_number_format_single' ).val( isNumberFormat );
				jQuery( '#field_settings .list_number_settings #list_choice_number_rounding_single' ).val( isNumberRounding );
				jQuery( '#field_settings .list_number_settings #list_choice_number_fixed_point_single' ).prop( 'checked', isNumberFixedPoint );
				jQuery( '#field_settings .list_number_settings #list_choice_number_rounding_single_direction' ).val( isNumberRoundingDirection );
				jQuery( '#field_settings .list_number_settings #list_choice_number_range_min_single' ).val( isNumberRangeMin );
				jQuery( '#field_settings .list_number_settings #list_choice_number_range_max_single' ).val( isNumberRangeMax );

				// display options if isNumber enabled
				if ( field.isNumber ) {
					jQuery( '#list_number_options' ).show();

					// set drop down options
					itsg_gf_list_numformat_displayed_options( 'single' );
				} else {
					jQuery( '#list_number_options' ).hide();
				}
			}
		}
	}, 50);
}

// trigger for when column titles are updated
jQuery( document ).on( 'change', '#gfield_settings_columns_container #field_columns li', function() {
		itsg_gf_list_numformat_init();
});

// trigger when 'Enable multiple columns' is ticked
jQuery( document ).on('change', '#field_settings input[id=field_columns_enabled]', function() {
	itsg_gf_list_numformat_init();
});

// trigger for when field is opened
jQuery( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
		itsg_gf_list_numformat_init();
});