<?php
/**
 * Plugin name: Количество комментариев facebook
 * Description: Сортирует записи по количеству комментариев и шар фейсбук
 * Author: freelancevip.pro
 * Author URI: https://freelancevip.pro/
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) || die();

new IR_Facebook_Comments_Counter();

class IR_Facebook_Comments_Counter {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	function menu() {
		add_submenu_page( 'tools.php', 'facebook-comments-count', 'FB comments', 'manage_options', 'fb-comments-count', array(
			$this,
			'menu_cb'
		) );
	}

	function menu_cb() {
		?>
        <div class="wrap">
            <h1>Записи по количеству комментариев facebook</h1>
            <form action="">
                <p>
                    <b>Дата начала парсинга</b>
                </p>
                <p>
                    <input type="date" name="dbDateStart" style="width: 214px;">
                </p>
                <p>
                    <input id="fb-continue" type="submit" value="Продолжить" class="button button-primary button-large">
                    <input id="fb-start" type="submit" value="Начать заново" class="button-link-delete">
                </p>
            </form>
        </div>
		<?php
	}
}