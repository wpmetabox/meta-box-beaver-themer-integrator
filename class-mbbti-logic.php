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
	public function __construct() {
		if ( did_action( 'bb_logic_init' ) ) {
			$this->init();
		} else {
			add_action( 'bb_logic_init', array( $this, 'init' ) );
		}
	}

	/**
	 * Sets up callbacks for conditional logic rules.
	 */
	public function init() {
		BB_Logic_Rules::register(
			array(
				'metabox/archive-field'       => array( $this, 'archive_field' ),
				'metabox/post-field'          => array( $this, 'post_field' ),
				'metabox/post-author-field'   => array( $this, 'post_author_field' ),
				'metabox/user-field'          => array( $this, 'user_field' ),
				'metabox/settings-page-field' => array( $this, 'settings_page_field' ),
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
	public function evaluate_rule( $value = false, $rule ) {
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
		$term_id = get_queried_object_id();
		$value   = rwmb_meta( $rule->key, array( 'object_type' => 'term' ), $term_id );

		return $this->evaluate_rule( $value, $rule );
	}

	/**
	 * Post field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function post_field( $rule ) {
		global $post;
		$post_id = is_object( $post ) ? $post->ID : 0;
		$value   = rwmb_meta( $rule->key, '', $post_id );

		return $this->evaluate_rule( $value, $rule );
	}

	/**
	 * Post author field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function post_author_field( $rule ) {
		global $post;
		$id    = is_object( $post ) ? $post->post_author : 0;
		$value = rwmb_meta( $rule->key, array( 'object_type' => 'user' ), $post->post_author );

		return $this->evaluate_rule( $value, $rule );
	}

	/**
	 * User field rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function user_field( $rule ) {
		$user  = wp_get_current_user();
		$value = rwmb_meta( $rule->key, array( 'object_type' => 'user' ), $user->ID );

		return $this->evaluate_rule( $value, $rule );
	}

	/**
	 * Settings page rule.
	 *
	 * @param object $rule Conditional logic rule.
	 * @return bool
	 */
	public function settings_page_field( $rule ) {
		$value = rwmb_meta( $rule->key, array( 'object_type' => 'setting' ), $rule->option_name );
		return $this->evaluate_rule( $value, $rule );
	}

}
