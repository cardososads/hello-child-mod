<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

require_once get_stylesheet_directory_uri() . 'classes/class-numerology-calculator.php';

function render_and_calculate_destiny_number($atts) {
    $elementor_form_id = $atts['id']; // ID do formulário do Elementor

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name']) && isset($_POST['birth_date'])) {
        $calculator = new NumerologyCalculator();
        $firstName = sanitize_text_field($_POST['first_name']);
        $birthDate = sanitize_text_field($_POST['birth_date']);
        $destinyNumber = $calculator->calculateDestinyNumber($firstName, $birthDate);
        var_dump($destinyNumber); // Exibe o resultado
    }

    return \Elementor\Plugin::instance()->frontend->get_builder_content($elementor_form_id, true);
}
add_shortcode('render_destiny_form', 'render_and_calculate_destiny_number');
