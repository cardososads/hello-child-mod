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

$elementor_form_id = 'Form1'; // Substitua pelo ID real do formulário do Elementor

function render_and_calculate_destiny_number($atts) {
    ob_start();

    // Renderize o formulário do Elementor
    $elementor_form_id = $atts['id']; // ID do formulário do Elementor
    echo \Elementor\Plugin::instance()->frontend->get_builder_content($elementor_form_id, true);

    // Verifique se há dados de sessão
    if (isset($_SESSION['name_number']) && isset($_SESSION['birth_number'])) {
        echo '<h2>Resultados:</h2>';
        echo '<p>Número do Nome: ' . $_SESSION['name_number'] . '</p>';
        echo '<p>Número do Destino: ' . $_SESSION['birth_number'] . '</p>';

        // Limpar os dados da sessão
        unset($_SESSION['name_number']);
        unset($_SESSION['birth_number']);
    }

    return ob_get_clean();
}
add_shortcode('render_destiny_form', 'render_and_calculate_destiny_number');

// Hook para processar o envio do formulário
add_action('elementor_pro/forms/new_record', function($record, $handler) {
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

    // Armazene os dados do formulário em uma opção temporária
    set_transient('form1_submission_data', $fields, 60 * 60); // Armazena por 1 hora

    // Calcula o número de destino
    $calculator = new NumerologyCalculator();
    $firstName = sanitize_text_field($fields['first_name']);
    $birthDate = sanitize_text_field($fields['birth_date']);
    $destinyNumber = $calculator->calculateDestinyNumber($firstName, $birthDate);

    // Armazene os resultados na sessão
    $_SESSION['name_number'] = $firstName;
    $_SESSION['birth_number'] = $destinyNumber;

}, 10, 2);

function mostrar_form1_submission_data() {
    // Obtenha os dados armazenados
    $fields = get_transient('form1_submission_data');

    if ($fields) {
        // Mostre os dados usando var_dump
        ob_start();
        echo '<pre>';
        var_dump($fields);
        echo '</pre>';
        return ob_get_clean();
    } else {
        return 'Nenhuma submissão recente de formulário encontrada.';
    }
}
add_shortcode('mostrar_form1_dados', 'mostrar_form1_submission_data');
