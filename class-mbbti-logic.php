<?php
/**
 * Integrates Conditional Logic in the front end.
 *
 * @package    Meta Box
 * @subpackage Meta Box Beaver Themer Integrator
 */

/**
 * Handle conditional logic settings in the front end for Beaver Themer.
 */
class MBBTI_Logic {
	/**
	 * Sets up callbacks for conditional logic rules.
	 */
	public function init() {
		BB_Logic_Rules::register(
			array(
				'metabox/archive-field'     => array( $this, 'archive_field' ),
				'metabox/post-field'        => array( $this, 'post_field' ),
				'metabox/post-author-field' => array( $this, 'post_author_field' ),
				'metabox/user-field'        => array( $this, 'user_field' ),
			)
		);
		add_action( 'bb_logic_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue scripts for conditional logic.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'bb-logic-rules-metabox',
			plugin_dir_url( __FILE__ ) . 'js/logic.js',
			array( 'bb-logic-core' ),
			BB_LOGIC_VERSION,
			true
		);
	}

	/**
	 * Process an MB rule based on the object ID of the field location such as archive, post or user.
	 *
	 * @param string $object_id Object (post, term, user) ID.
	 * @param object $rule      Conditional logic rule.
	 * @return bool
	 */
	public function evaluate_rule( $object_id = false, $rule ) {
		$value = rwmb_meta( $rule->key, '', $object_id );

		if ( is_array( $value ) ) {
			$value = empty( $value ) ? 0 : 1;
		} elseif ( is_object( $value ) ) {
			$value = 1;
		}

		return BB_Logic_Rules::evaluate_rule(
			array(
				'value'    => $value,
				'operator' => $rule->operator,
				'compare'  => $rule->compare,
				'isset'    => $value,
			)
		);
	}

	/**
	 * Archive field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function archive_field( $rule ) {
		$object = get_queried_object();

		if ( ! is_object( $object ) || ! isset( $object->taxonomy ) || ! isset( $object->term_id ) ) {
			$id = 'archive';
		} else {
			$id = $object->taxonomy . '_' . $object->term_id;
		}

		return $this->evaluate_rule( $id, $rule );
	}

	/**
	 * Post field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function post_field( $rule ) {
		global $post;
		$id = is_object( $post ) ? $post->ID : 0;
		return $this->evaluate_rule( $id, $rule );
	}

	/**
	 * Post author field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function post_author_field( $rule ) {
		global $post;
		$id = is_object( $post ) ? $post->post_author : 0;
		return $this->evaluate_rule( 'user_' . $id, $rule );
	}

	/**
	 * User field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function user_field( $rule ) {
		$user = wp_get_current_user();
		return $this->evaluate_rule( 'user_' . $user->ID, $rule );
	}
}
