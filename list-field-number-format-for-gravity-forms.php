<?php
/*
Plugin Name: List Field Number Format for Gravity Forms
Description: Turn your list field columns into repeatable number fields
Version: 1.0.1
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com
License: GPL2
Text Domain: list-field-number-format-for-gravity-forms

------------------------------------------------------------------------
Copyright 2016 Adrian Gordon

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

//------------------------------------------

if ( class_exists( 'GFForms' ) ) {
    GFForms::include_addon_framework();

    class ITSG_GF_ListField_Number_Format extends GFAddOn {

        protected $_version = '1.0.1';
        protected $_min_gravityforms_version = '1.9.9.8';
        protected $_slug = 'list-field-number-format-for-gravity-forms';
        protected $_full_path = __FILE__;
        protected $_title = 'List Field Number Format for Gravity Forms';
        protected $_short_title = 'List Field Number Format';

        public function init() {
			parent::init();

			require_once( GFCommon::get_base_path() . '/currency.php' );

			add_action( 'gform_field_standard_settings', array( $this, 'field_ajaxupload_settings' ) , 10, 2 );
			add_filter( 'gform_tooltips', array( $this, 'field_ajaxupload_tooltip' ) );
			add_filter( 'gform_column_input_content', array( &$this, 'change_column_content' ), 10, 6 );
			add_filter( 'gform_validation', array( &$this, 'validate_columns' ) );
        }

		public function scripts() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$scripts = array(
				array(
					'handle'    => 'itsg_gf_listnumformat_js',
					'src'       => $this->get_base_url() . "/js/itsg_gf_listnumformat_js{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery' ),
					'enqueue'   => array( array( $this, 'form_has_list_number_field' ) ),
					'in_footer' => true,
					'callback'  => array( $this, 'localize_scripts' ),
				),
				array(
					'handle'    => 'itsg_gf_listnumformat_admin_js',
					'src'       => $this->get_base_url() . "/js/itsg_gf_listnumformat_admin_js{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery' ),
					'enqueue'   => array( array( $this, 'requires_admin_js' ) ),
					'in_footer' => true,
					'callback'  => array( $this, 'localize_scripts_admin' ),
				)
			);

			 return array_merge( parent::scripts(), $scripts );
		} // END scripts

		function requires_admin_js() {
			return GFCommon::is_form_editor();
		}

		public function localize_scripts( $form, $is_ajax ) {
			// Localize the script with data
			$default_isNumberFormat = apply_filters( 'itsg_gf_listfield_default_number_format', '' );
			$default_isNumberRounding = apply_filters( 'itsg_gf_listfield_default_number_rounding', '' );
			$is_entry_detail = GFCommon::is_entry_detail();
			$number_format_fields = array();

			if ( is_array( $form['fields'] ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field->type ) {
						$form_id = $form['id'];
						$field_id = $field['id'];
						$has_columns = is_array( $field->choices );
						if ( $has_columns ) {
							foreach( $field['choices'] as $key=>$choice ){
								if ( true  == rgar( $choice, 'isNumber' ) )  {
									$column_number = $key + 1;

									$isNumberFormat = strlen( $default_isNumberFormat) > 0 ? $default_isNumberFormat : rgar( $choice, 'isNumberFormat' );
									$number_format_fields[ $field_id ][ $column_number ]['isNumberFormat'] = $isNumberFormat;

									$isNumberRounding = strlen( $default_isNumberRounding ) > 0 ? $default_isNumberRounding : rgar( $choice, 'isNumberRounding' );
									$number_format_fields[ $field_id ][ $column_number ]['isNumberRounding'] = $isNumberRounding;

									$isNumberRoundingDirection = strlen( rgar( $choice, 'isNumberRoundingDirection' ) ) > 0 ? rgar( $choice, 'isNumberRoundingDirection' ) : 'roundclosest';
									$number_format_fields[ $field_id ][ $column_number ]['isNumberRoundingDirection'] = $isNumberRoundingDirection;

									$isNumberFixedPoint = strlen( rgar( $choice, 'isNumberFixedPoint' ) ) > 0 ? $field->isNumberFixedPoint : 'false';
									$number_format_fields[ $field_id ][ $column_number ]['isNumberFixedPoint'] = rgar( $choice, 'isNumberFixedPoint' );
								}
							}
						} elseif ( true == $field->isNumber ) {
							$column_number = 1;

							$isNumberFormat = strlen( $default_isNumberFormat ) > 0 ? $default_isNumberFormat : $field->isNumberFormat;
							$number_format_fields[ $field_id ][ $column_number ]['isNumberFormat'] = $isNumberFormat;

							$isNumberRounding = strlen( $default_isNumberRounding ) > 0 ? $default_isNumberRounding : $field->isNumberRounding;
							$number_format_fields[ $field_id ][ $column_number ]['isNumberRounding'] = $isNumberRounding;

							$isNumberRoundingDirection = strlen( $field->isNumberRoundingDirection ) > 0 ? $field->isNumberRoundingDirection : 'roundclosest';
							$number_format_fields[ $field_id ][ $column_number ]['isNumberRoundingDirection'] = $isNumberRoundingDirection;

							$isNumberFixedPoint = strlen( $field->isNumberFixedPoint ) > 0 ? $field->isNumberFixedPoint : 'false';
							$number_format_fields[ $field_id ][ $column_number ]['isNumberFixedPoint'] = $isNumberFixedPoint;
						}
					}
				}
			}

			$settings_array = array(
				'form_id' => $form['id'],
				'is_entry_detail' => $is_entry_detail ? $is_entry_detail : 0,
				'number_format_fields' => $number_format_fields,
			);

			wp_localize_script( 'itsg_gf_listnumformat_js', 'itsg_gf_listnumformat_js_settings', $settings_array );

			?><script><?php
			GFCommon::gf_global();
			GFCommon::gf_vars();
			?></script><?php

		} // END localize_scripts

		public function localize_scripts_admin( $form, $is_ajax ) {
			$settings_array = array(
				'text_number_format' => esc_js( __( 'Number Format', 'list-field-number-format-for-gravity-forms' ) ),
				'text_enable_number_format' => esc_js( __( 'Enable Number Format', 'list-field-number-format-for-gravity-forms' ) ),
				'text_currency' => esc_js( __( 'Currency', 'list-field-number-format-for-gravity-forms' ) ),
				'text_rounding_direction' => esc_js( __( 'Rounding Direction', 'list-field-number-format-for-gravity-forms' ) ),
				'text_do_not_round' => esc_js( __( 'Do not round', 'list-field-number-format-for-gravity-forms' ) ),
				'text_rounding' => esc_js( __( 'Rounding', 'list-field-number-format-for-gravity-forms' ) ),
				'text_range' => esc_js( __( 'Range', 'list-field-number-format-for-gravity-forms' ) ),
				'text_min' => esc_js( __( 'Min', 'list-field-number-format-for-gravity-forms' ) ),
				'text_max' => esc_js( __( 'Max', 'list-field-number-format-for-gravity-forms' ) ),
				'text_format_currency' => esc_js( $this->get_number_format_text( 'currency' ) ),
				'text_format_decimal_dot' => esc_js( $this->get_number_format_text( 'decimal_dot' ) ),
				'text_format_decimal_comma' => esc_js( $this->get_number_format_text( 'decimal_comma' ) ),
				'text_fixed_point' => esc_js( __( 'Fixed point notation', 'list-field-number-format-for-gravity-forms' ) ),
			);

			wp_localize_script( 'itsg_gf_listnumformat_admin_js', 'itsg_gf_listnumformat_admin_js_settings', $settings_array );
		} // END localize_scripts_admin

		/*
          * Adds custom sortable setting for field
          */
        function field_ajaxupload_settings( $position, $form_id ) {
            // Create settings on position 50 (top position)
            if ( 50 == $position ) {
				?>
				<li class='list_number_settings field_setting'>
					<label class="section_label"><?php _e( 'Number Format', 'list-field-number-format-for-gravity-forms' ); ?></label>
					<input type='checkbox' id='list_number_enable' onclick='SetFieldProperty( "isNumber", this.checked );itsg_gf_list_numformat_init();'>
					<label class='inline' for='list_number_enable'>
					<?php _e( 'Enable Number Format', 'list-field-number-format-for-gravity-forms' ); ?>
					<?php gform_tooltip( 'list_number_enable' );?>
					</label>
					<div id="list_number_options" style="display:none; background: rgb(244, 244, 244) none repeat scroll 0px 0px; padding: 10px; border-bottom: 1px solid grey; margin-top: 10px;" >
						<div style="clear: both;">
						<label for="list_choice_number_format_single" class="section_label">
						<?php _e( 'Number Format', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<select style="margin-bottom: 10px;" onchange="SetFieldProperty( 'isNumberFormat', this.value);itsg_gf_list_numformat_displayed_options( 'single' )" id="list_choice_number_format_single" >
						<option value="decimal_dot">9,999.99</option>
						<option value="decimal_comma">9.999,99</option>
						<option value="currency"><?php _e( 'Currency', 'list-field-number-format-for-gravity-forms' ); ?></option>
						</select>
						<div style="clear: both;">
						<label for="list_choice_number_rounding_single" class="section_label">
						<?php _e( 'Rounding', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<select style="margin-bottom: 10px;" onchange="SetFieldProperty( 'isNumberRounding', this.value);" id="list_choice_number_rounding_single" >
						<option value="norounding"><?php _e( 'Do not round', 'list-field-number-format-for-gravity-forms' ); ?></option>
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						</select>
						<br>
						<input type="checkbox" id="list_choice_number_fixed_point_single" onclick="SetFieldProperty( 'isNumberFixedPoint', this.checked );" >
						<label for="list_choice_number_fixed_point_single" class="inline">
						<?php _e( 'Fixed point notation', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						<br><br>
						<div style="clear: both;">
						<label for="list_choice_number_rounding_single_direction" class="section_label">
						<?php _e( 'Rounding Direction', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<select style="margin-bottom: 10px;" onchange="SetFieldProperty( 'isNumberRoundingDirection', this.value);" id="list_choice_number_rounding_single_direction" >
						<option value="roundclosest"><?php _e( 'Round closest', 'list-field-number-format-for-gravity-forms' ); ?></option>
						<option value="roundup"><?php _e( 'Round up', 'list-field-number-format-for-gravity-forms' ); ?></option>
						<option value="rounddown"><?php _e( 'Round down', 'list-field-number-format-for-gravity-forms' ); ?></option>
						</select>
						<br>
						<div style="clear: both;">
						<label class="section_label">
						<?php _e( 'Round', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<div class="range_min">
						<input type="text" onchange="SetFieldProperty( 'isNumberRangeMin', this.value);" id="list_choice_number_range_min_single" >
						<label for="list_choice_number_range_min_single">
						<?php _e( 'Min', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<div class="range_max">
						<input type="text" onchange="SetFieldProperty( 'isNumberRangeMax', this.value);" id="list_choice_number_range_max_single" >
						<label for="list_choice_number_range_max_single">
						<?php _e( 'Max', 'list-field-number-format-for-gravity-forms' ); ?>
						</label>
						</div>
						<br>
					</div>
				</li>
			<?php
            }
        } // END field_ajaxupload_settings

		/*
         * Tooltip
         */
		function field_ajaxupload_tooltip( $tooltips ) {
			$tooltips['list_number_enable'] = "<h6>". __( 'Number Format', 'list-field-number-format-for-gravity-forms' )."</h6>". __( 'Select this option to set column as a number format field.', 'list-field-number-format-for-gravity-forms' );
			return $tooltips;
		} // END field_ajaxupload_tooltip

		/*
         * Changes column field
         */
		function change_column_content( $input, $input_info, $field, $text, $value, $form_id ) {
			if ( GFCommon::is_form_editor() ) {
				$has_columns = is_array( $field['choices'] );
				if ( $has_columns ) {
					foreach( $field['choices'] as $choice ) {
						if ( $text == rgar( $choice, 'text' ) && true == rgar( $choice, 'isNumber' ) ) {
							$number_format = rgar( $choice, 'isNumberFormat' );
							$number_format_text = $this->get_number_format_text( $number_format );
							$new_input = str_replace( "value='' ", "value='{$number_format_text}' ", $input );
							return $new_input;
						}
					}
				} else {
					if ( true == $field->isNumber ) {
						$number_format = $field->isNumberFormat;
						$number_format_text = $this->get_number_format_text( $number_format );
						$new_input = str_replace( "value='' ", "value='{$number_format_text}' ", $input );
						return $new_input;
					}
				}
			} else {
				$field_id = $field['id'];
				$has_columns = is_array( $field['choices'] );
				if ( $has_columns ) {
					$number_of_columns = sizeof( $field['choices'] );
					foreach( $field['choices'] as $choice ) {
						$min = rgar( $choice, 'isNumberRangeMin' );
						$max = rgar( $choice, 'isNumberRangeMax' );
						if ( $text == rgar( $choice, 'text' )  && true == rgar( $choice, 'isNumber' ) && ( is_numeric( $min ) || is_numeric( $max ) ) ) {
							$message = $this->get_range_message( $min, $max);
							$new_input =  $input . '<div class="instruction ">' . $message . "</div>";
							return $new_input;
						}
					}
				} else {
					if ( true == $field->isNumber ) {
						$min = $field->isNumberRangeMin;
						$max = $field->isNumberRangeMax;
						if ( is_numeric( $min ) || is_numeric( $max ) ) {
							$message = $this->get_range_message( $min, $max);
							$new_input =  $input . '<div class="instruction ">' . $message . "</div>";
							return $new_input;
						}
					}
				}
			}
			return $input;
		} // change_column_content

		/*
		 * Handles custom validation for range
		 */
		function validate_columns( $validation_result ) {
			$form = $validation_result['form'];
			if ( self::form_has_list_number_field( $form ) ) {
				$current_page = rgpost( 'gform_source_page_number_' . $form['id'] ) ? rgpost( 'gform_source_page_number_' . $form['id'] ) : 1;
				foreach( $form['fields'] as &$field )  {
					$field_page = $field->pageNumber;
					$is_hidden = RGFormsModel::is_field_hidden( $form, $field, array() );
					if ( $field_page != $current_page || $is_hidden ) {
						continue;
					}
					$has_columns = is_array( $field['choices'] );
					if ( $has_columns ) {
						$number_of_columns = sizeof( $field['choices'] );
						$column_number = 0;
						$value = rgpost( "input_{$field['id']}" );
						if ( is_array( $value ) ) {
							foreach( $value as $column_value ) {
								$choice = $field['choices'][ $column_number ];
								if ( true  == rgar( $choice, 'isNumber' ) )  {
									$value = $column_value;
									$number_format = rgar( $choice, 'isNumberFormat' );
									$min = rgar( $choice, 'isNumberRangeMin' );
									$max = rgar( $choice, 'isNumberRangeMax' );
									if ( ! empty( $value ) ) {

										// check number is in correct format
										if ( ! $this->is_valid_format_column_value( $value, $number_format ) ) {
											$validation_result['is_valid'] = false; // set the form validation to false
											$field->failed_validation = true;

											$number_format_text = $this->get_number_format_text( $number_format );

											$message = sprintf( esc_html__( "The column '%s' requires a value in %s format.", 'list-field-number-format-for-gravity-forms' ), $choice['text'], $number_format_text );
											$field->validation_message = $message;
											break;
										}

										// check number is in correct range
										if ( ! $this->is_valid_number( $value, $number_format, $min, $max ) ) {
											$validation_result['is_valid'] = false; // set the form validation to false
											$field->failed_validation = true;
											$message = $this->get_column_validation_range_message( $choice['text'], $min, $max );
											$field->validation_message = $message;
										}
									}
								}
								if ( $column_number >= ( $number_of_columns - 1 ) ) {
									$column_number = 0; // reset column number
								} else {
									$column_number = $column_number + 1; // increment column number
								}
							}
						}
					} elseif ( true == $field->isNumber ) {
						$value = rgpost( "input_{$field['id']}" );
						if ( is_array( $value ) ) {
							foreach( $value as $key => $column_value ) {
								$value = $column_value;
								if ( ! empty( $value ) ) {
									$number_format = $field->isNumberFormat;
									$min = $field->isNumberRangeMin;
									$max = $field->isNumberRangeMax;
									// check number is in correct format
									if ( ! $this->is_valid_format_column_value( $value, $number_format ) ) {
										$validation_result['is_valid'] = false; // set the form validation to false
										$field->failed_validation = true;

										$number_format_text = $this->get_number_format_text( $number_format );

										$message = sprintf( esc_html__( "Requires a value in %s format.", 'list-field-number-format-for-gravity-forms' ), $number_format_text );
										$field->validation_message = $message;
										break;
									}

									// check number is in correct range
									if ( ! $this->is_valid_number( $value, $number_format, $min, $max ) ) {
										$validation_result['is_valid'] = false; // set the form validation to false
										$field->failed_validation = true;
										$message = $this->get_validation_range_message( $min, $max );
										$field->validation_message = $message;
									}
								}
							}
						}
					}
				}
			}
			//Assign modified $form object back to the validation result
			$validation_result['form'] = $form;
			return $validation_result;
		} // END validate_columns

		// check is value provided is value, compared to number format and min/max range - used in validation
		function is_valid_number( $value, $number_format, $min, $max ) {

			$value = $this->get_clean_column_value( $value, $number_format );

			$value = GFCommon::maybe_add_leading_zero( $value );

			if ( ! GFCommon::is_numeric( $value, 'decimal_dot' ) ) {
				return false;
			}

			if ( ( is_numeric( $min ) && $value < $min ) ||
				 ( is_numeric( $max ) && $value > $max ) ) {
				return false;
			} else {
				return true;
			}
		} // END is_valid_number

		// get string representation of number format
		function get_number_format_text( $number_format ) {
			$number_format_text = '';

			switch ( $number_format ) {
				case 'decimal_comma' :
					$number_format_text = '9.999,99';
					break;
				case 'currency' :
					$number = '9999.99';
					$currency = new RGCurrency( GFCommon::get_currency() );
					$number_format_text = $currency->to_money( $number );
					break;
				default :
				// case 'decimal_dot' :
					$number_format_text = '9,999.99';
					break;
				}
				return $number_format_text;
		} // END get_number_format_text

		// get range message - used below each input if a min and/or max has been specified for the column
		function get_range_message( $min, $max ) {
			$message = '';

			if ( is_numeric( $min ) && is_numeric( $max ) ) {
				$message = sprintf( esc_html__( 'Please enter a value between %s and %s.', 'list-field-number-format-for-gravity-forms' ), "<strong>$min</strong>", "<strong>$max</strong>" );
			} elseif ( is_numeric( $min ) ) {
				$message = sprintf( esc_html__( 'Please enter a value greater than or equal to %s.', 'list-field-number-format-for-gravity-forms' ), "<strong>$min</strong>" );
			} elseif ( is_numeric( $max ) ) {
				$message = sprintf( esc_html__( 'Please enter a value less than or equal to %s.', 'list-field-number-format-for-gravity-forms' ), "<strong>$max</strong>" );
			}

			return $message;
		} // END get_range_message

		// create validation message for single-column list field
		function get_validation_range_message( $min, $max ) {
			$message = '';

			if ( is_numeric( $min ) && is_numeric( $max ) ) {
				$message = sprintf( esc_html__( "Requires a value between %s and %s.", 'list-field-number-format-for-gravity-forms' ), "<strong>{$min}</strong>", "<strong>{$max}</strong>" );
			} elseif ( is_numeric( $min ) ) {
				$message = sprintf( esc_html__( "Requires a value greater than or to %s.", 'list-field-number-format-for-gravity-forms' ), "<strong>{$min}</strong>" );
			} elseif ( is_numeric( $max ) ) {
				$message = sprintf( esc_html__( "Requires a value less than or to %s.", 'list-field-number-format-for-gravity-forms' ), "<strong>{$max}</strong>" );
			} elseif ( $this->failed_validation ) {
				$message = esc_html__( 'Please enter a valid number', 'list-field-number-format-for-gravity-forms' );
			}

			return $message;
		} // END get_validation_range_message

		// create validation message for multi-column list field
		function get_column_validation_range_message( $column_title, $min, $max ) {
			$message = '';

			if ( is_numeric( $min ) && is_numeric( $max ) ) {
				$message = sprintf( esc_html__( "The column '%s' requires a value between %s and %s.", 'list-field-number-format-for-gravity-forms' ), $column_title, "<strong>{$min}</strong>", "<strong>{$max}</strong>" );
			} elseif ( is_numeric( $min ) ) {
				$message = sprintf( esc_html__( "The column '%s' requires a value greater than or to %s.", 'list-field-number-format-for-gravity-forms' ), $column_title, "<strong>{$min}</strong>" );
			} elseif ( is_numeric( $max ) ) {
				$message = sprintf( esc_html__( "The column '%s' requires a value less than or to %s.", 'list-field-number-format-for-gravity-forms' ), $column_title, "<strong>{$max}</strong>" );
			} elseif ( $this->failed_validation ) {
				$message = esc_html__( 'Please enter a valid number', 'list-field-number-format-for-gravity-forms' );
			}

			return $message;
		} // END get_column_validation_range_message

		// converts formatted number to standard number
		function get_clean_column_value( $value, $number_format ) {
			$value = trim( $value );
			if ( 'currency' == $number_format ) {
				$currency = new RGCurrency( GFCommon::get_currency() );
				$value    = $currency->to_number( $value );
			} else {
				$value = GFCommon::clean_number( $value, $number_format );
			}

			return $value;
		} // END get_clean_column_value

		// check that number provided is in the correct format - used in validation
		function is_valid_format_column_value( $value, $number_format ) {
			$value = GFCommon::maybe_add_leading_zero( $value );

			$requires_valid_number = ! rgblank( $value );

			$is_valid_number = GFCommon::is_numeric( $value, $number_format );

			if ( $requires_valid_number && ! $is_valid_number ) {
				return false;
			} else {
				return true;
			}
		} // END is_valid_format_column_value

		/*
         * Check if list field has a number format field
         */
		public static function form_has_list_number_field( $form ) {
			if ( ! GFCommon::is_form_editor() && is_array( $form['fields'] ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field->type ) {
						$has_columns = is_array( $field->choices );
						if ( $has_columns ) {
							foreach( $field['choices'] as $choice ) {
								if ( true  == rgar( $choice, 'isNumber' ) )  {
									return true;
								}
							}
						} else if ( true == $field->isNumber ) {
							return true;
						}
					}
				}
			}
			return false;
		} // END form_has_list_number_field
    }
    new ITSG_GF_ListField_Number_Format();
}