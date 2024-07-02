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

// Corrigido para usar get_stylesheet_directory() em vez de get_stylesheet_directory_uri()
require_once get_stylesheet_directory() . '/classes/class-numerology-calculator.php';


// Hook para processar o envio do formulário "Form1"
add_action('elementor_pro/forms/new_record', function ($record, $handler) {
    // Verifique se o formulário é 'Form1'
    $form_name = $record->get_form_settings('form_name');
    if ('Form1' !== $form_name) {
        return;
    }

    // Obtenha os dados do formulário
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($raw_fields as $id => $field) {
        $fields[$id] = $field['value'];
    }

    // Armazena os dados do formulário usando transients para acesso global
    set_transient('form1_submission_data', $fields, 60 * 60); // Armazena por 1 hora

}, 10, 2);

function mostrar_form1_submission_data()
{
    // Obtenha os dados armazenados
    $fields = get_transient('form1_submission_data');

    if ($fields) {
        // Mostre os dados do formulário "Form1"
        ob_start();
        echo '<h2>Dados do Formulário 1:</h2>';
        echo '<p>Primeiro Nome: ' . esc_html($fields['first_name']) . '</p>';
        echo '<p>Data de Nascimento: ' . esc_html($fields['birth_date']) . '</p>';
        // Aqui você pode adicionar mais campos conforme necessário
        return ob_get_clean();
    } else {
        return 'Nenhuma submissão recente de formulário encontrada.';
    }
}

add_shortcode('mostrar_form1_dados', 'mostrar_form1_submission_data');


