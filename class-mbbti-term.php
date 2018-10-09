<?php
/**
 * Integrates Meta Box custom fields with Beaver Themer.
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

/**
 * The plugin main class.
 */
class MBBTI_Term {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'fl_page_data_add_properties', array( $this, 'add_to_posts' ) );
	}

	/**
	 * Add Meta Box Field to posts group.
	 */
	public function add_to_posts() {
		FLPageData::add_post_property( 'meta_box', array(
			'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => 'posts',
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

		/*
		 * Archive Term Meta
		 */
		FLPageData::add_archive_property( 'meta_box_term_meta', array(
			'label'       => __( 'Meta Box Field Term Meta', 'meta-box-beaver-themer-integrator' ),
			'group'       => 'archives',
			'type'        => array(
				'string',
				'html',
				'photo',
				'multiple-photos',
				'url',
				'custom_field',
			),
			'getter'      => array( $this, 'get_field__term_value' ),
		) );

		FLPageData::add_archive_property_settings_fields( 'meta_box_term_meta', array(
			'field'      => array(
				'type'    => 'select',
				'label'   => __( 'Field Name', 'meta-box-beaver-themer-integrator' ),
				'options' => $this->get_post_fields_term(),
				'toggle'  => $this->get_toggle_rules(),
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
	public function get_field__term_value( $settings, $property ) {
		$field_id      = $settings->field;
		$term_id 	   = get_queried_object()->term_id;
		$term_taxonomy = get_queried_object()->taxonomy;
		$fields_obj    = rwmb_get_registry( 'field' )->get_by_object_type( 'term' );
		$fields_type   = ! empty( $fields_obj[$term_taxonomy][$field_id]['type'] ) ? $fields_obj[$term_taxonomy][$field_id]['type'] : '';
		$args     = array();
		switch ( $fields_type ) {
			case 'image':
			case 'image_advanced':
			case 'image_upload':
			case 'plupload_image':
				$value = rwmb_get_value( $field_id );
				return array_keys( $value );
			case 'single_image':
				$args['size'] = $settings->image_size;
				$value        = rwmb_get_value( $field_id, $args );
				$value['id']  = $value['ID'];
				return $args;
			case 'date':
			case 'datetime':
				if ( ! empty( $settings->date_format ) ) {
					$args['format'] = $settings->date_format;
				}
				break;
		}

		$value = rwmb_meta( $field_id, array( 'object_type' => 'term' ), $term_id );

		return $value;
	}

	/**
	 * Get list of Meta Box fields for term.
	 *
	 * @return array
	 */
	public function get_post_fields_term() {
		$sources = array();
		$fields  = $this->get_all_fields_term();

		foreach ( $fields as $term => $list ) {
			$options = array();
			foreach ( $list as $field ) {
				$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
			}
			$sources[ $term ] = array(
				'label'   => $term,
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
		$fields  = $this->get_all_fields_term();
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
	 * Get all valuable fields in the term.
	 *
	 * @return array
	 */
	protected function get_all_fields_term() {
		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'term' );

		// Remove fields that don't have value.
		array_walk( $fields, function ( &$list ) {
			$list = array_filter( $list, function( $field ) {
				return ! in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true );
			} );
		} );
		return $fields;
	}

}
