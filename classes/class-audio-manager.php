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

    public function getDestinyAudios() {
        $destiny_audios = array();

        // Verifica se existe a categoria de áudios de destino
        if (isset($this->audios_data['_numeros_destino'])) {
            foreach ($this->audios_data['_numeros_destino'] as $item) {
                $numero = $item['numero'];
                $destiny_audios[$numero] = array(
                    'src' => esc_url($item['_audio_do_numero']),
                    'subtitle' => isset($item['_legenda_do_audio']) ? stripslashes($item['_legenda_do_audio']) : '',
                    'subtitles_js' => isset($item['_legenda_do_audio']) ? $item['_legenda_do_audio'] : '',
                );
            }
        }

        return $destiny_audios;
    }

    public function getExpressionAudios($gender = 'masculino', $without_gender = false) {
        $expression_audios = array();

        // Determina a chave a ser usada com base no gênero
        $key = '_numeros_expressao_' . ($without_gender ? 'sem_genero' : $gender);

        // Verifica se existe a categoria de áudios de expressão
        if (isset($this->audios_data[$key])) {
            foreach ($this->audios_data[$key] as $item) {
                $numero = $item['numero'];
                $expression_audios[$numero] = array(
                    'src' => esc_url($item['_audio_do_numero']),
                    'subtitle' => isset($item['_legenda_do_audio']) ? stripslashes($item['_legenda_do_audio']) : '',
                    'subtitles_js' => isset($item['_legenda_do_audio']) ? $item['_legenda_do_audio'] : '',
                );
            }
        }

        return $expression_audios;
    }

    public function getMotivationAudios($gender = 'masculino', $marital_status = 'casado') {
        $motivation_audios = array();

        // Determina a chave a ser usada com base no gênero e estado civil
        $key = '_numeros_de_motivacao_' . $gender . '_' . $marital_status;

        // Verifica se existe a categoria de áudios de motivação
        if (isset($this->audios_data[$key])) {
            foreach ($this->audios_data[$key] as $item) {
                $numero = $item['numero'];
                $motivation_audios[$numero] = array(
                    'src' => esc_url($item['_audio_do_numero']),
                    'subtitle' => isset($item['_legenda_do_audio']) ? stripslashes($item['_legenda_do_audio']) : '',
                    'subtitles_js' => isset($item['_legenda_do_audio']) ? $item['_legenda_do_audio'] : '',
                );
            }
        }

        return $motivation_audios;
    }
}
