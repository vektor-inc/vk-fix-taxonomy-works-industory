<?php
/*
Plugin Name: VK Fix Taxonomy Works Industory
Description: 
Author: Vektor,Inc.
Version: 1.0.0
License: GPLv2
*/

// function vkftwi_should_deactivate_plugin() {
// 	global $wpdb;
// 	$old_taxonomy = 'works-Industry';
// 	$new_taxonomy = 'works-industry';
// 	$old_exists = $wpdb->get_var( $wpdb->prepare(
// 		"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s",
// 		$old_taxonomy
// 	) );
// 	$new_exists = $wpdb->get_var( $wpdb->prepare(
// 		"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s",
// 		$new_taxonomy
// 	) );
// 	return ( !$old_exists && $new_exists );
// }

// function vkftwi_deactivate_if_needed() {
// 	if ( vkftwi_should_deactivate_plugin() ) {
// 		if ( is_admin() && current_user_can('activate_plugins') ) {
// 			deactivate_plugins( plugin_basename(__FILE__) );
// 			add_action('admin_notices', function() {
// 				echo '<div class="notice notice-success is-dismissible"><p>"works-Industry" が存在せず "works-industry" が存在するため、VK Fix Taxonomy Works Industory プラグインは自動停止されました。</p></div>';
// 			});
// 		}
// 	}
// }
// add_action( 'admin_init', 'vkftwi_deactivate_if_needed', 1 );

/**
 * works-Industry というタクソノミーが存在していたら works-industry に書き換え処理を実行する関数
 */
function vkftwi_replace_old_works_industory_if_exist() {
	if ( vkftwi_is_old_works_industory_exist() ) {
		global $wpdb;
		$old_taxonomy = 'works-Industry';
		$new_taxonomy = 'works-industry';

		// まず works-Industry が存在するか確認
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
			$old_taxonomy
		) );
		if ( ! $count ) {
			return 'No works-Industry taxonomy found.';
		}

		// works-Industry を全て works-industry に書き換え
		$updated = $wpdb->update(
			$wpdb->term_taxonomy,
			[ 'taxonomy' => $new_taxonomy ],
			[ 'taxonomy' => $old_taxonomy ]
		);

		if ( $updated !== false && $updated > 0 ) {
			clean_term_cache( 0, $new_taxonomy );
			flush_rewrite_rules();
			return true;
		} else {
			return 'No matching taxonomy found or update failed.';
		}
	}
}
add_action( 'admin_init', 'vkftwi_replace_old_works_industory_if_exist', 15 );

/**
 * works-Industry というタクソノミーが存在しているかどうかを確認する関数
 * 
 * @return bool
 * true: 存在する
 * false: 存在しない
 */
function vkftwi_is_old_works_industory_exist() {
	global $wpdb;
	$old_taxonomy = 'works-Industry';
	$count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
		$old_taxonomy
	) );
	return (bool) $count;
}