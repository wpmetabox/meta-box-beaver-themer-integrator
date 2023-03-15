<?php
/**
 * Integrates Meta Box custom fields with Beaver Themer.
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

namespace MBBTI;

use FLPageData;

/**
 * The plugin main class.
 */
abstract class Base {
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
	 * List of fields.
	 *
	 * @var array
	 */
	private $field_list;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'fl_page_data_add_properties', [ $this, 'add_properties' ] );
	}

	/**
	 * Add Meta Box Field to posts group.
	 */
	public function add_properties() {
		if ( ! $this->is_active() ) {
			return;
		}

		$func = "add_{$this->type}_property";
		FLPageData::$func( 'meta_box', [
			'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => $this->group,
			'type'   => [
				'string',
				'html',
				'url',
				'custom_field',
				'color',
			],
			'getter' => [ $this, 'get_field_value' ],
			'form'   => 'meta_box',
		] );

		FLPageData::$func( 'meta_box_photo', [
			'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => $this->group,
			'type'   => 'photo',
			'getter' => [ $this, 'get_photo_value' ],
			'form'   => 'meta_box',
		] );

		FLPageData::$func( 'meta_box_gallery', [
			'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => $this->group,
			'type'   => 'multiple-photos',
			'getter' => [ $this, 'get_multiple_photos_value' ],
			'form'   => 'meta_box',
		] );

		$func   = "add_{$this->type}_property_settings_fields";
		$fields = [
			'field' => [
				'type'    => 'select',
				'label'   => __( 'Field Name', 'meta-box-beaver-themer-integrator' ),
				'options' => $this->get_fields(),
				'toggle'  => $this->get_toggle_rules(),
			],
		];
		if ( $this->has_image_field() ) {
			$fields['image_size'] = [
				'type'  => 'photo-sizes',
				'label' => __( 'Image Size', 'meta-box-beaver-themer-integrator' ),
			];
			$fields['display']    = [
				'type'    => 'hidden',
				'default' => 'url',
			];
		}
		if ( $this->has_date_field() ) {
			$fields['date_format'] = [
				'type'        => 'text',
				'label'       => __( 'Date Format', 'meta-box-beaver-themer-integrator' ),
				'description' => __( 'Enter a <a href="http://php.net/date">PHP date format string</a>. Leave empty to use the default field format.', 'meta-box-beaver-themer-integrator' ),
			];
		}
		FLPageData::$func( 'meta_box', $fields );
		FLPageData::$func( 'meta_box_photo', $fields );
		FLPageData::$func( 'meta_box_gallery', $fields );
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

		$args  = [ 'object_type' => $this->object_type ];
		$field = rwmb_get_field_settings( $field_id, $args, $object_id );

		if ( ! $field ) {
			return;
		}

		switch ( $field['type'] ) {
			case 'color':
				$value = rwmb_get_value( $field_id, $args, $object_id );
				return str_replace( '#', '', $value );
			case 'image':
			case 'image_advanced':
			case 'image_upload':
			case 'plupload_image':
				return rwmb_the_value( $field_id, $args, $object_id );
			case 'single_image':
				$args['size'] = $settings->image_size;
				$value        = rwmb_get_value( $field_id, $args, $object_id );
				$display      = $settings->display;
				if ( $display === 'tag' ) {
					return wp_get_attachment_image( $value['ID'], $settings->image_size );
				}
				if ( $display === 'url' ) {
					return $value['url'] ?? '';
				}
				if ( $display === 'title' ) {
					return $value['title'] ?? '';
				}
				if ( $display === 'caption' ) {
					return $value['caption'] ?? '';
				}
				if ( $display === 'description' ) {
					return $value['title'] ?? '';
				}
				if ( $display === 'alt' ) {
					return $value['alt'] ?? '';
				}

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

	public function get_photo_value( $settings, $property ) {
		list( $object_id, $field_id ) = $this->parse_settings( $settings );

		$args  = [ 'object_type' => $this->object_type ];
		$field = rwmb_get_field_settings( $field_id, $args, $object_id );

		if ( ! $field ) {
			return;
		}

		switch ( $field['type'] ) {
			case 'image':
			case 'image_advanced':
			case 'image_upload':
			case 'plupload_image':
				$value = rwmb_get_value( $field_id, $args, $object_id );
				$id    = array_key_first( $value );
				return [
					'id'  => $id,
					'url' => $value[ $id ]['url'] ?? '',
				];
			case 'single_image':
				$args['size'] = $settings->image_size;
				$value        = rwmb_get_value( $field_id, $args, $object_id );
				return [
					'id'  => $value['ID'] ?? '',
					'url' => $value['url'] ?? '',
				];
		}

		$value = rwmb_the_value( $field_id, $args, $object_id, false );

		return $value;
	}

	public function get_multiple_photos_value( $settings, $property ) {
		list( $object_id, $field_id ) = $this->parse_settings( $settings );

		$args  = [ 'object_type' => $this->object_type ];
		$field = rwmb_get_field_settings( $field_id, $args, $object_id );

		if ( ! $field ) {
			return;
		}

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
				return empty( $value['ID'] ) ? [] : [ $value['ID'] ];
		}

		$value = rwmb_the_value( $field_id, $args, $object_id, false );

		return $value;
	}

	protected function get_fields() {
		$list = $this->get_field_list();
		return $this->format( $list );
	}

	/**
	 * Get list of fields, categorized by types.
	 *
	 * @return array
	 */
	private function get_field_list() {
		if ( ! empty( $this->field_list ) ) {
			return $this->field_list;
		}

		$list = rwmb_get_registry( 'field' )->get_by_object_type( $this->object_type );

		// Keep fields that have value only.
		foreach ( $list as &$fields ) {
			$fields = array_filter( $fields, [ $this, 'has_value' ] );
		}

		$this->field_list = $list;

		return $list;
	}

	/**
	 * Check if a field has value.
	 *
	 * @param  array $field Field settings.
	 * @return bool
	 */
	private function has_value( $field ) {
		return ! in_array( $field['type'], [ 'heading', 'divider', 'custom_html', 'button' ], true );
	}

	/**
	 * Get toggle rules for select field.
	 * Only show additional fields when field type matches.
	 *
	 * @return array
	 */
	protected function get_toggle_rules() {
		$list      = $this->get_field_list();
		$field_map = [];
		foreach ( $list as $object => $fields ) {
			foreach ( $fields as $field ) {
				$key               = 'setting' === $this->object_type ? "{$object}#{$field['id']}" : $field['id'];
				$field_map[ $key ] = $field['type'];
			}
		}

		$rules       = [];
		$image_rules = [ 'fields' => [ 'image_size', 'display' ] ];
		$date_rules  = [ 'fields' => [ 'date_format' ] ];
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

	/**
	 * Check if the field list has an image field.
	 *
	 * @return bool
	 */
	protected function has_image_field() {
		$types = [ 'image', 'image_advanced', 'image_upload', 'plupload_image', 'single_image' ];
		$list  = $this->get_field_list();
		foreach ( $list as $type => $fields ) {
			foreach ( $fields as $field ) {
				if ( in_array( $field['type'], $types, true ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if the field list has a date time field.
	 *
	 * @return bool
	 */
	protected function has_date_field() {
		$types = [ 'date', 'datetime' ];
		$list  = $this->get_field_list();
		foreach ( $list as $type => $fields ) {
			foreach ( $fields as $field ) {
				if ( in_array( $field['type'], $types, true ) ) {
					return true;
				}
			}
		}
		return false;
	}
}
