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

function process_form_data() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $calculator = new NumerologyCalculator();

        // Verifique qual formulário foi enviado e colete os dados apropriados
        if (isset($_POST['first_name']) && isset($_POST['birth_date'])) {
            $firstName = sanitize_text_field($_POST['first_name']);
            $birthDate = sanitize_text_field($_POST['birth_date']);
            $destinyNumber = $calculator->calculateDestinyNumber($firstName, $birthDate);
            // Salvar ou redirecionar com o resultado
        } elseif (isset($_POST['full_name'])) {
            $fullName = sanitize_text_field($_POST['full_name']);
            $expressionNumber = $calculator->calculateExpressionNumber($fullName);
            // Salvar ou redirecionar com o resultado
        } elseif (isset($_POST['email']) && isset($_POST['marital_status'])) {
            $fullName = sanitize_text_field($_POST['full_name']);
            $email = sanitize_email($_POST['email']);
            $maritalStatus = sanitize_text_field($_POST['marital_status']);
            $motivationNumber = $calculator->calculateMotivationNumber($fullName);
            // Salvar ou redirecionar com o resultado
        }
    }
}

add_shortcode('process_form', 'process_form_data');
