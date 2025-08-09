<?php
/*
Plugin Name: WP Shadow Links
Plugin URI: https://github.com/meksone/WP-Shadow-Links
Description: Obfuscates email addresses and generates functional mailto links with optional icons and copy-to-clipboard buttons.
Version: 0.1.0
Author: meksONE
Author URI: https://meksone.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mk-shadow-links
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Shadow Links JavaScript and Font Awesome.
 *
 * This function registers and enqueues the main email-link-generator.js script
 * and also ensures Font Awesome is loaded, as the script relies on it for icons.
 */
function shadow_links_enqueue_scripts() {
    // Enqueue Font Awesome for icons
    // Check if Font Awesome is already enqueued by the theme or another plugin
    // to avoid duplicates. We're using a common CDN URL for simplicity.
    if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
            array(), // No dependencies
            '6.0.0-beta3', // Version of Font Awesome
            'all' // Media type
        );
    }

    // Enqueue the main email-link-generator.js script
    wp_enqueue_script(
        'shadow-links-generator', // Unique handle for the script
        plugins_url( 'js/email-link-generator.js', __FILE__ ), // Path to the script
        array(), // Dependencies (e.g., array('jquery') if it relied on jQuery)
                 // The script handles DOMContentLoaded internally, so no specific WP dependency needed.
        '0.1.8', // Version number of the email-link-generator.js script
        true // Load in the footer for better performance
    );
}
add_action( 'wp_enqueue_scripts', 'shadow_links_enqueue_scripts' );

// Optional: Add a simple shortcode for demonstration or specific use cases
// This is just an example; the primary functionality is automatic via data attributes.
function shadow_links_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'email' => '',
        'domain' => '',
        'subject' => '',
        'body' => '',
        'class' => '',
        'target' => '',
        'title' => '',
        'copylink' => '',
        'copylink_title' => '',
        'copylink_icon' => '',
        'icon' => '',
        'linkwrapper' => '',
        'text' => '',
    ), $atts, 'shadow_link' );

    if ( empty( $atts['email'] ) || empty( $atts['domain'] ) ) {
        return '<!-- Shadow Links: Missing email or domain -->';
    }

    $data_attributes = '';
    foreach ( $atts as $key => $value ) {
        if ( ! empty( $value ) && $key !== 'text' ) {
            $data_attributes .= ' data-' . esc_attr( str_replace( '_', '-', $key ) ) . '="' . esc_attr( $value ) . '"';
        }
    }

    $output = '<div' . $data_attributes . '>';
    $output .= '<span class="' . esc_attr( $atts['class'] ) . '">' . esc_html( $atts['text'] ?: $atts['email'] . '@' . $atts['domain'] ) . '</span>';
    $output .= '</div>';

    return $output;
}
add_shortcode( 'shadow_link', 'shadow_links_shortcode' );

?>