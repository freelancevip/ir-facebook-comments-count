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
	const SITE_URL = 'https://inforesist.org/';

	/**
	 * IR_Facebook_Comments_Counter constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * @param $date_str
	 *
	 * @return void
	 */
	function parsing( $date_str ) {
		sleep( 5 );
		$urls = $this->get_posts_after( $date_str );
		echo "Всего записей за указанный период: " . count( $urls );
		echo '<br>';

		$url_chunks = array_chunk( $urls, 50 );

		foreach ( $url_chunks as $chunk ) {
			$fb_data = $this->facebook_multiple_info( $chunk );
			if ( $fb_data ) {
				foreach ( $fb_data as $index => $item ) {
					$postid = url_to_postid( $index );
					$post   = get_post( $postid );
					update_post_meta( $post->ID, 'comment_count', $item->share->comment_count );
					update_post_meta( $post->ID, 'share_count', $item->share->share_count );
					echo "Для записи {$post->ID} спарсены значения: {$item->share->comment_count}, {$item->share->share_count}";
				}
			}
		}
	}

	/**
	 * $urls_array
	 *
	 * @return array
	 */
	function facebook_multiple_info( $urls_array ) {
		$fb_query = "https://graph.facebook.com/?ids=";
		foreach ( $urls_array as $url ) {
			$fb_query .= $url . ',';
		}
		$fb_query = rtrim( $fb_query, ',' );
		$results  = json_decode( file_get_contents( $fb_query ) );

		return $results;
	}

	/**
	 * Returns first post after datetime
	 *
	 * @param string $date "Y-m-d H:i:s"
	 *
	 * @return array
	 */
	private function get_posts_after( $date ) {
		global $wpdb;
		$sql = "SELECT post_name FROM {$wpdb->posts} WHERE post_date > '{$date}' AND post_type = 'post' AND post_status = 'publish' ORDER BY post_modified ASC LIMIT 3000";

		$results = $wpdb->get_col( $sql );

		return array_map( function ( $name ) {
			return self::SITE_URL . $name . '/';
		}, $results );
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
		add_menu_page( 'FB comments', 'FB comments', 'activate_plugins', 'fb-comments-count', array(
			$this,
			'menu_cb'
		), null, 6 );
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
