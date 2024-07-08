<?php

class AudioManager {
    private $audios_data;

    public function __construct() {
        $this->audios_data = get_option('_audios');
    }

    public function getAudiosByKeys($keys = array()) {
        $audios = array();

        foreach ($keys as $key) {
            if (isset($this->audios_data[$key])) {
                $audios[] = $this->audios_data[$key];
            }
        }

        return $audios;
    }
}
