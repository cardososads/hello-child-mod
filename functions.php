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

function exibir_audios_com_legendas_shortcode() {
    ob_start();

    // Recupera os dados da página de opções
    $audios_data = get_option('_audios');

    // Verifica se existem dados
    if (!$audios_data) {
        return 'Nenhum áudio encontrado.';
    }

    ?>
    <div id="audio-legenda" style="display: none;"></div>
    <div id="audios-container">
        <?php
        foreach ($audios_data as $key => $value) {
            if (strpos($key, '_audio') !== false) {
                echo '<audio controls autoplay style="width: 100%;" data-legenda-key="' . esc_attr($key) . '">';
                echo '<source src="' . esc_url($value) . '" type="audio/mpeg">';
                echo 'Seu navegador não suporta o elemento de áudio.';
                echo '</audio>';
            }
        }
        ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const audioElements = document.querySelectorAll('audio[data-legenda-key]');
            const legendaElement = document.getElementById('audio-legenda');
            let timeoutIDs = [];

            const handleSubtitles = (subtitles) => {
                timeoutIDs.forEach(id => clearTimeout(id));  // Clear previous timeouts
                timeoutIDs = [];

                subtitles.forEach(subtitle => {
                    const timeoutID = setTimeout(() => {
                        legendaElement.textContent = subtitle.text;
                        legendaElement.style.display = "block";
                    }, subtitle.time * 1000);
                    timeoutIDs.push(timeoutID);
                });
            };

            audioElements.forEach(audio => {
                const legendaKey = audio.getAttribute('data-legenda-key').replace('_audio', '_legenda');
                const subtitles = <?php echo json_encode($audios_data); ?>[legendaKey] || [];

                const parsedSubtitles = subtitles.map(leg => {
                    const parts = leg.split('::');
                    return {
                        time: parseFloat(parts[0]),
                        text: parts[1]
                    };
                });

                const playAudio = (audioElement) => {
                    setTimeout(() => {
                        audioElement.play().catch(error => {
                            console.log('Autoplay foi bloqueado. Tentando novamente.');
                            setTimeout(() => playAudio(audioElement), 1000);
                        });
                    }, 1000);
                };

                playAudio(audio);

                audio.addEventListener('play', () => handleSubtitles(parsedSubtitles));
                audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

                audio.addEventListener('seeked', () => {
                    timeoutIDs.forEach(id => clearTimeout(id));
                    timeoutIDs = [];

                    const currentTime = audio.currentTime;
                    parsedSubtitles.forEach(subtitle => {
                        if (subtitle.time >= currentTime) {
                            const timeoutID = setTimeout(() => {
                                legendaElement.textContent = subtitle.text;
                                legendaElement.style.display = "block";
                            }, (subtitle.time - currentTime) * 1000);
                            timeoutIDs.push(timeoutID);
                        }
                    });
                });

                audio.addEventListener('ended', () => {
                    legendaElement.textContent = "";
                    legendaElement.style.display = "none";
                });
            });
        });
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('exibir_audios_com_legendas', 'exibir_audios_com_legendas_shortcode');


