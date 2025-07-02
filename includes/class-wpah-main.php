<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WPAH_Main' ) ) {
class WPAH_Main {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_filter( 'the_content', [ $this, 'inject_age_badge' ] );
        load_plugin_textdomain( 'wpah', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'wpah-style', WPAH_URL . 'assets/css/wpah-style.css', [], WPAH_VERSION );
    }

    public function inject_age_badge( $content ) {
        if ( ! is_singular() ) return $content;

        $post_types = (array) get_option( 'wpah_post_types', [ 'post', 'page' ] );
        if ( ! in_array( get_post_type(), $post_types, true ) ) return $content;

        $use_modified = get_option( 'wpah_use_modified', 0 );
        $post = get_post();
        $date = $use_modified ? strtotime( $post->post_modified ) : strtotime( $post->post_date );

        $threshold = absint( get_option( 'wpah_threshold', 24 ) ); // months
        $now = current_time( 'timestamp' );
        $age_months = floor( ( $now - $date ) / (30*24*60*60) );

        if ( $age_months < $threshold ) return $content; // Not old enough

        $msg = get_option( 'wpah_message', __( 'This content is over %s old and may be outdated.', 'wpah' ) );
        $unit = $threshold >= 12 ? __( 'years', 'wpah' ) : __( 'months', 'wpah' );
        $val = $threshold >= 12 ? round($threshold/12,1) : $threshold;
        $badge_msg = sprintf( esc_html( $msg ), esc_html( "$val $unit" ) );

        $badge_style = esc_attr( get_option( 'wpah_badge_style', 'default' ) );
        $position = esc_attr( get_option( 'wpah_badge_position', 'top' ) );

        $badge = '<div class="wpah-badge wpah-style-' . $badge_style . ' wpah-pos-' . $position . '">'
            . esc_html( $badge_msg ) .
            '</div>';

        if ( $position === 'top' ) {
            return $badge . $content;
        } else {
            return $content . $badge;
        }
    }
}
new WPAH_Main();
}
