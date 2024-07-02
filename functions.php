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

require_once get_template_directory() . 'classes/class-numerology-calculator.php';

function calculate_destiny_number() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name']) && isset($_POST['birth_date'])) {
        $calculator = new NumerologyCalculator();
        $firstName = sanitize_text_field($_POST['first_name']);
        $birthDate = sanitize_text_field($_POST['birth_date']);
        $destinyNumber = $calculator->calculateDestinyNumber($firstName, $birthDate);
        var_dump($destinyNumber); // Exibe o resultado
    }
}
add_shortcode('calculate_destiny_number', 'calculate_destiny_number');

function calculate_expression_number() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'])) {
        $calculator = new NumerologyCalculator();
        $fullName = sanitize_text_field($_POST['full_name']);
        $expressionNumber = $calculator->calculateExpressionNumber($fullName);
        var_dump($expressionNumber); // Exibe o resultado
    }
}
add_shortcode('calculate_expression_number', 'calculate_expression_number');

function calculate_motivation_number() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'])) {
        $calculator = new NumerologyCalculator();
        $fullName = sanitize_text_field($_POST['full_name']);
        $motivationNumber = $calculator->calculateMotivationNumber($fullName);
        var_dump($motivationNumber); // Exibe o resultado
    }
}
add_shortcode('calculate_motivation_number', 'calculate_motivation_number');
