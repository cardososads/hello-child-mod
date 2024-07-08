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
    <pre><?php var_dump($audios_data); ?></pre>
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
            $birth_number = $form1_data['destiny_number'];
        }
    }

    if ($slug === 'form-02') {
        // Se for o formulário form-02, exibe os três áudios
        ?>
        <audio id="audioIntrodutorio" controls autoplay style="width: 100%">
            <source src="<?php echo esc_url($audios_data['_audio-introdutorio']); ?>" type="audio/mpeg">
        </audio>
        <audio id="entradaDestino" controls style="width: 100%; display: none;">
            <source src="<?php echo esc_url($audios_data['_pos-intro']); ?>" type="audio/mpeg">
        </audio>
        <?php if (!empty($birth_number) && isset($audios_data['numeros']['item-' . $birth_number]['_audio_do_numero'])) : ?>
            <audio id="audioDestino" controls style="width: 100%; display: none;">
                <source src="<?php echo esc_url($audios_data['numeros']['item-' . $birth_number]['_audio_do_numero']); ?>" type="audio/mpeg">
            </audio>
        <?php endif; ?>
        <?php
    } else {
        // Caso contrário, exibe apenas os dois áudios padrão
        ?>
        <audio id="audioIntrodutorio" controls autoplay style="width: 100%">
            <source src="<?php echo esc_url($audios_data['_audio-introdutorio']); ?>" type="audio/mpeg">
        </audio>
        <audio id="entradaDestino" controls style="width: 100%; display: none;">
            <source src="<?php echo esc_url($audios_data['_pos-intro']); ?>" type="audio/mpeg">
        </audio>
        <?php
    }

    // Script JS para controlar a reprodução e legendas
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php echo stripslashes($audios_data['_legenda-intro']); ?>;
            <?php echo stripslashes($audios_data['_legenda-pos-intro']); ?>;
            <?php echo isset($birth_number) && isset($audios_data['numeros']['item-' . $birth_number]['_legenda_do_audio']) ? stripslashes($audios_data['numeros']['item-' . $birth_number]['_legenda_do_audio']) : ''; ?>;

            const audio = document.getElementById('audioIntrodutorio');
            const secondAudio = document.getElementById('entradaDestino');
            const destinyAudio = document.getElementById('audioDestino');
            const textDiv = document.getElementById('text');
            let timeoutIDs = [];

            const playAudio = (audioElement) => {
                setTimeout(() => {
                    audioElement.play().catch(error => {
                        console.log('Autoplay foi bloqueado. Tentando novamente.');
                        setTimeout(() => playAudio(audioElement), 1000);
                    });
                }, 1000);
            };

            playAudio(audio);

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

            audio.addEventListener('play', () => handleSubtitles(subtitles));
            secondAudio.addEventListener('play', () => handleSubtitles(secondSubtitles));
            destinyAudio.addEventListener('play', () => handleSubtitles(destinySubtitles));

            audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
            secondAudio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
            destinyAudio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

            audio.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                const currentTime = audio.currentTime;
                subtitles.forEach(subtitle => {
                    if (subtitle.time >= currentTime) {
                        const timeoutID = setTimeout(() => {
                            textDiv.textContent = subtitle.text;
                        }, (subtitle.time - currentTime) * 1000);
                        timeoutIDs.push(timeoutID);
                    }
                });
            });

            secondAudio.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                const currentTime = secondAudio.currentTime;
                secondSubtitles.forEach(subtitle => {
                    if (subtitle.time >= currentTime) {
                        const timeoutID = setTimeout(() => {
                            textDiv.textContent = subtitle.text;
                        }, (subtitle.time - currentTime) * 1000);
                        timeoutIDs.push(timeoutID);
                    }
                });
            });

            destinyAudio.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                const currentTime = destinyAudio.currentTime;
                destinySubtitles.forEach(subtitle => {
                    if (subtitle.time >= currentTime) {
                        const timeoutID = setTimeout(() => {
                            textDiv.textContent = subtitle.text;
                        }, (subtitle.time - currentTime) * 1000);
                        timeoutIDs.push(timeoutID);
                    }
                });
            });

            audio.addEventListener('ended', () => {
                textDiv.textContent = "";
                secondAudio.style.display = 'block';
                playAudio(secondAudio);
            });

            secondAudio.addEventListener('ended', () => {
                textDiv.textContent = "";
                destinyAudio.style.display = 'block';
                playAudio(destinyAudio);
            });

            destinyAudio.addEventListener('ended', () => {
                textDiv.textContent = "";
            });

            // Mostra e esconde players conforme necessário
            secondAudio.addEventListener('play', () => {
                audio.style.display = 'none';
                audio.pause(); // Pausa o primeiro áudio caso esteja tocando
            });

            destinyAudio.addEventListener('play', () => {
                audio.style.display = 'none';
                secondAudio.style.display = 'none';
                audio.pause(); // Pausa o primeiro áudio caso esteja tocando
                secondAudio.pause(); // Pausa o segundo áudio caso esteja tocando
            });

            // Mostra o primeiro player novamente se necessário
            secondAudio.addEventListener('ended', () => {
                audio.style.display = 'block';
            });

            destinyAudio.addEventListener('ended', () => {
                audio.style.display = 'block';
            });
        });
    </script>
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
