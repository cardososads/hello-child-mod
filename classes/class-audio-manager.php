<?php

class AudioManager {
    private $audios_data;

    public function __construct() {
        $this->audios_data = get_option('_audios');
    }

    public function getAudiosByKeys($keys = array()) {
        $audios = array();

        foreach ($keys as $key) {
            if (isset($this->audios_data[$key]['src'])) {
                $audios[] = array(
                    'src' => esc_url($this->audios_data[$key]['src']),
                    'subtitle' => isset($this->audios_data[$key]['subtitle']) ? stripslashes($this->audios_data[$key]['subtitle']) : '',
                );
            }
        }

        return $audios;
    }
}
