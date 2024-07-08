<?php

class AudioManager {
    private $audios_data;

    public function __construct() {
        $this->audios_data = get_option('_audios');
    }

    public function getAllAudios() {
        return $this->audios_data;
    }

    public function getIntroductions() {
        $introductions = array();

        // Verifica se existe o áudio introdutório
        if (isset($this->audios_data['_audio-introdutorio'])) {
            $introductions['_audio-introdutorio'] = array(
                'src' => esc_url($this->audios_data['_audio-introdutorio']),
                'subtitle' => isset($this->audios_data['_legenda-intro']) ? stripslashes($this->audios_data['_legenda-intro']) : '',
                'subtitles_js' => isset($this->audios_data['_legenda-intro']) ? $this->audios_data['_legenda-intro'] : '',
            );
        }

        // Verifica se existe o áudio de entrada de destino
        if (isset($this->audios_data['_pos-intro'])) {
            $introductions['_pos-intro'] = array(
                'src' => esc_url($this->audios_data['_pos-intro']),
                'subtitle' => isset($this->audios_data['_legenda-pos-intro']) ? stripslashes($this->audios_data['_legenda-pos-intro']) : '',
                'subtitles_js' => isset($this->audios_data['_legenda-pos-intro']) ? $this->audios_data['_legenda-pos-intro'] : '',
            );
        }

        return $introductions;
    }
}

