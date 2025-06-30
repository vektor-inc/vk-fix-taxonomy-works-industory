<?php

class FixTaxonomySlugCaseTest extends WP_UnitTestCase {
    private static $term_taxonomy_id;
    private static $term_id;
    private static $old_taxonomy = 'works-Industry';

    public static function tearDownAfterClass(): void {
        global $wpdb;
        if ( self::$term_taxonomy_id ) {
            $wpdb->delete($wpdb->term_taxonomy, [ 'term_taxonomy_id' => self::$term_taxonomy_id ]);
        }
        if ( self::$term_id ) {
            $wpdb->delete($wpdb->terms, [ 'term_id' => self::$term_id ]);
        }
    }

    public function test_returns_false_when_no_works_industry_exists() {
        global $wpdb;
        // 念のため事前に削除
        $wpdb->delete($wpdb->term_taxonomy, [ 'taxonomy' => self::$old_taxonomy ]);
        $this->assertFalse(vkftwi_is_old_works_industory_exist());
    }

    public function test_returns_true_when_works_industry_exists() {
        global $wpdb;
        // テスト用のタームを作成
        $wpdb->insert(
            $wpdb->terms,
            [
                'name' => 'Test Industry',
                'slug' => 'test-industry'
            ]
        );
        self::$term_id = $wpdb->insert_id;
        $wpdb->insert(
            $wpdb->term_taxonomy,
            [
                'term_id' => self::$term_id,
                'taxonomy' => self::$old_taxonomy,
                'description' => '',
                'parent' => 0,
                'count' => 0
            ]
        );
        self::$term_taxonomy_id = $wpdb->insert_id;
        $this->assertTrue(vkftwi_is_old_works_industory_exist());
    }
}
