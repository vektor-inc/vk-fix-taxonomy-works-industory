<?php
/*
Plugin Name: VK Fix Taxonomy Works Industory
Description: 
Author: Vektor,Inc.
Version: 1.0.0
License: GPLv2
*/

function fix_taxonomy_slug_case() {
	global $wpdb;

	$old_taxonomy = 'works-Industry';
	$new_taxonomy = 'works-industry';
	$term_taxonomy_id = 7;

	// 該当レコードがあるか確認
	$current = $wpdb->get_var( $wpdb->prepare(
		"SELECT taxonomy FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id = %d",
		$term_taxonomy_id
	) );

	if ( $current === $old_taxonomy ) {
		$updated = $wpdb->update(
			$wpdb->term_taxonomy,
			[ 'taxonomy' => $new_taxonomy ],
			[ 'term_taxonomy_id' => $term_taxonomy_id ]
		);

		if ( $updated !== false ) {
			// キャッシュクリアなど必要に応じて処理
			clean_term_cache( $term_taxonomy_id );
			flush_rewrite_rules();
			echo "Taxonomy slug updated successfully.";
		} else {
			echo "Failed to update taxonomy slug.";
		}
	} else {
		echo "No matching taxonomy found.";
	}
}

// 管理画面の読み込み時に1回実行するなど用途に応じてトリガー
add_action( 'admin_init', 'fix_taxonomy_slug_case' );