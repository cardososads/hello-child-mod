<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AudioDisplay {
    public function __construct() {
        add_shortcode('display_audio_with_captions', [$this, 'display_audio_with_captions']);
    }

    public function display_audio_with_captions() {
        if (isset($_SESSION['selected_audio']) && isset($_SESSION['selected_caption'])) {
            $audio_file = $_SESSION['selected_audio'];
            $caption_file = $_SESSION['selected_caption'];

            echo '<audio controls>
                    <source src="' . esc_url($audio_file) . '" type="audio/mpeg">
                    <track src="' . esc_url($caption_file) . '" kind="captions" srclang="pt" label="Portuguese">
                    Seu navegador não suporta o elemento de áudio.
                  </audio>';

            unset($_SESSION['selected_audio']);
            unset($_SESSION['selected_caption']);
        } else {
            echo 'Nenhum áudio selecionado.';
        }
    }
}

new AudioDisplay();
