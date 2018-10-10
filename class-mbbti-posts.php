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
class MBBTI_Posts extends MBBTI_Base {
	/**
	 * Parse settings to get field ID and object ID.
	 *
	 * @param  object $settings Themer settings.
	 * @return array            Field ID and object ID.
	 */
	public function parse_settings( $settings ) {
		return array( get_the_ID(), $settings->field );
	}

	/**
	 * Get list of Meta Box fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		$sources = array();
		$fields  = $this->get_all_fields();

		foreach ( $fields as $post_type => $list ) {
			$post_type_object = get_post_type_object( $post_type );
			$options          = array();
			foreach ( $list as $field ) {
				$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
			}
			$sources[ $post_type ] = array(
				'label'   => $post_type_object->labels->singular_name,
				'options' => $options,
			);
		}
		return $sources;
	}

	/**
	 * Filter fields if neccessary.
	 *
	 * @param  array $fields List of fields.
	 * @return array
	 */
	public function filter_fields( $fields ) {
		// Remove fields for non-existing post types.
		return array_filter(
			$fields,
			function( $post_type ) {
				return post_type_exists( $post_type );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
