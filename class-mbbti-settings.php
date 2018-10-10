<?php
/**
 * Integrates MB Settings Page with Beaver Themer.
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

/**
 * Settings class.
 */
class MBBTI_Settings {
	/**
	 * Group type.
	 *
	 * @var string
	 */
	protected $group = 'site';
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'fl_page_data_add_properties', array( $this, 'add_properties' ) );
	}

	/**
	 * Add Meta Box settings fields to site group.
	 */
	public function add_properties() {
		if ( ! function_exists( 'mb_settings_page_load' ) ) {
			return;
		}

		FLPageData::add_site_property( 'meta_box', array(
			'label'  => __( 'Meta Box Settings Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => $this->group,
			'type'   => array(
				'string',
				'html',
				'photo',
				'multiple-photos',
				'url',
				'custom_field',
			),
			'getter' => array( $this, 'get_field_value' ),
			'form'   => 'meta_box',
		) );
		FLPageData::add_site_property_settings_fields( 'meta_box', array(
			'field'      => array(
				'type'    => 'select',
				'label'   => __( 'Field Name', 'meta-box-beaver-themer-integrator' ),
				'options' => $this->get_settings_fields(),
				// 'toggle'  => $this->get_toggle_rules(),
			),
			'image_size' => array(
				'type'  => 'photo-sizes',
				'label' => __( 'Image Size', 'meta-box-beaver-themer-integrator' ),
			),
			'date_format' => array(
				'type'        => 'text',
				'label'       => __( 'Date Format', 'meta-box-beaver-themer-integrator' ),
				'description' => __( 'Enter a <a href="http://php.net/date">PHP date format string</a>. Leave empty to use the default field format.', 'meta-box-beaver-themer-integrator' ),
			),
		) );
	}

	/**
	 * Display Meta Box field.
	 *
	 * @param object $settings Property settings.
	 * @param string $property Property.
	 *
	 * @return mixed
	 */
	public function get_field_value( $settings, $property ) {
		list( $option_name, $field_id ) = explode( '#', $settings->field );

		$args  = array( 'object_type' => 'setting' );
		$field = rwmb_get_field_settings( $field_id, $args, $option_name );

		switch ( $field['type'] ) {
			case 'image':
			case 'image_advanced':
			case 'image_upload':
			case 'plupload_image':
				$value = rwmb_get_value( $field_id, $args, $option_name );
				return array_keys( $value );
			case 'single_image':
				$args['size'] = $settings->image_size;
				$value        = rwmb_get_value( $field_id, $args, $option_name );
				$value['id']  = $value['ID'];
				return $value;
			case 'date':
			case 'datetime':
				if ( ! empty( $settings->date_format ) ) {
					$args['format'] = $settings->date_format;
				}
				break;
		}

		$value = rwmb_the_value( $field_id, $args, $option_name, false );

		return $value;
	}

	/**
	 * Get list of Meta Box fields for posts.
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$sources = array();
		$fields  = $this->get_all_fields();
		foreach ( $fields as $settings_page => $list ) {
			$options = array();
			foreach ( $list as $field ) {
				$key = "{$settings_page}#{$field['id']}";
				$options[ $key ] = $field['name'] ? $field['name'] : $field['id'];
			}
			$sources[ $settings_page ] = array(
				'label'   => $settings_page,
				'options' => $options,
			);
		}

		return $sources;
	}

	/**
	 * Get toggle rules for select field.
	 * Only show additional fields when field type matches.
	 *
	 * @return array
	 */
	public function get_toggle_rules() {
		$fields  = $this->get_all_fields();
		$field_map = array();
		foreach ( $fields as $post_type => $list ) {
			foreach ( $list as $field ) {
				$field_map[ $field['id'] ] = $field['type'];
			}
		}

		$rules       = array();
		$image_rules = array( 'fields' => array( 'image_size' ) );
		$date_rules  = array( 'fields' => array( 'date_format' ) );
		foreach ( $field_map as $id => $type ) {
			switch ( $type ) {
				case 'image':
				case 'image_advanced':
				case 'image_upload':
				case 'plupload_image':
				case 'single_image':
					$rules[$id] = $image_rules;
					break;
				case 'date':
				case 'datetime':
					$rules[$id] = $date_rules;
					break;
			}
		}

		return $rules;
	}

	/**
	 * Get all fields that have values.
	 *
	 * @return array
	 */
	protected function get_all_fields() {
		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );

		// Remove fields that don't have value.
		array_walk( $fields, function ( &$list ) {
			$list = array_filter( $list, function( $field ) {
				return ! in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true );
			} );
		} );
		return $fields;
	}
}
