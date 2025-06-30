<?php
use PHPUnit\Framework\TestCase;

class FixTaxonomySlugCaseTest extends TestCase {
    private static $term_taxonomy_ids = [];
    private static $term_ids = [];
    private static $old_taxonomy = 'works-Industry';
    private static $new_taxonomy = 'works-industry';

    public static function tearDownAfterClass(): void {
        global $wpdb;
        // term_taxonomyとtermsを削除
        foreach (self::$term_taxonomy_ids as $tt_id) {
            $wpdb->delete($wpdb->term_taxonomy, [ 'term_taxonomy_id' => $tt_id ]);
        }
        foreach (self::$term_ids as $term_id) {
            $wpdb->delete($wpdb->terms, [ 'term_id' => $term_id ]);
        }
    }

    public function setUp(): void {
        global $wpdb;
        if (!isset($GLOBALS['wpdb'])) {
            $GLOBALS['wpdb'] = $wpdb = \WP_Test_Factory::get_wpdb_instance();
        }
    }

    public function test_returns_message_when_no_works_industry_exists() {
        global $wpdb;
        // 念のため事前に削除
        $wpdb->delete($wpdb->term_taxonomy, [ 'taxonomy' => self::$old_taxonomy ]);
        $result = fix_taxonomy_slug_case();
        $this->assertSame('No works-Industry taxonomy found.', $result);
    }

    public function test_replaces_works_industry_and_returns_true() {
        global $wpdb;
        // テスト用のタームを作成
        $wpdb->insert(
            $wpdb->terms,
            [
                'name' => 'Test Industry',
                'slug' => 'test-industry'
            ]
        );
        $term_id = $wpdb->insert_id;
        self::$term_ids[] = $term_id;
        $wpdb->insert(
            $wpdb->term_taxonomy,
            [
                'term_id' => $term_id,
                'taxonomy' => self::$old_taxonomy,
                'description' => '',
                'parent' => 0,
                'count' => 0
            ]
        );
        $tt_id = $wpdb->insert_id;
        self::$term_taxonomy_ids[] = $tt_id;

        $result = fix_taxonomy_slug_case();
        $this->assertTrue($result);
        // 置換後の値を確認
        $taxonomy = $wpdb->get_var( $wpdb->prepare(
            "SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d",
            $tt_id
        ) );
        $this->assertEquals(self::$new_taxonomy, $taxonomy);
    }
}
