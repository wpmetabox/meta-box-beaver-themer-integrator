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
class MBBTI_Terms extends MBBTI_Base {
	/**
	 * Settings group type.
	 * @var string
	 */
	protected $group = 'archives';

	/**
	 * Themer settings type: post, archive or site.
	 * @var string
	 */
	protected $type = 'archive';

	/**
	 * Object type: post, term or setting.
	 * @var string
	 */
	protected $object_type = 'term';

	/**
	 * Check if module is active.
	 * @return boolean
	 */
	public function is_active() {
		return function_exists( 'mb_term_meta_load' );
	}

	/**
	 * Parse settings to get field ID and object ID.
	 * @param  object $settings Themer settings.
	 * @return array            Field ID and object ID.
	 */
	public function parse_settings( $settings ) {
		return array( get_queried_object_id(), $settings->field );
	}

	/**
	 * Get list of Meta Box fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		$sources = array();
		$fields  = $this->get_all_fields();

		foreach ( $fields as $taxonomy => $list ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$options = array();
			foreach ( $list as $field ) {
				$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
			}
			$sources[ $taxonomy ] = array(
				'label'   => $taxonomy_object->labels->singular_name,
				'options' => $options,
			);
		}
		return $sources;
	}
}
