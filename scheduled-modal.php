<?php

/**
 * Plugin Name: Scheduled Modal
 * Plugin URI: https://github.com/discern/wp-scheduled-modal
 * Description: Allows scheduling popups/modals to appear/disappear at specific times. Supports background images, gradients, internal and external links and more!
 * Version: 1.2.10
 * Author: Discern
 * License: GPL3
 * Text Domain: scheduled-modal
 */

namespace Discern\ScheduledModal;

defined('ABSPATH') || exit;


final class ScheduledModal
{
    private const MINIMUM_PHP_VERSION = '8.3';
    private const SUPPRESS_DAYS_DEFAULT = 30;
    private const DELAY_SECONDS_DEFAULT = 3;

    private const ALLOWED_TAGS = [
            'a'      => [
                    'href'   => true,
                    'title'  => true,
                    'rel'    => true,
                    'target' => true,
            ],
            'b'      => [],
            'br'     => [],
            'em'     => [],
            'hr'     => [],
            'i'      => [],
            'small'  => [],
            'strong' => [],
    ];

    private const BUTTON_COLORS = [
            'Red',
            'Orange',
            'Amber',
            'Yellow',
            'Lime',
            'Green',
            'Emerald',
            'Teal',
            'Cyan',
            'Sky',
            'Blue',
            'Indigo',
            'Violet',
            'Purple',
            'Fuchsia',
            'Pink',
            'Rose',
            'Slate',
            'Gray',
            'Zinc',
            'Neutral',
            'Stone',
    ];

    public const DEFAULT_GRADIENT = [
            'primary'   => '#4a044e',
            'secondary' => '#111827',
            'direction' => 'to right',
            'opacity'   => 0.6,
    ];

    public function __construct()
    {
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('WP Scheduled Modal requires PHP ' . esc_html(self::MINIMUM_PHP_VERSION) . ' or higher. Please upgrade PHP.');
        }

        $this->init_hooks();
    }


    private function init_hooks(): void
    {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_kwp_scheduled_modal', [$this, 'save_modal'], 10, 1);
        add_action('pre_get_posts', [$this, 'list_modals']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('wp_footer', [$this, 'render_active_modal']);

        add_filter('acf/settings/remove_wp_meta', '__return_true');
        add_filter('manage_kwp_scheduled_modal_posts_columns', function ($columns) {
            $new = [];

            foreach ($columns as $key => $value) {
                if ($key === 'date') {
                    // Insert start/end time *before* date
                    $new['start_time'] = 'Start Time';
                    $new['end_time'] = 'End Time';
                }
                $new[$key] = $value;
            }

            unset($new['date']);

            return $new;
        });

        add_filter('post_class', function ($classes, $class, $post_id) {
            if (get_post_type($post_id) !== 'kwp_scheduled_modal') {
                return $classes;
            }

            $now = current_time('timestamp');
            $start = strtotime(get_post_meta($post_id, '_kwp_modal_start_time', true));
            $end = strtotime(get_post_meta($post_id, '_kwp_modal_end_time', true));

            if ($start && $end && $now >= $start && $now <= $end) {
                $classes[] = 'kwp-modal-active';
            } elseif ($start && $now < $start) {
                $classes[] = 'kwp-modal-upcoming';
            } elseif ($end && $now > $end) {
                $classes[] = 'kwp-modal-expired';
            } elseif (!$start || !$end) {
                $classes[] = 'kwp-modal-incomplete';
            }

            return $classes;
        }, 10, 3);

        add_filter('manage_edit-kwp_scheduled_modal_sortable_columns', function ($columns) {
            $columns['start_time'] = 'start_time';
            $columns['end_time'] = 'end_time';

            return $columns;
        });

        add_action('manage_kwp_scheduled_modal_posts_custom_column', function ($column, $post_id) {
            switch ($column) {
                case 'start_time':
                    $value = get_post_meta($post_id, '_kwp_modal_start_time', true);
                    echo $value ? esc_html($value) : '—';
                    break;
                case 'end_time':
                    $value = get_post_meta($post_id, '_kwp_modal_end_time', true);
                    echo $value ? esc_html($value) : '—';
                    break;
            }
        }, 10, 2);

        // Remove all Yoast columns from Scheduled Modal list view
        add_filter('manage_edit-kwp_scheduled_modal_columns', function ($columns) {
            // Yoast columns to unset
            $yoast_columns = [
                    'wpseo-score',
                    'wpseo-score-readability',
                    'wpseo-title',
                    'wpseo-metadesc',
                    'wpseo-focuskw',
                    'wpseo-links',
                    'wpseo-linked',
                    'wpseo-content_score',
            ];

            foreach ($yoast_columns as $col) {
                if (isset($columns[$col])) {
                    unset($columns[$col]);
                }
            }

            return $columns;
        }, 20);
    }


    public function render_info_metabox()
    {
        ?>
        <p>
            Clicking “Update” will create a new identifier for this popup, which will
            <strong style="color: #911">force it to reappear</strong> for all visitors,
            even those who have previously dismissed the popup.
        </p>
        <p>
            <strong style="color: #911">If an unexpected popup appears</strong>, check
            the start and end times of all scheduled popups for overlapping times. Only
            one popup can appear at a time, but if multiple popups are active, only the
            one with the earliest start time will appear. (Hint: look for
            <strong style="color: #090">Active</strong> in the list view.)
        </p>
        <?php
    }


    public function register_post_type(): void
    {
        // Register custom post type
        register_post_type('kwp_scheduled_modal', [
                'capability_type' => ['page', 'pages'],
                'menu_icon'       => 'dashicons-format-gallery',
                'has_archive'     => false,
                'map_meta_cap'    => true,
                'public'          => false,
                'show_ui'         => true,
                'supports'        => ['title'],
                'labels'          => [
                        'name'               => 'Popups',
                        'singular_name'      => 'Popup',
                        'add_new_item'       => 'Add New Popup',
                        'edit_item'          => 'Edit Popup',
                        'view_item'          => 'View Popup',
                        'all_items'          => 'All Popups',
                        'search_items'       => 'Search Popups',
                        'not_found'          => 'No Popups found.',
                        'not_found_in_trash' => 'No Popups found in Trash.',
                ],
        ]);
    }

    public function add_meta_boxes(): void
    {
        // Remove Yoast SEO meta box from Scheduled Modal edit screen
        remove_meta_box('wpseo_meta', 'kwp_scheduled_modal', 'normal');

        add_meta_box(
                'scheduled_modal_meta',
                'Popup Settings',
                [$this, 'render_scheduled_modal_meta_box'],
                'kwp_scheduled_modal',
                'normal',
                'high'
        );

        add_meta_box(
                'kwp_modal_info_meta',
                'Popup Tips',
                [$this, 'render_info_metabox'],
                'kwp_scheduled_modal',
                'side'
        );
    }

    public function render_scheduled_modal_meta_box($post): void
    {
        $meta = [
                'button_color'    => get_post_meta($post->ID, '_kwp_modal_button_color', true),
                'button_text'     => get_post_meta($post->ID, '_kwp_modal_button_text', true),
                'callout'         => get_post_meta($post->ID, '_kwp_modal_callout', true),
                'delay'           => get_post_meta($post->ID, '_kwp_modal_delay', true),
                'end_time'        => get_post_meta($post->ID, '_kwp_modal_end_time', true),
                'extra'           => get_post_meta($post->ID, '_kwp_modal_extra', true),
                'gradient'        => $this->parseGradient(get_post_meta($post->ID, '_kwp_modal_gradient', true)),
                'menu_item_class' => get_post_meta($post->ID, '_kwp_modal_menu_item_class', true),
                'menu_item_label' => get_post_meta($post->ID, '_kwp_modal_menu_item_label', true),
                'start_time'      => get_post_meta($post->ID, '_kwp_modal_start_time', true),
                'supporting'      => get_post_meta($post->ID, '_kwp_modal_supporting', true),
                'suppress'        => get_post_meta($post->ID, '_kwp_modal_suppress', true),
                'target_post_id'  => get_post_meta($post->ID, '_kwp_modal_target_post_id', true),
                'target_url'      => get_post_meta($post->ID, '_kwp_modal_target_url', true),
                'text_color'      => get_post_meta($post->ID, '_kwp_modal_text_color', true) ?: 'kwp-modal-text-light',
                'text_shadow'     => get_post_meta($post->ID, '_kwp_modal_text_shadow', true) ?: '0',
        ];

        $button_href = '';
        if (!empty($meta['target_post_id'])) {
            $button_href = get_permalink($meta['target_post_id']);
        } elseif (!empty($meta['target_url'])) {
            $button_href = $meta['target_url'];
        }

        wp_nonce_field('save_scheduled_modal_meta', 'scheduled_modal_nonce');

        $image_id = get_post_meta($post->ID, '_kwp_modal_bg_image', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

        $this->render_view('form', [
                'meta'        => $meta,
                'image_id'    => $image_id,
                'image_url'   => $image_url,
                'title'       => $post->post_title,
                'button_href' => $button_href,
        ]);
    }

    public function render_view($file, $vars): void
    {
        $file = basename($file, '.php');
        extract($vars);
        require trailingslashit(plugin_dir_path(__FILE__)) . 'assets/inc/scheduled-modal-' . $file . '.php';
    }

    /**
     * @noinspection PhpUnused
     */
    protected function getAllowedTags(): array
    {
        return self::ALLOWED_TAGS;
    }

    /**
     * @noinspection PhpUnused
     */
    protected function getButtonColors(): array
    {
        return self::BUTTON_COLORS;
    }

    public function save_modal(int $post_id): void
    {
        // Bail on autosave or missing capability
        if (
                (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
                !current_user_can('edit_post', $post_id)
        ) {
            return;
        }

        if (
                !is_admin() ||          // ensure this runs only in admin
                !isset($_POST['scheduled_modal_nonce']) ||
                !check_admin_referer('save_scheduled_modal_meta', 'scheduled_modal_nonce')
        ) {
            return;
        }

        $fields = [
                'bg_image',
                'button_color',
                'button_text',
                'callout',
                'delay',
                'end_time',
                'extra',
                'menu_item_class',
                'menu_item_label',
                'start_time',
                'supporting',
                'suppress',
                'target_post_id',
                'target_url',
                'text_color',
                'text_shadow',
        ];

        $gradient = [
                'primary'   => sanitize_hex_color(wp_unslash($_POST['_kwp_modal_gradient_primary'] ?? self::DEFAULT_GRADIENT['primary'])),
                'secondary' => sanitize_hex_color(wp_unslash($_POST['_kwp_modal_gradient_secondary'] ?? self::DEFAULT_GRADIENT['secondary'])),
                'direction' => sanitize_text_field(wp_unslash($_POST['_kwp_modal_gradient_direction'] ?? self::DEFAULT_GRADIENT['direction'])),
                'opacity'   => min(max(floatval(wp_unslash($_POST['_kwp_modal_gradient_opacity'] ?? self::DEFAULT_GRADIENT['opacity'])), 0), 1),
        ];

        // String prevents weird json encoding issue where 0.8 becomes 0.80000000000000004
        $gradient['opacity'] = (string)round((($gradient['opacity'] * 100)) / 100, 2);
        update_post_meta($post_id, '_kwp_modal_gradient', wp_json_encode($gradient));

        foreach ($fields as $field) {
            $field = '_kwp_modal_' . $field;

            if (isset($_POST[$field])) {
                // Apply a very permissive kses filter first, then sanitize based on field type
                $value = wp_kses_post(wp_unslash($_POST[$field]));

                switch ($field) {
                    case '_kwp_modal_callout':
                    case '_kwp_modal_supporting':
                    case '_kwp_modal_extra':
                        $value = wp_kses($value, $this->getAllowedTags());
                        break;

                    case '_kwp_modal_target_url':
                        $value = esc_url_raw($value);
                        break;

                    case '_kwp_modal_delay':
                    case '_kwp_modal_suppress':
                    case '_kwp_modal_target_post_id':
                    case '_kwp_modal_bg_image':
                        $value = intval($value);

                        if ($field === '_kwp_modal_delay' && $value < 0) {
                            $value = self::DELAY_SECONDS_DEFAULT; // default to 3 seconds if invalid
                        }

                        if ($field === '_kwp_modal_suppress' && $value < 1) {
                            $value = self::SUPPRESS_DAYS_DEFAULT; // default to 30 days if invalid
                        }
                        break;

                    case '_kwp_modal_start_time':
                    case '_kwp_modal_end_time':
                        $value = strtotime($value);

                        if (!$value) {
                            $value = null;
                        } else {
                            $value = gmdate('Y-m-d H:i:s', $value);
                        }
                        break;

                    default:
                        $value = sanitize_text_field($value);
                        break;
                }

                update_post_meta($post_id, $field, $value);
            } else {
                delete_post_meta($post_id, $field);
            }
        }
    }

    public function list_modals($query): void
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== 'kwp_scheduled_modal') {
            return;
        }

        $orderby = $query->get('orderby');

        if (!$orderby) {
            $query->set('orderby', 'meta_value'); // meta_value is required, see below
            $query->set('meta_key', '_kwp_modal_start_time'); // sort by start time within active
            $query->set('order', 'DESC'); // earliest start time first
        } elseif ($orderby === 'start_time') {
            $query->set('meta_key', '_kwp_modal_start_time');
            $query->set('orderby', 'meta_value');
        } elseif ($orderby === 'end_time') {
            $query->set('meta_key', '_kwp_modal_end_time');
            $query->set('orderby', 'meta_value');
        }
    }

    public function enqueue_admin_scripts($hook): void
    {
        if (in_array($hook, ['edit.php', 'post.php', 'post-new.php'], true) && get_post_type() === 'kwp_scheduled_modal') {
            wp_enqueue_media(); // loads media uploader
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script(
                    'scheduled-modal-admin',
                    plugins_url('assets/js/scheduled-modal-admin.min.js', __FILE__),
                    ['jquery'],
                    '1.0',
                    true
            );
            wp_enqueue_style(
                    'scheduled-modal-style-admin',
                    plugins_url('assets/css/scheduled-modal-admin.min.css', __FILE__),
                    [],
                    '1.0'
            );

            // We need this for the preview
            wp_enqueue_style(
                    'scheduled-modal-style',
                    plugins_url('assets/css/scheduled-modal.min.css', __FILE__),
                    [],
                    '1.0'
            );
        }
    }

    public function enqueue_frontend_scripts(): void
    {
        wp_enqueue_script(
                'scheduled-modal',
                plugins_url('assets/js/scheduled-modal.min.js', __FILE__),
                [],
                '1.0',
                true
        );

        wp_enqueue_style(
                'scheduled-modal-style',
                plugins_url('assets/css/scheduled-modal.min.css', __FILE__),
                [],
                '1.0'
        );
    }

    /**
     * Enqueue modal HTML + JS
     */
    public function render_active_modal(): void
    {
        $now = current_time('Y-m-d H:i:s');

        // Query modals that are currently active.
        // Intentionally using a meta_query here to find the first
        // active modal (start <= now && end >= now). Limit of 1 and
        // an inequality on the datetime fields keeps it efficient.
        // @noinspection WordPress.DB.SlowDBQuery.slow_db_query_meta_query
        $args = [
                'post_type'      => 'kwp_scheduled_modal',
                'meta_query'     => [
                        [
                                'key'     => '_kwp_modal_start_time',
                                'value'   => $now,
                                'compare' => '<=',
                                'type'    => 'DATETIME',
                        ],
                        [
                                'key'     => '_kwp_modal_end_time',
                                'value'   => $now,
                                'compare' => '>=',
                                'type'    => 'DATETIME',
                        ],
                ],
                'orderby'        => 'meta_value',
                'meta_key'       => '_kwp_modal_start_time',
                'order'          => 'ASC',
                'posts_per_page' => 1,
        ];

        $modals = get_posts($args);

        if (!$modals) {
            if (is_admin()) {
                $js = sprintf("console.info('%s');", esc_js('No active modals found.'));
                wp_add_inline_script('scheduled-modal', $js, 'before');
            }

            return;
        }

        $modal = $modals[0];

        $current_url = $this->getCurrentUrl();
        $internal_target = (int)get_post_meta($modal->ID, '_kwp_modal_target_post_id', true);
        $external_target = (string)get_post_meta($modal->ID, '_kwp_modal_target_url', true);
        $menu_item_class = get_post_meta($modal->ID, '_kwp_modal_menu_item_class', true);
        $menu_item_label = (string)get_post_meta($modal->ID, '_kwp_modal_menu_item_label', true);

        if (!empty($internal_target)) {
            $button_href = get_permalink($internal_target);
        } else {
            $button_href = esc_url($external_target);
        }

        $on_target_page = !empty($button_href) && trailingslashit($button_href) == trailingslashit($current_url);

        if (!empty($menu_item_label) && !$on_target_page) {
            // Get the first menu. Hopefully it's the primary menu.
            $menu_id = wp_get_nav_menu_object(current(array_filter(get_nav_menu_locations())))?->slug;

            if ($menu_id) {
                $js = "const kwpModalMenuParams = " . json_encode([
                                'id'    => 'menu-' . $menu_id,
                                'class' => $menu_item_class ?? '',
                                'href'  => $button_href ?? '',
                                'label' => $menu_item_label,
                        ]) . ";";
                wp_add_inline_script('scheduled-modal', $js, 'before');
            }
        }

        // Allow the cookie to expire immediately if the modal is updated.
        $cookie_name = '_kwp_modal-' . sha1($modal->ID . '__' . $modal->post_modified_gmt);
        $cookie_value = sanitize_text_field(wp_unslash($_COOKIE[$cookie_name] ?? ''));

        if (
                !empty($cookie_value) &&
                stripos($cookie_value, 'suppressed_until:') !== false
        ) {
            $expiry = (int)strtotime(str_ireplace('suppressed_until:', '', $cookie_value)) * 1000;
            $js = sprintf(
                    "const t = new Date(%d).toLocaleString(); console.log('Scheduled Modal was suppressed until ' + t);",
                    $expiry
            );
            wp_add_inline_script('scheduled-modal', $js, 'before');

            return; // The modal was dismissed by the user; do not show it again until the cookie expires.
        }

        $bg_image = get_post_meta($modal->ID, '_kwp_modal_bg_image', true);
        $button_text = get_post_meta($modal->ID, '_kwp_modal_button_text', true);
        $callout = get_post_meta($modal->ID, '_kwp_modal_callout', true);
        $delay = intval(get_post_meta($modal->ID, '_kwp_modal_delay', true));
        $end_time = get_post_meta($modal->ID, '_kwp_modal_end_time', true);
        $extra = get_post_meta($modal->ID, '_kwp_modal_extra', true);
        $supporting = get_post_meta($modal->ID, '_kwp_modal_supporting', true);
        $suppress = intval(get_post_meta($modal->ID, '_kwp_modal_suppress', true));
        $target_attr = '';
        $text_color = get_post_meta($modal->ID, '_kwp_modal_text_color', true);
        $text_shadow = get_post_meta($modal->ID, '_kwp_modal_text_shadow', true);
        $title = get_the_title($modal->ID);

        if ($suppress < 1) {
            $suppress = self::SUPPRESS_DAYS_DEFAULT; // default to 30 days if invalid
        }

        if ($delay < 0) {
            $delay = self::DELAY_SECONDS_DEFAULT; // default to 3 seconds if invalid
        }

        $suppress *= 1000 * 60 * 60 * 24; // days to milliseconds
        $delay *= 1000; // seconds to milliseconds
        $gradient = get_post_meta($modal->ID, '_kwp_modal_gradient', true);
        $gradient = json_decode($gradient, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $gradient = self::DEFAULT_GRADIENT;
        }

        $args = [
                'bg_image'     => $bg_image ? wp_get_attachment_url($bg_image) : null,
                'button_color' => get_post_meta($modal->ID, '_kwp_modal_button_color', true) ?? null,
                'button_href'  => $button_href,
                'button_text'  => $button_text,
                'callout'      => apply_filters('the_content', $callout),
                'cookie_name'  => $cookie_name,
                'cookie_value' => $cookie_value ?? null,
                'current_url'  => $current_url,
                'delay'        => $delay,
                'ends'         => (int)strtotime($end_time) * 1000, // JS timestamp
                'extra'        => apply_filters('the_content', $extra),
                'gradient'     => $this->gradientArrayToString($gradient),
                'id'           => $modal->ID,
                'supporting'   => apply_filters('the_content', $supporting),
                'suppress'     => $suppress,
                'target_attr'  => $target_attr,
                'text_color'   => $text_color ?? 'kwp-modal-text-light',
                'text_shadow'  => $text_shadow ?? '0',
                'title'        => $title,
        ];

        if (current_user_can('manage_options')) {
            $js = 'console.table(' . json_encode($args) . ');';
            wp_add_inline_script('scheduled-modal', $js, 'after');
        }

        if (!$on_target_page) {
            // Determine target attribute for external links
            $site_url = trailingslashit(home_url());
            $args['target_attr'] = !str_starts_with($button_href, $site_url);
            $this->render_view('template', $args);
        } else {
            $js = sprintf(
                    "console.warn('Scheduled Modal button link (%s) matches the current URL (%s); modal will not display.');",
                    $button_href,
                    $current_url
            );
            wp_add_inline_script('scheduled-modal', $js, 'before');
        }
    }

    private function parseGradient($gradient)
    {
        $default = self::DEFAULT_GRADIENT;
        $gradient = json_decode($gradient, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($gradient)) {
                if (empty($gradient['primary']) || !preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $gradient['primary'])) {
                    $gradient['primary'] = $default['primary'];
                }

                if (empty($gradient['secondary']) || !preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $gradient['secondary'])) {
                    $gradient['secondary'] = $default['secondary'];
                }

                if (empty($gradient['direction']) || !in_array($gradient['direction'], ['to right', 'to left', 'to top', 'to bottom'], true)) {
                    $gradient['direction'] = $default['direction'];
                }

                if (empty($gradient['opacity']) || !is_numeric($gradient['opacity']) || $gradient['opacity'] < 0 || $gradient['opacity'] > 1) {
                    $gradient['opacity'] = $default['opacity'];
                }
            } else {
                $gradient = $default;
            }
        } else {
            $gradient = $default;
        }

        return $gradient;
    }

    private function gradientArrayToString($gradient): string
    {
        if (empty($gradient) || !is_array($gradient)) {
            return '';
        }

        $primary = $gradient['primary'] ?? self::DEFAULT_GRADIENT['primary'];
        $secondary = $gradient['secondary'] ?? self::DEFAULT_GRADIENT['secondary'];
        $direction = $gradient['direction'] ?? self::DEFAULT_GRADIENT['direction'];
        $opacity = isset($gradient['opacity']) && is_numeric($gradient['opacity']) ? floatval($gradient['opacity']) : self::DEFAULT_GRADIENT['opacity'];

        return "linear-gradient($direction, " . $this->hexToRgba($primary, $opacity) . ", " . $this->hexToRgba($secondary, $opacity) . ")";
    }

    private function hexToRgba($primary, float $opacity): string
    {
        $primary = ltrim($primary, '#');

        if (strlen($primary) === 3) {
            $r = hexdec(str_repeat(substr($primary, 0, 1), 2));
            $g = hexdec(str_repeat(substr($primary, 1, 1), 2));
            $b = hexdec(str_repeat(substr($primary, 2, 1), 2));
        } elseif (strlen($primary) === 6) {
            $r = hexdec(substr($primary, 0, 2));
            $g = hexdec(substr($primary, 2, 2));
            $b = hexdec(substr($primary, 4, 2));
        } else {
            // Invalid hex color
            return 'rgba(0,0,0,' . $opacity . ')';
        }

        return "rgba($r,$g,$b,$opacity)";
    }

    private function getCurrentUrl(): string
    {
        // Construct the current URL, with segments, but without any query parameters or fragments
        $scheme = 'http' . (is_ssl() ? 's' : '');
        $host = sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'] ?? ''));
        $request_uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
        $path = wp_parse_url($request_uri, PHP_URL_PATH) ?? '';

        return esc_url_raw(trailingslashit("$scheme://$host$path"));
    }
}

new ScheduledModal();
