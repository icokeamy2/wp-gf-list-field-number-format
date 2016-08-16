function itsg_gf_list_number_format_init(){
	var number_format_fields = itsg_gf_listnumformat_js_settings.number_format_fields;
	var form_id = itsg_gf_listnumformat_js_settings.form_id;

	for ( var key in number_format_fields ) {
		// skip loop if the property is from prototype
		if ( ! number_format_fields.hasOwnProperty( key ) ) continue;

		var obj = number_format_fields[ key ];
		for ( var prop in obj ) {
			// skip loop if the property is from prototype
			if( !obj.hasOwnProperty( prop ) ) continue;

			var field_id = key;
			var field_column = prop;
			var isNumberFormat = typeof 'undefined' !== obj[ field_column ]['isNumberFormat'] ? obj[ field_column ]['isNumberFormat'] : 'decimal_dot';
			var isNumberRounding = ( typeof 'undefined' == obj[ field_column ]['isNumberRounding'] || 'norounding' == obj[ field_column ]['isNumberRounding'] ) ? -1 : parseInt( obj[ field_column ]['isNumberRounding'] );
			var isNumberRoundingDirection = typeof 'undefined' !== obj[ field_column ]['isNumberRoundingDirection'] ? obj[ field_column ]['isNumberRoundingDirection'] : 'roundclosest';
			var isNumberFixedPoint = typeof 'undefined' !== obj[ field_column ]['isNumberFixedPoint'] ? obj[ field_column ]['isNumberFixedPoint'] : false;
			var field = jQuery( '.gfield_list_' + field_id + '_cell' + field_column +' input' );

			console.log( 'list-field-number-format-for-gravity-forms :: field_id: ' + field_id + ' field_column: ' + field_column + ' isNumberFormat: ' + isNumberFormat + ' isNumberRounding: ' + isNumberRounding + ' isNumberFixedPoint: ' + isNumberFixedPoint + ' isNumberRoundingDirection: ' + isNumberRoundingDirection );

			if ( 'currency' == isNumberFormat ) {
				gformInitListCurrencyFormatFields( field, isNumberRounding, gf_global.gf_currency_config.decimal_separator, gf_global.gf_currency_config.thousand_separator, isNumberFixedPoint, isNumberRoundingDirection );
			} else {
				var decimalSeparator = '.';
				var thousandSeparator = ',';

				if ( isNumberFormat == 'decimal_comma' ){

					decimalSeparator = ',';
					thousandSeparator = '.';
				}

				gformInitListNumberFormatFields( field, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection );
			}

		}
	}
}

function gformInitListCurrencyFormatFields( fieldList, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection ){
    jQuery( fieldList ).each(function(){
		jQuery( this ).unbind( 'change' );
        jQuery( this ).change( function( event ) {
			var $this = jQuery(this);
			if ( '' != $this.val() && '0' != $this.val() ) {
				var value = $this.val();
				console.log( 'value ' + value);
				if ( -1 == isNumberRounding || isNumberRounding > 2) {
					isNumberRounding = 2;
				}
				var clean_value = itsg_clean_number( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection );
				value = gformFormatMoney( clean_value );
				value = itsg_force_rounding( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection );
				$this.val( value ).trigger('keyup');
			}
        });
	});
}

function gformInitListNumberFormatFields( fieldList, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection ) {
    jQuery( fieldList ).each(function(){
		jQuery( this ).unbind( 'change' );
        jQuery( this ).change( function() {
			var $this = jQuery(this);
			if ( '' != $this.val() && '0' != $this.val() ) {
				var value = $this.val();
				var value_decimal = ( value.split( decimalSeparator )[1] || [] ).length; // get the number of decimal places
				if ( -1 == isNumberRounding ) {
					isNumberRounding = ( value.split( decimalSeparator )[1] || [] ).length; // get the number of decimal places
				}
				var clean_value = itsg_clean_number( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection );
				value = gformFormatNumber( clean_value, value_decimal, decimalSeparator, thousandSeparator ); // rounds closest
				value = itsg_force_rounding( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection );
				$this.val( value ).trigger('keyup');
			}
		});
	});
}

function create_length_number(str, max) {
	return str.length < max + 1 ? create_length_number( str + '0', max) : str;
}

function itsg_force_rounding( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection ) {

	var decimal_places = ( value.split( decimalSeparator )[1] || [] ).length; // get the number of decimal places

	if ( isNumberFixedPoint ) {
		if( decimal_places < isNumberRounding ) {
			// if decimal separator does not exist - add it
			if ( -1 == value.indexOf( decimalSeparator ) ) {
				value += decimalSeparator;
			}

			// add '0' padding until decimal places isNumberRounding has been met
			while( decimal_places < isNumberRounding ) {
				value += '0';
				decimal_places = value.substr( value.indexOf( decimalSeparator ) + 1 ).length;
			}
		}
	}

	// if isNumberRounding is disabled get the length of the current decimal places - this ensures Gravity Forms rounds to the existing decimal place
	if ( -1 == isNumberRounding ) {
		isNumberRounding = ( value.split( decimalSeparator )[1] || [] ).length; // get the number of decimal places
	}

	return value;
}

function toFixed( num, precision ) {
    return (+(Math.round(+(num + 'e' + precision)) + 'e' + -precision)).toFixed(precision);
}

// takes currency, comma or decimal separated numbers - cleans and passes through GF format function
function itsg_clean_number( value, isNumberRounding, decimalSeparator, thousandSeparator, isNumberFixedPoint, isNumberRoundingDirection ) {
	// remove any dollar symbols
	var value = value.replace( /\$/g, '' );

	// if decimal separator is comma (e.g. 9.999,00) convert to decimal (e.g. 9,999.99) by removing any dots and replacing comma with a dot
	if ( ',' == decimalSeparator ) {
		value = value.replace( /\./g, '' ).replace( /\,/g, '.' );
	}

	// remove any commas that remain (e.g. 9,999.99 to 9999.99)
	value = value.replace( /\,/g, '' );

	if ( 'rounddown' == isNumberRoundingDirection ) {
		var length = create_length_number( '1', isNumberRounding );
		var value = ( Math.floor( value * length ) / length ).toString();
	} else if ( 'roundup' == isNumberRoundingDirection ) {
		var length = create_length_number( '1', isNumberRounding );
		var value = ( Math.ceil( value * length ) / length ).toString();
	} else if ( 'roundclosest' == isNumberRoundingDirection ) {
		var value = ( parseFloat( value ).toFixed( isNumberRounding ) ).toString();
	}

	return value;
}

if ( '1' == itsg_gf_listnumformat_js_settings.is_entry_detail ) {
	// runs the main function when the page loads -- entry editor -- configures any existing upload fields
	jQuery(document).ready( function($) {
		itsg_gf_list_number_format_init();

		// bind the datepicker function to the 'add list item' button click event
		jQuery( '.gfield_list' ).on( 'click', '.add_list_item', function(){
			itsg_gf_list_number_format_init();
		});

		// bind to post conditional logic trigger
		jQuery( document ).bind( 'gform_post_conditional_logic', function( event, formId, fields, isInit ){
			itsg_gf_list_number_format_init();
		});
	});
} else {
	// runs the main function when the page loads -- front end forms -- configures any existing upload fields
	jQuery( document ).bind( 'gform_post_render', function($) {
		itsg_gf_list_number_format_init();

		// bind the datepicker function to the 'add list item' button click event
		jQuery( '.gfield_list' ).on( 'click', '.add_list_item', function(){
			itsg_gf_list_number_format_init();
		});

		// bind to post conditional logic trigger
		jQuery( document ).bind( 'gform_post_conditional_logic', function( event, formId, fields, isInit ){
			itsg_gf_list_number_format_init();
		});
	});
}