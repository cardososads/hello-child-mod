<?php

class AudioManager {
    private $audios_data;

    public function __construct() {
        $this->audios_data = get_option('_audios');
    }

    public function getAudios($audio_keys = array()) {
        $audios = array();

        foreach ($audio_keys as $key) {
            if (isset($this->audios_data[$key])) {
                $audios[$key] = array(
                    'src' => esc_url($this->audios_data[$key]),
                    'subtitle' => isset($this->audios_data[$key . '_legenda']) ? stripslashes($this->audios_data[$key . '_legenda']) : '',
                );
            }
        }

        return $audios;
    }

    public function getDynamicAudio($calculation_type) {
        $transient_key = strtolower($calculation_type) . '_submission_data';
        $submission_data = get_transient($transient_key);

        if ($submission_data && isset($submission_data[$calculation_type])) {
            $number = $submission_data[$calculation_type];
            if (isset($this->audios_data['numeros']['item-' . $number]['_audio_do_numero'])) {
                return array(
                    'src' => esc_url($this->audios_data['numeros']['item-' . $number]['_audio_do_numero']),
                    'subtitle' => isset($this->audios_data['numeros']['item-' . $number]['_legenda_do_audio']) ? stripslashes($this->audios_data['numeros']['item-' . $number]['_legenda_do_audio']) : '',
                );
            }
        }

        return array(
            'src' => '',
            'subtitle' => '',
        );
    }
}
