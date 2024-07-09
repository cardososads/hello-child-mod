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

//--------------------------------------------------------------------------------------------------


// Hook para processar o envio dos formulários
add_action('elementor_pro/forms/new_record', function ($record, $handler) {
    // Verifique qual formulário foi enviado
    $form_name = $record->get_form_settings('form_name');

    // Obtenha os dados do formulário
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($raw_fields as $id => $field) {
        $fields[$id] = $field['value'];
    }

    // Instancia a classe de cálculo
    require_once get_stylesheet_directory() . '/classes/class-numerology-calculator.php';
    $calculator = new NumerologyCalculator();

    // Armazena os dados do formulário usando transients para acesso global
    switch ($form_name) {
        case 'Form1':
            // Realiza o cálculo do número de destino
            $fields['destiny_number'] = $calculator->calculateDestinyNumber($fields['birth_date']);
            set_transient('form1_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
        case 'Form2':
            // Realiza o cálculo do número de expressão
            $fields['expression_number'] = $calculator->calculateExpressionNumber($fields['full_name']);
            set_transient('form2_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
        case 'Form3':
            // Realiza o cálculo do número de motivação
            $fields['motivation_number'] = $calculator->calculateMotivationNumber($fields['email']);
            set_transient('form3_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
    }

}, 10, 2);

function mostrar_form_submission_data($form_id)
{
    // Obtenha os dados armazenados
    $fields = get_transient($form_id . '_submission_data');

    if ($fields) {
        // Mostre os dados do formulário
        ob_start();
        echo '<h2>Dados do ' . esc_html($form_id) . ':</h2>';
        foreach ($fields as $key => $value) {
            echo '<p>' . esc_html(ucwords(str_replace('_', ' ', $key))) . ': ' . esc_html($value) . '</p>';
        }
        return ob_get_clean();
    } else {
        return 'Nenhuma submissão recente de formulário encontrada.';
    }
}

// Shortcodes para exibir os dados dos formulários
add_shortcode('mostrar_form1_dados', function () {
    return mostrar_form_submission_data('form1');
});

add_shortcode('mostrar_form2_dados', function () {
    return mostrar_form_submission_data('form2');
});

add_shortcode('mostrar_form3_dados', function () {
    return mostrar_form_submission_data('form3');
});

// Incluir as classes necessárias
require_once get_stylesheet_directory() . '/classes/class-audio-manager.php';
require_once get_stylesheet_directory() . '/classes/class-audio-player.php';

function custom_audio_introductions_shortcode() {
    // Instancia o AudioManager
    $audio_manager = new AudioManager();

    // Obter número de destino da transiente
    $form1_data = get_transient('form1_submission_data');
    $destiny_number = !empty($form1_data['destiny_number']) ? $form1_data['destiny_number'] : null;

    // Obtém os áudios introdutórios e do número de destino
    $introductions = $audio_manager->getIntroductions();
    $destiny_audios = $audio_manager->getDestinyAudios($destiny_number);

    $output = '<div class="audio-player-container">';
    $audio_index = 0;

    // Função para renderizar áudio
    $renderAudioPlayer = function ($audio, $audio_index) {
        $html = '';
        $html .= '<div class="audio-player" id="audio-player-' . $audio_index . '" ' . ($audio_index > 0 ? 'style="display:none;"' : '') . '>';
        $html .= '<audio controls ' . ($audio_index === 0 ? 'autoplay' : '') . '>';
        $html .= '<source src="' . $audio['src'] . '" type="audio/mpeg">';
        $html .= '</audio>';
        $html .= '</div>';
        return $html;
    };

    // Renderiza os áudios de introdução
    foreach ($introductions as $key => $audio) {
        $output .= $renderAudioPlayer($audio, $audio_index);

        if (!empty($audio['subtitles'])) {
            // Incluir o script de legendas JS diretamente
            $output .= '<script>';
            $output .= $audio['subtitles'];
            $output .= '</script>';
        }

        $audio_index++;
    }

    // Renderiza o áudio do número de destino específico
    if ($destiny_number !== null && isset($destiny_audios[$destiny_number])) {
        $audio = $destiny_audios[$destiny_number];
        $output .= $renderAudioPlayer($audio, $audio_index);

        if (!empty($audio['subtitles'])) {
            // Incluir o script de legendas JS diretamente
            $output .= '<script>';
            $output .= $audio['subtitles'];
            $output .= '</script>';
        }

        $audio_index++;
    }

    $output .= '</div>';

    // Adiciona script para gerenciar a reprodução em sequência
    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const players = document.querySelectorAll(".audio-player audio");
            for (let i = 0; i < players.length; i++) {
                players[i].addEventListener("ended", function() {
                    if (i + 1 < players.length) {
                        document.getElementById("audio-player-" + i).style.display = "none";
                        document.getElementById("audio-player-" + (i + 1)).style.display = "block";
                        players[i + 1].play();
                    }
                });
            }
        });
    </script>';

    return $output;
}
add_shortcode('custom_audio_introductions', 'custom_audio_introductions_shortcode');
