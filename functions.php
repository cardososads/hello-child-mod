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
    $audio_intro = get_option('_audios');

    // Pegar o número de destino da sessão
    $birth_number = isset($_SESSION['birth_number']) ? $_SESSION['birth_number'] : '';

    // Verificar o formulário do Elementor na página atual pelo slug
    global $post;
    $slug = $post->post_name;

    // Determinar os áudios com base no formulário Elementor
    $audio_sources = [];
    switch ($slug) {
        case 'form-02':
            // Formulário 02: 3 áudios
            $audio_sources[] = esc_url($audio_intro['_audio-introdutorio']);
            $audio_sources[] = esc_url($audio_intro['_pos-intro']);
            // Adicionar áudio de acordo com o número de destino
            if (!empty($birth_number) && isset($audio_intro['numeros']['item-' . $birth_number])) {
                $audio_sources[] = esc_url($audio_intro['numeros']['item-' . $birth_number]['_audio_do_numero']);
            }
            break;
        default:
            // Outros formulários: Usar áudios padrão
            $audio_sources[] = esc_url($audio_intro['_audio-introdutorio']);
            $audio_sources[] = esc_url($audio_intro['_pos-intro']);
            break;
    }

    ?>
    <div id="text"></div>
    <?php foreach ($audio_sources as $index => $audio_src) : ?>
        <audio id="audio_<?php echo $index; ?>" controls autoplay style="width: 100%">
            <source src="<?php echo $audio_src; ?>" type="audio/mpeg">
        </audio>
    <?php endforeach; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php
            echo stripslashes($audio_intro['_legenda-intro']);
            echo stripslashes($audio_intro['_legenda-pos-intro']);

            // Definir as legendas corretas
            $subtitles = isset($audio_intro['_legenda-intro']) ? json_encode(stripslashes($audio_intro['_legenda-intro'])) : '[]';
            $secondSubtitles = isset($audio_intro['_legenda-pos-intro']) ? json_encode(stripslashes($audio_intro['_legenda-pos-intro'])) : '[]';
            $destinySubtitles = isset($audio_intro['numeros']['item-0']['_legenda_do_audio']) ? json_encode(stripslashes($audio_intro['numeros']['item-0']['_legenda_do_audio'])) : '[]';
            ?>

            const audios = document.querySelectorAll('audio');
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

            audios.forEach(audio => {
                playAudio(audio);

                audio.addEventListener('play', () => {
                    const audioId = audio.id;
                    let subtitles = [];
                    if (audioId === 'audio_0') {
                        subtitles = <?php echo $subtitles; ?>;
                    } else if (audioId === 'audio_1') {
                        subtitles = <?php echo $secondSubtitles; ?>;
                    } else if (audioId === 'audio_2') {
                        subtitles = <?php echo $destinySubtitles; ?>;
                    }

                    handleSubtitles(subtitles);
                });

                audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
                audio.addEventListener('seeked', () => {
                    timeoutIDs.forEach(id => clearTimeout(id));
                    timeoutIDs = [];

                    const currentTime = audio.currentTime;
                    let subtitles = [];
                    if (audio.id === 'audio_0') {
                        subtitles = <?php echo $subtitles; ?>;
                    } else if (audio.id === 'audio_1') {
                        subtitles = <?php echo $secondSubtitles; ?>;
                    } else if (audio.id === 'audio_2') {
                        subtitles = <?php echo $destinySubtitles; ?>;
                    }

                    subtitles.forEach(subtitle => {
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
                    const nextAudio = audio.nextElementSibling;
                    if (nextAudio) {
                        playAudio(nextAudio);
                    }
                });
            });

            const handleSubtitles = (subtitles) => {
                timeoutIDs.forEach(id => clearTimeout(id));
                timeoutIDs = [];

                subtitles.forEach(subtitle => {
                    const timeoutID = setTimeout(() => {
                        textDiv.textContent = subtitle.text;
                    }, subtitle.time * 1000);
                    timeoutIDs.push(timeoutID);
                });
            };
        });
    </script>
    <?php
    // Adicione o formulário do Elementor com o ID desejado
    $elementor_form_id = 'Form1'; // Substitua pelo ID real do formulário do Elementor
    echo \Elementor\Plugin::instance()->frontend->get_builder_content($elementor_form_id, true);

    if (isset($_SESSION['name_number']) && isset($_SESSION['birth_number'])) {
        echo '<h2>Resultados:</h2>';
        echo '<p>Número do Nome: ' . $_SESSION['name_number'] . '</p>';
        echo '<p>Número do Destino: ' . $_SESSION['birth_number'] . '</p>';

        // Adicionar o áudio baseado nos resultados
        $audio_file = get_stylesheet_directory_uri() . '/audio/result_audio_' . $_SESSION['name_number'] . '.mp3';
        echo '<audio controls autoplay>
                <source src="' . esc_url($audio_file) . '" type="audio/mpeg">
                Seu navegador não suporta o elemento de áudio.
              </audio>';

        // Limpar os dados da sessão
        unset($_SESSION['name_number']);
        unset($_SESSION['birth_number']);
    }
    return ob_get_clean();
}

// Função para registrar o shortcode
function register_custom_shortcode() {
    add_shortcode('custom_form_shortcode', 'form_shortcode');
}
add_action('init', 'register_custom_shortcode');
