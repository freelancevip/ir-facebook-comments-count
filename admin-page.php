<?php
$meta_key = isset( $_GET['meta_key'] ) ? $_GET['meta_key'] : 'comment_count';
$order    = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
$after    = isset( $_GET['after'] ) ? $_GET['after'] : '';
$args     = array(
	'posts_per_page' => 50,
	'post_status'    => 'publish',
	'meta_key'       => $meta_key,
	'orderby'        => 'meta_value_num',
	'order'          => $order
);
if ( $after ) {
	$args['date_query'] = array(
		'after'     => $after,
		'inclusive' => true
	);
}
?>
<div class="wrap" id="fb-comments-count-container">
    <div id="fb-comments-count-loader">
        <div class="fb-comments-count-loader"></div>
    </div>
    <h1>Записи по количеству комментариев facebook</h1>
	<?php
	$date  = new DateTime();
	$day   = $date->format( "Y-m-d" );
	$week  = $date->modify( '-1 week' )->format( "Y-m-d" );
	$month = $date->modify( '-1 month' )->format( "Y-m-d" );

	$uri      = $_SERVER['REQUEST_URI'];
	$active   = ' button-link-delete';
	$d_active = '';
	$w_active = '';
	$m_active = '';
	if ( strpos( $uri, $day ) ) {
		$d_active = $active;
	}
	if ( strpos( $uri, $week ) ) {
		$w_active = $active;
	}
	if ( strpos( $uri, $month ) ) {
		$m_active = $active;
	}
	?>
    <div class="button-controls">
        <a href="admin.php?page=fb-comments-count&meta_key=<?php echo $meta_key ?>&order=<?php echo $order ?>&after=<?php echo $day ?>"
           class="button<?php echo $d_active ?>">За
            день</a>
        <a href="admin.php?page=fb-comments-count&meta_key=<?php echo $meta_key ?>&order=<?php echo $order ?>&after=<?php echo $week ?>"
           class="button<?php echo $w_active ?>">За
            неделю</a>
        <a href="admin.php?page=fb-comments-count&meta_key=<?php echo $meta_key ?>&order=<?php echo $order ?>&after=<?php echo $month ?>"
           class="button<?php echo $m_active ?>">За
            месяц</a>
    </div>
    <table class="widefat">
        <thead>
        <tr>
            <td>Заголовок</td>
            <td>
                <a href="admin.php?page=fb-comments-count&meta_key=comment_count&order=<?php echo $order ?>&after=<?php echo $after ?>">Комментариев</a>
            </td>
            <td>
                <a href="admin.php?page=fb-comments-count&meta_key=share_count&order=<?php echo $order ?>&after=<?php echo $after ?>">Перепосты</a>
            </td>
            <td>Действие</td>
        </tr>
        </thead>
		<?php
		$query = new WP_Query( $args );
		if ( $query->have_posts() ):
			?>
            <tbody>
			<?php while ( $query->have_posts() ) : $query->the_post();
				$id            = get_the_ID();
				$comment_count = get_post_meta( $id, 'comment_count', true );
				if ( ! $comment_count ) {
					$comment_count = '0';
				}
				$share_count = get_post_meta( $id, 'share_count', true );
				if ( ! $share_count ) {
					$share_count = '0';
				}
				?>
                <tr>
                    <td><?php the_title() ?></td>
                    <td><?php echo $comment_count ?></td>
                    <td><?php echo $share_count ?></td>
                    <td><a target="_blank" href="<?php the_permalink() ?>"><i class="fa fa-link" aria-hidden="true"></i>
                            Перейти</a>
                    </td>
                </tr>
			<?php endwhile; ?>
            </tbody>
		<?php
		endif;
		?>
    </table>
</div>
<style>
    #fb-comments-count-container td {
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
    }

    #fb-comments-count-container tr:hover {
        background: #f5f5f5;
    }

    #fb-comments-count-container tr:nth-child(even) {
        background: #f9f9f9;
    }

    #fb-comments-count-container {
        position: relative;
    }

    #fb-comments-count-loader {
        position: fixed;
        top: -10px;
        left: -23px;
        right: -20px;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1000;
        display: none;
    }

    .fb-comments-count-loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -60px;
        margin-top: -60px;
        z-index: 1001;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    #fb-stop {
        z-index: 10002;
        position: relative;
    }
</style>