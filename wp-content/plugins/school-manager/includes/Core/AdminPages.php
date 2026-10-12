<?php
namespace SchoolManager\Core;

class AdminPages {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu_pages']);
    }

    public static function add_menu_pages() {
        add_menu_page(
            'School Manager',
            'School Manager',
            'manage_options',
            'school-manager',
            [__CLASS__, 'render_schools_page'],
            'dashicons-building',
            3
        );
        add_submenu_page(
            'school-manager',
            'Subscriptions',
            'Subscriptions',
            'manage_options',
            'school-manager-subscriptions',
            [__CLASS__, 'render_subscriptions_page']
        );
    }

    public static function render_schools_page() {
        if (!current_user_can('manage_options')) return;

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        if (isset($_POST['sm_action']) && $_POST['sm_action'] == 'toggle_status') {
            check_admin_referer('sm_toggle_status');
            $id = intval($_POST['school_id']);
            $current_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$prefix}schools WHERE id = %d", $id));
            $new_status = ($current_status == 'active') ? 'suspended' : 'active';
            $wpdb->update("{$prefix}schools", ['status' => $new_status, 'updated_at' => current_time('mysql')], ['id' => $id]);
            echo '<div class="updated"><p>Status updated.</p></div>';
        }

        $schools = $wpdb->get_results("SELECT * FROM {$prefix}schools ORDER BY created_at DESC");

        echo '<div class="wrap"><h1>Schools</h1>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Name</th><th>Admin Email</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($schools as $s) {
            echo '<tr>';
            echo '<td>' . esc_html($s->name) . '</td>';
            echo '<td>' . esc_html($s->admin_email) . '</td>';
            echo '<td>' . esc_html($s->status) . '</td>';
            echo '<td>' . esc_html($s->created_at) . '</td>';
            echo '<td>
                <form method="post" style="display:inline;">
                    ' . wp_nonce_field('sm_toggle_status', '_wpnonce', true, false) . '
                    <input type="hidden" name="sm_action" value="toggle_status">
                    <input type="hidden" name="school_id" value="'.intval($s->id).'">
                    <button type="submit" class="button">Toggle Status</button>
                </form>
            </td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }

    public static function render_subscriptions_page() {
        echo '<div class="wrap"><h1>Subscriptions</h1><p>Super Admin subscription management goes here.</p></div>';
    }
}
