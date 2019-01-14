<?php
/**
 * Server side processing for Meta Box rules.
 *
 * @since 0.1
 */
final class MB_Logic_Rules {

	/**
	 * Sets up callbacks for conditional logic rules.
	 *
	 * @since  0.1
	 * @return void
	 */
	static public function init() {
		BB_Logic_Rules::register( array(
			'metabox/archive-field'     => __CLASS__ . '::archive_field',
			'metabox/post-field'        => __CLASS__ . '::post_field',
			'metabox/post-author-field' => __CLASS__ . '::post_author_field',
			'metabox/user-field'        => __CLASS__ . '::user_field',
		) );
		add_action( 'bb_logic_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

    static public function enqueue( $rules = null ) {
		wp_enqueue_script(
			"bb-logic-rules-metabox",
			plugin_dir_url( __FILE__ ) . 'js/index.js',
			array( 'bb-logic-core' ),
			BB_LOGIC_VERSION,
			true
		);
    }

	/**
	 * Process an MB rule based on the object ID of the
	 * field location such as archive, post or user.
	 *
	 * @since  0.1
	 * @param string $object_id
	 * @param object $rule
	 * @return bool
	 */
	static public function mb_evaluate_rule( $object_id = false, $rule ) {
		$value = rwmb_meta( $rule->key, $object_id );

		if ( is_array( $value ) ) {
			$value = empty( $value ) ? 0 : 1;
		} elseif ( is_object( $value ) ) {
			$value = 1;
		}

		return BB_Logic_Rules::evaluate_rule( array(
			'value' 	=> $value,
			'operator' 	=> $rule->operator,
			'compare' 	=> $rule->compare,
			'isset' 	=> $value,
		) );
	}

	/**
	 * Archive field rule.
	 *
	 * @since  0.1
	 * @param object $rule
	 * @return bool
	 */
	static public function archive_field( $rule ) {
		$object = get_queried_object();

		if ( ! is_object( $object ) || ! isset( $object->taxonomy ) || ! isset( $object->term_id ) ) {
			$id = 'archive';
		} else {
			$id = $object->taxonomy . '_' . $object->term_id;
		}

		return self::mb_evaluate_rule( $id, $rule );
	}

	/**
	 * Post field rule.
	 *
	 * @since  0.1
	 * @param object $rule
	 * @return bool
	 */
	static public function post_field( $rule ) {
		global $post;
		$id = is_object( $post ) ? $post->ID : 0;
		return self::mb_evaluate_rule( $id, $rule );
	}

	/**
	 * Post author field rule.
	 *
	 * @since  0.1
	 * @param object $rule
	 * @return bool
	 */
	static public function post_author_field( $rule ) {
		global $post;
		$id = is_object( $post ) ? $post->post_author : 0;
		return self::mb_evaluate_rule( 'user_' . $id, $rule );
	}

	/**
	 * User field rule.
	 *
	 * @since  0.1
	 * @param object $rule
	 * @return bool
	 */
	static public function user_field( $rule ) {
		$user = wp_get_current_user();
		return self::mb_evaluate_rule( 'user_' . $user->ID, $rule );
	}
}

MB_Logic_Rules::init();
