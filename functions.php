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
    <div id="audio-legenda" style="font-size: 16px; margin-top: 10px;"></div>
    <div id="audios-container">
        <audio id="audio-intro" controls autoplay style="width: 100%;">
            <source src="<?php echo esc_url($audios_data['_audio-introdutorio']); ?>" type="audio/mpeg">
            Seu navegador não suporta o elemento de áudio.
        </audio>
        <audio id="audio-pos-intro" controls style="width: 100%; display: none;">
            <source src="<?php echo esc_url($audios_data['_pos-intro']); ?>" type="audio/mpeg">
            Seu navegador não suporta o elemento de áudio.
        </audio>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const audioIntro = document.getElementById('audio-intro');
            const audioPosIntro = document.getElementById('audio-pos-intro');
            const legendaElement = document.getElementById('audio-legenda');
            let timeoutIDs = [];

            const subtitlesIntro = [
                { time: 0, text: "Olá, tudo bem?" },
                { time: 2.5, text: "Nesse momento vamos iniciar nossa jornada de conhecimento," },
                { time: 5.8, text: "entendendo como seu nome, data de nascimento e assinatura" },
                { time: 9.5, text: "revelam muitos aspectos sobre sua vida." },
                { time: 12.7, text: "Com a numerologia cabalística saberemos sobre oportunidades," },
                { time: 16, text: "relacionamento, desafios, e outros fatos que podem te ajudar" },
                { time: 20.5, text: "a ter autoconhecimento e uma visão única e profunda" },
                { time: 23, text: "sobre diversos aspectos da sua existência." }
            ];

            const subtitlesPosIntro = []; // Adicione as legendas de _legenda-pos-intro aqui se houver

            const handleSubtitles = (subtitles) => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                subtitles.forEach(subtitle => {
                    const timeoutID = setTimeout(() => {
                        legendaElement.textContent = subtitle.text;
                    }, subtitle.time * 1000);
                    timeoutIDs.push(timeoutID);
                });
            };

            const playAudio = (audioElement) => {
                setTimeout(() => {
                    audioElement.play().catch(error => {
                        console.log('Autoplay foi bloqueado. Tentando novamente.');
                        setTimeout(() => playAudio(audioElement), 1000);
                    });
                }, 1000);
            };

            playAudio(audioIntro);

            audioIntro.addEventListener('play', () => handleSubtitles(subtitlesIntro));
            audioPosIntro.addEventListener('play', () => handleSubtitles(subtitlesPosIntro));

            audioIntro.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
            audioPosIntro.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

            audioIntro.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                const currentTime = audioIntro.currentTime;
                subtitlesIntro.forEach(subtitle => {
                    if (subtitle.time >= currentTime) {
                        const timeoutID = setTimeout(() => {
                            legendaElement.textContent = subtitle.text;
                        }, (subtitle.time - currentTime) * 1000);
                        timeoutIDs.push(timeoutID);
                    }
                });
            });

            audioPosIntro.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                const currentTime = audioPosIntro.currentTime;
                subtitlesPosIntro.forEach(subtitle => {
                    if (subtitle.time >= currentTime) {
                        const timeoutID = setTimeout(() => {
                            legendaElement.textContent = subtitle.text;
                        }, (subtitle.time - currentTime) * 1000);
                        timeoutIDs.push(timeoutID);
                    }
                });
            });

            audioIntro.addEventListener('ended', () => {
                legendaElement.textContent = "";
                audioPosIntro.style.display = 'block';
                playAudio(audioPosIntro);
            });

            audioPosIntro.addEventListener('ended', () => {
                legendaElement.textContent = "";
            });

            audioPosIntro.addEventListener('play', () => {
                audioIntro.style.display = 'none';
                audioIntro.pause();
            });

            audioPosIntro.addEventListener('ended', () => {
                audioIntro.style.display = 'block';
            });
        });
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('exibir_audios_com_legendas', 'exibir_audios_com_legendas_shortcode');
