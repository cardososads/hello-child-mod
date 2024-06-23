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

// Carregar classes do tema filho
require_once get_stylesheet_directory() . '/classes/class_numerology_calculator.php';
require_once get_stylesheet_directory() . '/classes/class_form_handler.php';
require_once get_stylesheet_directory() . '/classes/class_audio_display.php';

// Inicializar as classes
new FormHandler();
new AudioDisplay();

// Adicionar shortcodes
function form_1_shortcode() {
    ob_start();
    ?>
    <amp-3q-player
            data-id="c8dbe7f4-7f7f-11e6-a407-0cc47a188158"
            layout="responsive"
            width="480"
            height="270"
    ></amp-3q-player>
    <audio id="meuAudio" controls autoplay autocapitalize="on" style="width: 100%">
        <source src="<?php echo esc_url(get_stylesheet_directory_uri() . '/audio/intro/introducao.mp3'); ?>" type="audio/mpeg">
        <track id="legendasTrack" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/audio/intro/introducao.vtt'); ?>" kind="subtitles" srclang="pt" label="Portuguese" default>
        Seu navegador não suporta o elemento de áudio ou legendas. Por favor, ative as legendas manualmente se estiverem disponíveis.
    </audio>


    <form method="post">
        <label for="name">Nome Completo:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="birthdate">Data de Nascimento (dd/mm/yyyy):</label>
        <input type="text" id="birthdate" name="birthdate" required>
        <br>
        <input type="submit" name="numerology_submit_1" value="Calcular">
    </form>
    <?php
    if (isset($_SESSION['name_number']) && isset($_SESSION['birth_number'])) {
        echo '<h2>Resultados:</h2>';
        echo '<p>Número do Nome: ' . $_SESSION['name_number'] . '</p>';
        echo '<p>Número do Destino: ' . $_SESSION['birth_number'] . '</p>';

        // Adicionar o áudio baseado nos resultados
        $audio_file = get_stylesheet_directory_uri() . '/audio/result_audio_' . $_SESSION['name_number'] . '.mp3';
        echo '<audio controls>
                <source src="' . esc_url($audio_file) . '" type="audio/mpeg">
                Seu navegador não suporta o elemento de áudio.
              </audio>';

        // Limpar os dados da sessão
        unset($_SESSION['name_number']);
        unset($_SESSION['birth_number']);
    }
    return ob_get_clean();
}
add_shortcode('form_1_shortcode', 'form_1_shortcode');


function enqueue_amp_audio_script() {
    wp_enqueue_script( 'amp-audio', 'src="https://cdn.ampproject.org/v0/amp-3q-player-0.1.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_amp_audio_script' );
