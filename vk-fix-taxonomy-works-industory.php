<?php
/*
Plugin Name: VK Fix Taxonomy Works Industory
Description: 
Author: Vektor,Inc.
Version: 1.0.0
License: GPLv2
*/

function vkftwi_should_deactivate_plugin() {
	global $wpdb;
	$old_taxonomy = 'works-Industry';
	$new_taxonomy = 'works-industry';
	$old_exists = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s",
		$old_taxonomy
	) );
	$new_exists = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s",
		$new_taxonomy
	) );
	return ( !$old_exists && $new_exists );
}

function vkftwi_deactivate_if_needed() {
	if ( vkftwi_should_deactivate_plugin() ) {
		if ( is_admin() && current_user_can('activate_plugins') ) {
			deactivate_plugins( plugin_basename(__FILE__) );
			add_action('admin_notices', function() {
				echo '<div class="notice notice-success is-dismissible"><p>"works-Industry" が存在せず "works-industry" が存在するため、VK Fix Taxonomy Works Industory プラグインは自動停止されました。</p></div>';
			});
		}
	}
}
add_action( 'admin_init', 'vkftwi_deactivate_if_needed', 1 );

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
add_action( 'admin_init', 'fix_taxonomy_slug_case', 20 );