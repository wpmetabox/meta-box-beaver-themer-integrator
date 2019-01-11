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
abstract class MBBTI_Base {
	/**
	 * Settings group type.
	 *
	 * @var string
	 */
	protected $group = 'posts';

	/**
	 * Themer settings type: post, archive or site.
	 *
	 * @var string
	 */
	protected $type = 'post';

	/**
	 * Object type: post, term or setting.
	 *
	 * @var string
	 */
	protected $object_type = 'post';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'fl_page_data_add_properties', array( $this, 'add_properties' ) );
	}

	/**
	 * Add Meta Box Field to posts group.
	 */
	public function add_properties() {
		if ( ! $this->is_active() ) {
			return;
		}

		$func = "add_{$this->type}_property";
		FLPageData::$func(
			'meta_box',
			array(
				'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
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
			)
		);
		$func = "add_{$this->type}_property";
		FLPageData::$func(
			'meta_box_color',
			array(
				'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
				'group'  => $this->group,
				'type'   => array(
					'color',
				),
				'getter' => array( $this, 'get_color_field_value' ),
				'form'   => 'meta_box',
			)
		);

		$func = "add_{$this->type}_property_settings_fields";
		FLPageData::$func(
			'meta_box',
			array(
				'field'       => array(
					'type'    => 'select',
					'label'   => __( 'Field Name', 'meta-box-beaver-themer-integrator' ),
					'options' => $this->get_fields(),
					'toggle'  => $this->get_toggle_rules(),
				),
				'image_size'  => array(
					'type'  => 'photo-sizes',
					'label' => __( 'Image Size', 'meta-box-beaver-themer-integrator' ),
				),
				'date_format' => array(
					'type'        => 'text',
					'label'       => __( 'Date Format', 'meta-box-beaver-themer-integrator' ),
					'description' => __( 'Enter a <a href="http://php.net/date">PHP date format string</a>. Leave empty to use the default field format.', 'meta-box-beaver-themer-integrator' ),
				),
			)
		);
		FLPageData::$func(
			'meta_box_color',
			array(
				'field' => array(
					'type'    => 'select',
					'label'   => __( 'Field Name', 'meta-box-beaver-themer-integrator' ),
					'options' => $this->get_color_fields(),
				),
			)
		);
	}

	/**
	 * Check if module is active.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return true;
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
		list( $object_id, $field_id ) = $this->parse_settings( $settings );

		$args  = array( 'object_type' => $this->object_type );
		$field = rwmb_get_field_settings( $field_id, $args, $object_id );

		switch ( $field['type'] ) {
			case 'image':
			case 'image_advanced':
			case 'image_upload':
			case 'plupload_image':
				$value = rwmb_get_value( $field_id, $args, $object_id );
				return array_keys( $value );
			case 'single_image':
				$args['size'] = $settings->image_size;
				$value        = rwmb_get_value( $field_id, $args, $object_id );
				$value['id']  = $value['ID'];
				return $value;
			case 'date':
			case 'datetime':
				if ( ! empty( $settings->date_format ) ) {
					$args['format'] = $settings->date_format;
				}
				break;
		}

		$value = rwmb_the_value( $field_id, $args, $object_id, false );

		return $value;
	}

	/**
	 * Display Meta Box field.
	 *
	 * @param object $settings Property settings.
	 * @param string $property Property.
	 *
	 * @return mixed
	 */
	public function get_color_field_value( $settings, $property ) {
		list( $object_id, $field_id ) = $this->parse_settings( $settings );

		$args  = array( 'object_type' => $this->object_type );
		$value = rwmb_get_value( $field_id, $args, $object_id );

		return str_replace( '#', '', $value );
	}

	/**
	 * Get fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		$list = $this->get_field_list();

		return $this->format( $list );
	}

	/**
	 * Get color fields.
	 *
	 * @return array
	 */
	public function get_color_fields() {
		$list = $this->get_field_list();

		array_walk( $list, array( $this, 'filter_is_color' ) );

		return $this->format( $list );
	}

	/**
	 * Get list of fields, categorized by types.
	 *
	 * @return array
	 */
	public function get_field_list() {
		$list = rwmb_get_registry( 'field' )->get_by_object_type( $this->object_type );

		// Keep fields that have value only.
		array_walk( $list, array( $this, 'filter_has_value' ) );

		return $list;
	}

	/**
	 * Filter a list of fields, keep fields that have value only.
	 *
	 * @param array $fields Array of fields.
	 */
	public function filter_has_value( &$fields ) {
		$fields = array_filter( $fields, array( $this, 'has_value' ) );
	}

	/**
	 * Check if field has value.
	 *
	 * @param  array $field Field settings.
	 * @return boolean
	 */
	public function has_value( $field ) {
		return ! in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true );
	}

	/**
	 * Filter a list of fields, keep color fields only.
	 *
	 * @param array $fields Array of fields.
	 */
	public function filter_is_color( &$fields ) {
		$fields = array_filter( $fields, array( $this, 'is_color' ) );
	}

	/**
	 * Check if field is a color field.
	 *
	 * @param  array $field Field settings.
	 * @return boolean
	 */
	public function is_color( $field ) {
		return 'color' === $field['type'];
	}

	/**
	 * Get toggle rules for select field.
	 * Only show additional fields when field type matches.
	 *
	 * @return array
	 */
	public function get_toggle_rules() {
		$list      = $this->get_field_list();
		$field_map = array();
		foreach ( $list as $fields ) {
			foreach ( $fields as $field ) {
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
					$rules[ $id ] = $image_rules;
					break;
				case 'date':
				case 'datetime':
					$rules[ $id ] = $date_rules;
					break;
			}
		}
		return $rules;
	}
}
