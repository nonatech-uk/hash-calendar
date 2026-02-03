<?php
/**
 * Admin Meta Boxes for Hash Runs
 */

if (!defined('ABSPATH')) {
    exit;
}

class GH3_Admin {

    /**
     * Initialize hooks
     */
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_hash_run', array($this, 'save_meta_fields'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));

        // Admin list columns
        add_filter('manage_hash_run_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_hash_run_posts_custom_column', array($this, 'render_custom_columns'), 10, 2);
        add_filter('manage_edit-hash_run_sortable_columns', array($this, 'set_sortable_columns'));
        add_action('pre_get_posts', array($this, 'set_default_sort'));

        // Pagination - set default per page
        add_filter('edit_hash_run_per_page', array($this, 'set_per_page_default'));
        add_filter('get_user_option_edit_hash_run_per_page', array($this, 'get_per_page_option'));

        // Prevent WordPress from setting future status on hash_runs
        add_filter('wp_insert_post_data', array($this, 'prevent_future_status'), 10, 2);
    }

    /**
     * Prevent WordPress from auto-setting 'future' status for hash_runs
     */
    public function prevent_future_status($data, $postarr) {
        if ($data['post_type'] === 'hash_run' && $data['post_status'] === 'future') {
            $data['post_status'] = 'publish';
        }
        return $data;
    }

    /**
     * Set default items per page (50)
     */
    public function set_per_page_default($per_page) {
        return 50;
    }

    /**
     * Get per page option with default
     */
    public function get_per_page_option($value) {
        return $value ? $value : 50;
    }

    /**
     * Set custom columns for admin list
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['run_date'] = __('Date', 'gh3-hash-runs');
        $new_columns['title'] = $columns['title'];
        $new_columns['hares'] = __('Hare(s)', 'gh3-hash-runs');
        $new_columns['location'] = __('Location', 'gh3-hash-runs');
        return $new_columns;
    }

    /**
     * Render custom column content
     */
    public function render_custom_columns($column, $post_id) {
        switch ($column) {
            case 'run_date':
                $run_date = get_post_meta($post_id, '_gh3_run_date', true);
                if ($run_date) {
                    echo esc_html(date_i18n(get_option('date_format'), strtotime($run_date)));
                } else {
                    echo '—';
                }
                break;
            case 'hares':
                $hares = get_post_meta($post_id, '_gh3_hares', true);
                echo $hares ? esc_html($hares) : '—';
                break;
            case 'location':
                $location = get_post_meta($post_id, '_gh3_location', true);
                echo $location ? esc_html($location) : '—';
                break;
        }
    }

    /**
     * Set sortable columns
     */
    public function set_sortable_columns($columns) {
        $columns['run_date'] = 'run_date';
        $columns['hares'] = 'hares';
        $columns['location'] = 'location';
        return $columns;
    }

    /**
     * Set default sort order and handle custom sorting
     */
    public function set_default_sort($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== 'hash_run') {
            return;
        }

        // Handle custom column sorting
        $orderby = $query->get('orderby');

        if ($orderby === 'run_date' || empty($orderby)) {
            // Default sort by run_date ascending
            $query->set('meta_key', '_gh3_run_date');
            $query->set('orderby', 'meta_value');
            if (empty($query->get('order'))) {
                $query->set('order', 'ASC');
            }
        } elseif ($orderby === 'hares') {
            $query->set('meta_key', '_gh3_hares');
            $query->set('orderby', 'meta_value');
        } elseif ($orderby === 'location') {
            $query->set('meta_key', '_gh3_location');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles($hook) {
        global $post_type;

        if ($post_type === 'hash_run') {
            wp_enqueue_style(
                'gh3-admin-css',
                GH3_HASH_RUNS_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                GH3_HASH_RUNS_VERSION
            );
        }
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'gh3_run_details',
            __('Hash Run Details', 'gh3-hash-runs'),
            array($this, 'render_run_details_meta_box'),
            'hash_run',
            'normal',
            'high'
        );

        add_meta_box(
            'gh3_location_details',
            __('Location Details', 'gh3-hash-runs'),
            array($this, 'render_location_meta_box'),
            'hash_run',
            'normal',
            'high'
        );
    }

    /**
     * Get the next suggested run number
     */
    private function get_next_run_number() {
        global $wpdb;

        $last_run_number = $wpdb->get_var(
            "SELECT MAX(CAST(meta_value AS UNSIGNED))
             FROM {$wpdb->postmeta}
             WHERE meta_key = '_gh3_run_number'"
        );

        return $last_run_number ? intval($last_run_number) + 1 : 1;
    }

    /**
     * Render Run Details meta box
     */
    public function render_run_details_meta_box($post) {
        wp_nonce_field('gh3_save_meta', 'gh3_meta_nonce');

        $run_number = get_post_meta($post->ID, '_gh3_run_number', true);
        $run_date = get_post_meta($post->ID, '_gh3_run_date', true);
        $start_time = get_post_meta($post->ID, '_gh3_start_time', true);
        if (empty($start_time)) {
            $start_time = '19:30';
        }
        $hares = get_post_meta($post->ID, '_gh3_hares', true);
        $oninn = get_post_meta($post->ID, '_gh3_oninn', true);
        $notes = get_post_meta($post->ID, '_gh3_notes', true);

        $suggested_number = $this->get_next_run_number();
        ?>
        <div class="gh3-meta-box">
            <div class="gh3-field-row">
                <div class="gh3-field">
                    <label for="gh3_run_number"><?php _e('Run Number', 'gh3-hash-runs'); ?></label>
                    <input type="number"
                           id="gh3_run_number"
                           name="gh3_run_number"
                           value="<?php echo esc_attr($run_number); ?>"
                           placeholder="<?php echo esc_attr($suggested_number); ?>"
                           class="small-text">
                    <?php if (empty($run_number)) : ?>
                        <p class="description"><?php printf(__('Suggested: %d', 'gh3-hash-runs'), $suggested_number); ?></p>
                    <?php endif; ?>
                </div>

                <div class="gh3-field">
                    <label for="gh3_run_date"><?php _e('Run Date', 'gh3-hash-runs'); ?></label>
                    <input type="date"
                           id="gh3_run_date"
                           name="gh3_run_date"
                           value="<?php echo esc_attr($run_date); ?>"
                           class="regular-text">
                </div>

                <div class="gh3-field">
                    <label for="gh3_start_time"><?php _e('Start Time', 'gh3-hash-runs'); ?></label>
                    <input type="time"
                           id="gh3_start_time"
                           name="gh3_start_time"
                           value="<?php echo esc_attr($start_time); ?>">
                    <p class="description"><?php _e('Default: 19:30', 'gh3-hash-runs'); ?></p>
                </div>
            </div>

            <div class="gh3-field-row">
                <div class="gh3-field gh3-field-full">
                    <label for="gh3_hares"><?php _e('Hare(s)', 'gh3-hash-runs'); ?></label>
                    <input type="text"
                           id="gh3_hares"
                           name="gh3_hares"
                           value="<?php echo esc_attr($hares); ?>"
                           class="large-text"
                           placeholder="<?php _e('Who is laying the trail?', 'gh3-hash-runs'); ?>">
                </div>
            </div>

            <div class="gh3-field-row">
                <div class="gh3-field gh3-field-full">
                    <label for="gh3_oninn"><?php _e('On Inn', 'gh3-hash-runs'); ?></label>
                    <input type="text"
                           id="gh3_oninn"
                           name="gh3_oninn"
                           value="<?php echo esc_attr($oninn); ?>"
                           class="large-text"
                           placeholder="<?php _e('Pub/venue after the run', 'gh3-hash-runs'); ?>">
                </div>
            </div>

            <div class="gh3-field-row">
                <div class="gh3-field gh3-field-full">
                    <label for="gh3_notes"><?php _e('Notes', 'gh3-hash-runs'); ?></label>
                    <textarea id="gh3_notes"
                              name="gh3_notes"
                              rows="3"
                              class="large-text"
                              placeholder="<?php _e('Additional information...', 'gh3-hash-runs'); ?>"><?php echo esc_textarea($notes); ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Location meta box
     */
    public function render_location_meta_box($post) {
        $location = get_post_meta($post->ID, '_gh3_location', true);
        $what3words = get_post_meta($post->ID, '_gh3_what3words', true);
        $maps_url = get_post_meta($post->ID, '_gh3_maps_url', true);
        ?>
        <div class="gh3-meta-box">
            <div class="gh3-field-row">
                <div class="gh3-field gh3-field-full">
                    <label for="gh3_location"><?php _e('Location', 'gh3-hash-runs'); ?></label>
                    <input type="text"
                           id="gh3_location"
                           name="gh3_location"
                           value="<?php echo esc_attr($location); ?>"
                           class="large-text"
                           placeholder="<?php _e('Start location description', 'gh3-hash-runs'); ?>">
                </div>
            </div>

            <div class="gh3-field-row">
                <div class="gh3-field">
                    <label for="gh3_what3words"><?php _e('What3Words', 'gh3-hash-runs'); ?></label>
                    <input type="text"
                           id="gh3_what3words"
                           name="gh3_what3words"
                           value="<?php echo esc_attr($what3words); ?>"
                           class="regular-text"
                           placeholder="<?php _e('e.g., ///word.word.word', 'gh3-hash-runs'); ?>">
                </div>

                <div class="gh3-field">
                    <label for="gh3_maps_url"><?php _e('Google Maps URL', 'gh3-hash-runs'); ?></label>
                    <input type="url"
                           id="gh3_maps_url"
                           name="gh3_maps_url"
                           value="<?php echo esc_url($maps_url); ?>"
                           class="large-text"
                           placeholder="<?php _e('https://maps.google.com/...', 'gh3-hash-runs'); ?>">
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save meta fields
     */
    public function save_meta_fields($post_id) {
        // Check nonce
        if (!isset($_POST['gh3_meta_nonce']) || !wp_verify_nonce($_POST['gh3_meta_nonce'], 'gh3_save_meta')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save fields
        $fields = array(
            'gh3_run_number'  => '_gh3_run_number',
            'gh3_run_date'    => '_gh3_run_date',
            'gh3_start_time'  => '_gh3_start_time',
            'gh3_hares'       => '_gh3_hares',
            'gh3_location'    => '_gh3_location',
            'gh3_what3words'  => '_gh3_what3words',
            'gh3_maps_url'    => '_gh3_maps_url',
            'gh3_oninn'       => '_gh3_oninn',
            'gh3_notes'       => '_gh3_notes',
        );

        foreach ($fields as $field_name => $meta_key) {
            if (isset($_POST[$field_name])) {
                $value = $_POST[$field_name];

                // Sanitize based on field type
                if ($meta_key === '_gh3_run_number') {
                    $value = intval($value);
                } elseif ($meta_key === '_gh3_maps_url') {
                    $value = esc_url_raw($value);
                } elseif ($meta_key === '_gh3_notes') {
                    $value = sanitize_textarea_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }

                update_post_meta($post_id, $meta_key, $value);
            }
        }

        // Auto-generate title if Run # is set
        $run_number = isset($_POST['gh3_run_number']) ? intval($_POST['gh3_run_number']) : 0;
        if ($run_number > 0) {
            $hares = isset($_POST['gh3_hares']) ? sanitize_text_field($_POST['gh3_hares']) : '';
            $location = isset($_POST['gh3_location']) ? sanitize_text_field($_POST['gh3_location']) : '';

            // Build title: only include " - " if both exist
            if ($hares && $location) {
                $new_title = $hares . ' - ' . $location;
            } elseif ($hares) {
                $new_title = $hares;
            } elseif ($location) {
                $new_title = $location;
            } else {
                $new_title = '';
            }

            // Update post title
            remove_action('save_post_hash_run', array($this, 'save_meta_fields'));
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $new_title,
            ));
            add_action('save_post_hash_run', array($this, 'save_meta_fields'));
        }
    }
}
