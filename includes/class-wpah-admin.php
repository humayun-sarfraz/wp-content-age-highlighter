<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WPAH_Admin' ) ) {
class WPAH_Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_menu() {
        add_options_page(
            __( 'Content Age Highlighter', 'wpah' ),
            __( 'Content Age Highlighter', 'wpah' ),
            'manage_options',
            'wpah_settings',
            [ $this, 'settings_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'wpah_settings', 'wpah_threshold', 'absint' );
        register_setting( 'wpah_settings', 'wpah_badge_style', 'sanitize_text_field' );
        register_setting( 'wpah_settings', 'wpah_badge_position', 'sanitize_text_field' );
        register_setting( 'wpah_settings', 'wpah_message', 'sanitize_text_field' );
        register_setting( 'wpah_settings', 'wpah_post_types', [ $this, 'sanitize_types' ] );
        register_setting( 'wpah_settings', 'wpah_use_modified', 'absint' );
    }

    public function sanitize_types( $input ) {
        $types = get_post_types( [ 'public' => true ], 'names' );
        $input = (array) $input;
        return array_values( array_intersect( $input, $types ) );
    }

    public function settings_page() {
        $public_types = get_post_types( [ 'public' => true ], 'objects' );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WP Content Age Highlighter Settings', 'wpah' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'wpah_settings' ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><?php esc_html_e( 'Age Threshold', 'wpah' ); ?></th>
                        <td>
                            <input type="number" min="1" name="wpah_threshold" value="<?php echo absint( get_option('wpah_threshold', 24) ); ?>" style="width:70px;">
                            <span><?php esc_html_e( 'months', 'wpah' ); ?></span>
                            <br>
                            <small><?php esc_html_e( 'Show badge if content is this many months old.', 'wpah' ); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Badge Style', 'wpah' ); ?></th>
                        <td>
                            <select name="wpah_badge_style">
                                <option value="default" <?php selected( get_option('wpah_badge_style', 'default'), 'default' ); ?>><?php esc_html_e( 'Default (Yellow Warning)', 'wpah' ); ?></option>
                                <option value="danger" <?php selected( get_option('wpah_badge_style'), 'danger' ); ?>><?php esc_html_e( 'Danger (Red)', 'wpah' ); ?></option>
                                <option value="info" <?php selected( get_option('wpah_badge_style'), 'info' ); ?>><?php esc_html_e( 'Info (Blue)', 'wpah' ); ?></option>
                                <option value="custom" <?php selected( get_option('wpah_badge_style'), 'custom' ); ?>><?php esc_html_e( 'Custom (match theme)', 'wpah' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Badge Position', 'wpah' ); ?></th>
                        <td>
                            <select name="wpah_badge_position">
                                <option value="top" <?php selected( get_option('wpah_badge_position', 'top'), 'top' ); ?>><?php esc_html_e( 'Top of Content', 'wpah' ); ?></option>
                                <option value="bottom" <?php selected( get_option('wpah_badge_position'), 'bottom' ); ?>><?php esc_html_e( 'Bottom of Content', 'wpah' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Badge Message', 'wpah' ); ?></th>
                        <td>
                            <input type="text" name="wpah_message" style="width:350px" value="<?php echo esc_attr( get_option('wpah_message', __( 'This content is over %s old and may be outdated.', 'wpah' ) ) ); ?>">
                            <br>
                            <small><?php esc_html_e( 'Use %s where you want the age to appear (e.g. “2 years”)', 'wpah' ); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Check Last Modified (not Published) Date', 'wpah' ); ?></th>
                        <td>
                            <input type="checkbox" name="wpah_use_modified" value="1" <?php checked( get_option('wpah_use_modified', 0), 1 ); ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Show On Post Types', 'wpah' ); ?></th>
                        <td>
                            <?php
                            $enabled = (array) get_option( 'wpah_post_types', [ 'post', 'page' ] );
                            foreach ( $public_types as $type ) {
                                printf(
                                    '<label style="margin-right:15px;"><input type="checkbox" name="wpah_post_types[]" value="%s" %s> %s</label>',
                                    esc_attr( $type->name ),
                                    checked( in_array( $type->name, $enabled, true ), true, false ),
                                    esc_html( $type->labels->singular_name )
                                );
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
new WPAH_Admin();
}
