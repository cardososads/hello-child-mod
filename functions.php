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
    ?>
    <div id="text"></div>
    <audio id="meuAudio" controls autoplay style="width: 100%">
        <source src="<?php echo esc_url(get_stylesheet_directory_uri() . '/audio/intro/introducao.mp3'); ?>" type="audio/mpeg">
        <track id="legendasTrack" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/audio/intro/introducao.vtt'); ?>" kind="subtitles" srclang="pt" label="Portuguese" default>
        Seu navegador não suporta o elemento de áudio ou legendas. Por favor, ative as legendas manualmente se estiverem disponíveis.
    </audio>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const subtitles = [
                { time: 0, text: "Olá, tudo bem?" },
                { time: 2.5, text: "Nesse momento vamos iniciar nossa jornada de conhecimento," },
                { time: 5.8, text: "entendendo como seu nome, data de nascimento e assinatura" },
                { time: 9.5, text: "revelam muitos aspectos sobre sua vida." },
                { time: 12.7, text: "Com a numerologia cabalística saberemos sobre oportunidades," },
                { time: 16, text: "relacionamento, desafios, e outros fatos que podem te ajudar" },
                { time: 20.5, text: "a ter autoconhecimento e uma visão única e profunda" },
                { time: 23, text: "sobre diversos aspectos da sua existência." }
            ];

            const audio = document.getElementById('meuAudio');
            const textDiv = document.getElementById('text');
            let timeoutIDs = [];

            audio.addEventListener('play', () => {
                timeoutIDs.forEach(id => clearTimeout(id));  // Clear previous timeouts
                timeoutIDs = [];

                subtitles.forEach(subtitle => {
                    const timeoutID = setTimeout(() => {
                        textDiv.textContent = subtitle.text;
                    }, subtitle.time * 1000);
                    timeoutIDs.push(timeoutID);
                });
            });

            audio.addEventListener('pause', () => {
                timeoutIDs.forEach(id => clearTimeout(id));  // Clear timeouts on pause
            });

            audio.addEventListener('seeked', () => {
                timeoutIDs.forEach(id => clearTimeout(id));  // Clear previous timeouts
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

            audio.addEventListener('ended', () => {
                textDiv.textContent = "";
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



