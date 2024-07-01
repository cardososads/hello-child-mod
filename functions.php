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

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

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
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

// Inicie a sessão PHP
function start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

// Carregar classes do tema filho
require_once get_stylesheet_directory() . '/classes/class_numerology_calculator.php';
require_once get_stylesheet_directory() . '/classes/class_form_handler.php';
require_once get_stylesheet_directory() . '/classes/class_audio_display.php';
require_once get_stylesheet_directory() . '/classes/class_form_navigation.php';

// Inicializar as classes
new FormHandler();
new AudioDisplay();
new FormNavigation();

// Adicionar shortcode para o formulário e exibição de áudio
add_shortcode('form_1_shortcode', 'form_1_shortcode');
function form_1_shortcode() {
    ob_start();
    $audio_intro = get_option('_audios');
    ?>
    <div id="text"></div>
    <audio id="audioIntrodutorio" controls autoplay style="width: 100%">
        <source src="<?php echo esc_url($audio_intro['_audio-introdutorio']); ?>" type="audio/mpeg">
    </audio>
    <audio id="entradaDestino" controls style="width: 100%; display: none;">
        <source src="<?php echo esc_url($audio_intro['_pos-intro']); ?>" type="audio/mpeg">
    </audio>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php
            echo stripslashes($audio_intro['_legenda-intro']);
            echo stripslashes($audio_intro['_legenda-pos-intro']);
            ?>

            const audio = document.getElementById('audioIntrodutorio');
            const secondAudio = document.getElementById('entradaDestino');
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
                timeoutIDs.forEach(id => clearTimeout(id));  // Clear previous timeouts
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

            audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
            secondAudio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

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

            audio.addEventListener('ended', () => {
                textDiv.textContent = "";
                secondAudio.style.display = 'block';
                playAudio(secondAudio);
            });

            secondAudio.addEventListener('ended', () => {
                textDiv.textContent = "";
            });

            // Novo código para ocultar/desativar o primeiro player quando o segundo player começar a tocar
            secondAudio.addEventListener('play', () => {
                audio.style.display = 'none';
                audio.pause(); // Pausa o primeiro áudio caso esteja tocando
            });

            // Opcional: Mostrar o primeiro player novamente se necessário
            secondAudio.addEventListener('ended', () => {
                audio.style.display = 'block';
            });
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

add_action('elementor_pro/forms/new_record', function($record, $handler) {
    // Verifique se o formulário é 'Form1'
    $form_name = $record->get_form_settings('form_name');
    if ('Form1' !== $form_name) {
        return;
    }

    // Defina os nomes esperados dos campos
    $expected_fields = [
        'nome' => 'name', // 'nome' é o campo do Elementor, 'name' é o nome que ele terá no array
        'data_de_nascimento' => 'date', // 'data_de_nascimento' é o campo do Elementor, 'email' é o nome que ele terá no array
    ];

    // Obtenha os dados do formulário e renomeie conforme necessário
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($expected_fields as $key => $field_name) {
        if (isset($raw_fields[$key])) {
            $fields[$key] = $raw_fields[$key]['value'];
        } else {
            $fields[$key] = ''; // Define um valor padrão se o campo não estiver presente
        }
    }

    // Armazene os dados do formulário em uma opção temporária
    set_transient('form1_submission_data', $fields, 60*60); // Armazena por 1 hora
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
