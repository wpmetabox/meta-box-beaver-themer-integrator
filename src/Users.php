<?php
namespace MBBTI;

use FLPageData;

class Users extends Base {
	protected $group = 'user';
	protected $type = 'post';
	protected $object_type = 'user';

	public function add_properties() {
		if ( ! $this->is_active() ) {
			return;
		}

		$func = "add_{$this->type}_property";
		FLPageData::$func( 'meta_box_post_user', [
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

		FLPageData::$func( 'meta_box_photo_post_user', [
			'label'  => __( 'Meta Box Field', 'meta-box-beaver-themer-integrator' ),
			'group'  => $this->group,
			'type'   => 'photo',
			'getter' => [ $this, 'get_photo_value' ],
			'form'   => 'meta_box',
		] );

		FLPageData::$func( 'meta_box_gallery_post_user', [
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
		if ( $this->has_taxonomy_field() ) {
			$fields['display_term'] = [
				'type'    => 'select',
				'label'   => __( 'Field Type', 'meta-box-beaver-themer-integrator' ),
				'default' => 'tag',
				'options' => [
					'ID'   => __( 'ID', 'meta-box-beaver-themer-integrator' ),
					'name' => __( 'Name', 'meta-box-beaver-themer-integrator' ),
					'url'  => __( 'URL', 'meta-box-beaver-themer-integrator' ),
					'tag'  => __( 'Tag', 'meta-box-beaver-themer-integrator' ),
				],
			];
		}
		$fields['user'] = [
			'type'    => 'select',
			'label'   => __( 'User', 'meta-box-beaver-themer-integrator' ),
			'options' => [
				'current' => __( 'Current User', 'meta-box-beaver-themer-integrator' ),
				'specific' => __( 'Specific User', 'meta-box-beaver-themer-integrator' ),
			],
			'toggle' => [
				'specific' => ['fields' => ['user_id']],
			],
		];
		$fields['user_id'] = [
			'type'  => 'text',
			'label' => __( 'User ID', 'meta-box-beaver-themer-integrator' ),
		];
		FLPageData::$func( 'meta_box_post_user', $fields );
		FLPageData::$func( 'meta_box_photo_post_user', $fields );
		FLPageData::$func( 'meta_box_gallery_post_user', $fields );
	}

	public function is_active() {
		return function_exists( 'mb_user_meta_load' );
	}

	/**
	 * Parse settings to get field ID and object ID.
	 *
	 * @param  object $settings Themer settings.
	 * @return array            Field ID and object ID.
	 */
	public function parse_settings( $settings ) {
		$user_id = get_current_user_id();
		if ( 'specific' === $settings->user ) {
			$user_id = $settings->user_id;
		}
		return [ $user_id, $settings->field ];
	}

	public function format( $list ) {
		$sources = [];

		if ( empty( $list ) ) {
			return $sources;
		}

		$fields = $list['user'];
		foreach ( $fields as $field ) {
			$sources[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
		}

		return $sources;
	}
}
