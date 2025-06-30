<?php
/*
Plugin Name: VK Fix Taxonomy Works Industory
Description: 2025年6月30日以前に VK FullSite Installer から「X-T9 Pro 版ビジネス」のサイトをインポートしたサイトで、カスタム投稿タイプ制作実績（works）のカスタムタクソノミー works-Industry をそのまま使用している場合に誤動作を引き起こすため、works-Industry をworks-industry に書き換えるためのプラグインです。一度有効化してダッシュボードを開いたら処理が完了するので停止・削除してください。
Author: Vektor,Inc.
Version: 0.0.1
License: GPLv2
*/

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

/**
 * 投稿タイプマネージャーの業績タクソノミーのスラッグを works-industryに書き換える
 */
function vkftwi_tax_setting_replace(){
	// 投稿 idが79 に紐づいているカスタムフィールド名"veu_taxonomy" の値を取得
	$meta_value = get_post_meta( 79, 'veu_taxonomy', true );
	$meta_value[2]['slug'] = 'works-industry'; // slugをworks-industryに書き換え
	// 変更した値を再度保存
	update_post_meta( 79, 'veu_taxonomy', $meta_value );
}
add_action( 'admin_init', 'vkftwi_tax_setting_replace' );

/**
 * works-sidebar テンプレートの内容を更新
 */
function vkftwi_fix_works_sidebar() {

	// 投稿idが90の投稿を取得
	$sidebar_works = get_post( 90 );
	// コンテンツ内に 'works-Industry' が含まれている場合は 'works-industry' に置き換える
	if ( strpos( $sidebar_works->post_content, 'works-Industry' ) !== false ) {
		$sidebar_works->post_content = str_replace( 'works-Industry', 'works-industry', $sidebar_works->post_content );
		// 更新
		wp_update_post( $sidebar_works );
	}
	// コンテンツ内に '実績カーカイブ' が含まれている場合は '実績アーカイブ' に置き換える
	if ( strpos( $sidebar_works->post_content, '実績カーカイブ' ) !== false ) {
		$sidebar_works->post_content = str_replace( '実績カーカイブ', '実績アーカイブ', $sidebar_works->post_content );
		// 更新
		wp_update_post( $sidebar_works );
	}

}
add_action( 'admin_init', 'vkftwi_fix_works_sidebar' );

/**
 * works-taxonomy パターンの内容を更新
 */
function vkftwi_fix_works_taxonomy() {

	$works_taxonomy = get_post( 159 );
	// コンテンツ内に 'works-Industry' が含まれている場合は 'works-industry' に置き換える
	if ( strpos( $works_taxonomy->post_content, 'works-Industry' ) !== false ) {
		$works_taxonomy->post_content = str_replace( 'works-Industry', 'works-industry', $works_taxonomy->post_content );
		// 更新
		wp_update_post( $works_taxonomy );
	}

}
add_action( 'admin_init', 'vkftwi_fix_works_taxonomy' );