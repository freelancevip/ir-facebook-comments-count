<?php
/**
 * Plugin name: Количество комментариев facebook
 * Description: Сортирует записи по количеству комментариев и шар фейсбук
 * Author: freelancevip.pro
 * Author URI: https://freelancevip.pro/
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) || die();

$IR_Facebook_Comments_Counter = new IR_Facebook_Comments_Counter();

/**
 * Class IR_Facebook_Comments_Counter
 */
class IR_Facebook_Comments_Counter {

	/**
	 * IR_Facebook_Comments_Counter constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_print_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_fb_parser', array( $this, 'ajax_parsing' ) );
	}

	function ajax_parsing() {
		if ( ! $this->can_parse() ) {
			exit();
		}
		$date = $_POST['date'] . ' 00:00:00';
		$res  = $this->parsing( $date );
		wp_send_json( $res );
	}

	/**
	 * @param $date_str
	 *
	 * @return array
	 */
	function parsing( $date_str ) {
		$post = $this->get_post_after( $date_str );
		if ( $post ) {
			$url       = get_permalink( $post->ID );
			$info      = $this->get_facebook_info( $url );
			$post_date = date( 'Y-m-d H:i:s', strtotime( $post->post_modified ) );
			$return    = array(
				'dateNextPost' => $post_date,
				'info'         => $info,
				'status'       => true,
				'id'           => $post->ID
			);
			update_post_meta( $post->ID, 'comment_count', $info->share->comment_count );
			update_post_meta( $post->ID, 'share_count', $info->share->share_count );
		} else {
			$return = array(
				'message' => 'Все посты обработаны',
				'status'  => false
			);
		}

		return $return;
	}

	/**
	 * Returns first post after datetime
	 *
	 * @param string $date "Y-m-d H:i:s"
	 *
	 * @return object | null
	 */
	private function get_post_after( $date ) {
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->posts} WHERE post_date > '{$date}' AND post_type = 'post' AND post_status = 'publish' ORDER BY post_modified ASC LIMIT 1";

		return $wpdb->get_row( $sql );
	}

	/**
	 * @return bool
	 */
	private function can_parse() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @param $url
	 *
	 * @return array|mixed|object
	 */
	private function get_facebook_info( $url ) {
		return json_decode( file_get_contents( "https://graph.facebook.com/$url" ) );
	}

	/**
	 * Add menu item
	 */
	function menu() {
		add_submenu_page( 'tools.php', 'facebook-comments-count', 'FB comments', 'manage_options', 'fb-comments-count', array(
			$this,
			'menu_cb'
		) );
	}

	/**
	 * Menu callback
	 */
	function menu_cb() {
		require_once 'admin-page.php';
	}

	/**
	 * Plugin scripts
	 */
	function scripts() {
		wp_enqueue_script( 'fb-comments-count', plugins_url( 'script.js', __FILE__ ) );
	}
}
