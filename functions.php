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
    $numeros_destino = get_option('_numeros_destino_516'); // Ajuste o nome da opção conforme necessário

    // Obtém o slug da página atual
    global $post;
    $slug = $post->post_name;

    // Determina qual índice do array de destino usar com base no slug
    $indice_destino = 1; // Padrão para slug "form-01"
    if ($slug == 'form-02') {
        $indice_destino = 2; // Usar o terceiro áudio para "form-02"
    }

    // Verifica se o índice de destino existe no array
    if (isset($numeros_destino["item-$indice_destino"])) {
        $audio_destino = $numeros_destino["item-$indice_destino"]['_audio_do_numero'];
        $legenda_destino = $numeros_destino["item-$indice_destino"]['_legenda_do_audio'];
    } else {
        // Caso não exista, use um valor padrão ou lógica alternativa
        $audio_destino = ''; // Defina um valor padrão ou lógica alternativa aqui
        $legenda_destino = '';
    }
    ?>

    <div id="text"></div>

    <audio id="audioIntrodutorio" controls autoplay style="width: 100%">
        <source src="<?php echo esc_url($audio_intro['_audio-introdutorio']); ?>" type="audio/mpeg">
    </audio>

    <audio id="entradaDestino" controls style="width: 100%; display: none;">
        <source src="<?php echo esc_url($audio_intro['_pos-intro']); ?>" type="audio/mpeg">
    </audio>

    <audio id="audioDestino" controls style="width: 100%; display: none;">
        <source src="<?php echo esc_url($audio_destino); ?>" type="audio/mpeg">
    </audio>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php echo stripslashes($audio_intro['_legenda-intro']); ?>

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

            const audio = document.getElementById('audioIntrodutorio');
            const secondAudio = document.getElementById('entradaDestino');
            const thirdAudio = document.getElementById('audioDestino');
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

            audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));

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

            audio.addEventListener('ended', () => {
                textDiv.textContent = "";
                secondAudio.style.display = 'block';
                playAudio(secondAudio);
            });

            secondAudio.addEventListener('ended', () => {
                textDiv.textContent = "";
                thirdAudio.style.display = 'block';
                playAudio(thirdAudio);
            });

            thirdAudio.addEventListener('ended', () => {
                textDiv.textContent = "";
            });

            // Novo código para ocultar/desativar o primeiro player quando o segundo player começar a tocar
            secondAudio.addEventListener('play', () => {
                audio.style.display = 'none';
                audio.pause(); // Pausa o primeiro áudio caso esteja tocando
            });

            thirdAudio.addEventListener('play', () => {
                audio.style.display = 'none';
                audio.pause(); // Pausa o primeiro áudio caso esteja tocando
                secondAudio.style.display = 'none';
                secondAudio.pause(); // Pausa o segundo áudio caso esteja tocando
            });

            // Opcional: Mostrar o primeiro player novamente se necessário
            secondAudio.addEventListener('ended', () => {
                audio.style.display = 'block';
            });

            thirdAudio.addEventListener('ended', () => {
                secondAudio.style.display = 'block';
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

// Função para registrar o shortcode
function register_custom_shortcode() {
    add_shortcode('custom_form_shortcode', 'form_shortcode');
}
add_action('init', 'register_custom_shortcode');
