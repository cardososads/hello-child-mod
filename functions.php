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

function form_shortcode() {
    ob_start();

    // Obtém os dados dos áudios
    $audios_data = get_option('_audios');
    ?>

    <div id="text"></div>

    <?php
    // Verifica o slug da página para determinar o formulário do Elementor
    global $post;
    $slug = $post->post_name;

    // Recupera o número de destino do transient
    $birth_number = '';
    if ($slug !== 'form-01') { // Ajuste aqui com o slug correto do primeiro formulário
        $form1_data = get_transient('form1_submission_data');
        if ($form1_data && isset($form1_data['destiny_number'])) {
            $birth_number = intval($form1_data['destiny_number']); // Converte para inteiro
        }
    }

    // Determina se o áudio de destino deve ser carregado
    $load_destiny_audio = false;
    if ($slug === 'form-02' && !empty($birth_number) && isset($audios_data['numeros']['item-' . ($birth_number - 1)]['_audio_do_numero'])) {
        $load_destiny_audio = true;
    }

    // Script JS para controlar a reprodução e legendas
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php echo stripslashes($audios_data['_legenda-intro']); ?>;
            <?php echo stripslashes($audios_data['_legenda-pos-intro']); ?>;
            <?php echo isset($birth_number) && isset($audios_data['numeros']['item-' . ($birth_number - 1)]['_legenda_do_audio']) ? stripslashes($audios_data['numeros']['item-' . ($birth_number - 1)]['_legenda_do_audio']) : ''; ?>;

            const audioPlayer = document.getElementById('audioPlayer');
            const textDiv = document.getElementById('text');
            let timeoutIDs = [];

            const playAudio = (audioSource) => {
                audioPlayer.src = audioSource;
                audioPlayer.play().catch(error => {
                    console.log('Autoplay foi bloqueado. Tentando novamente.');
                    setTimeout(() => playAudio(audioSource), 1000);
                });
            };

            // Função para lidar com legendas
            const handleSubtitles = (subtitles) => {
                timeoutIDs.forEach(id => clearTimeout(id));  // Limpa timeouts anteriores
                timeoutIDs = [];

                subtitles.forEach(subtitle => {
                    const timeoutID = setTimeout(() => {
                        textDiv.textContent = subtitle.text;
                    }, subtitle.time * 1000);
                    timeoutIDs.push(timeoutID);
                });
            };

            // Quando o áudio terminar, limpa o texto das legendas
            audioPlayer.addEventListener('ended', () => {
                textDiv.textContent = "";
            });

            // Adicionar eventos de legendas para o primeiro áudio
            audioPlayer.addEventListener('play', () => handleSubtitles(subtitles));

            <?php if ($slug === 'form-02') : ?>
            // Adicionar legendas para o segundo áudio, se necessário
            audioPlayer.addEventListener('play', () => handleSubtitles(secondSubtitles));
            <?php endif; ?>

            // Eventos para pausar legendas ao pausar o áudio
            audioPlayer.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

            // Carregar o primeiro áudio ao carregar a página
            playAudio('<?php echo esc_url($audios_data['_audio-introdutorio']); ?>');

            <?php if ($slug === 'form-02') : ?>
            // Quando o primeiro áudio terminar, carrega o segundo áudio
            audioPlayer.addEventListener('ended', () => {
                playAudio('<?php echo esc_url($audios_data['_pos-intro']); ?>');
            });

            // Quando o segundo áudio terminar, carrega o áudio de destino, se necessário
            audioPlayer.addEventListener('ended', () => {
                <?php if ($load_destiny_audio) : ?>
                playAudio('<?php echo esc_url($audios_data['numeros']['item-' . ($birth_number - 1)]['_audio_do_numero']); ?>');
                <?php endif; ?>
            });
            <?php endif; ?>
        });
    </script>

    <!-- Elemento de áudio único para todos os players -->
    <audio id="audioPlayer" controls autoplay style="width: 100%; display: block;">
        <!-- Fallback para navegadores que não suportam áudio HTML5 -->
        Your browser does not support the audio element.
    </audio>

    <?php

    // Adicione o formulário do Elementor com o ID desejado
    $elementor_form_id = 'Form1'; // Substitua pelo ID real do formulário do Elementor
    echo \Elementor\Plugin::instance()->frontend->get_builder_content($elementor_form_id, true);

    return ob_get_clean();
}

// Função para registrar o shortcode
function register_custom_shortcode() {
    add_shortcode('custom_form_shortcode', 'form_shortcode');
}
add_action('init', 'register_custom_shortcode');
