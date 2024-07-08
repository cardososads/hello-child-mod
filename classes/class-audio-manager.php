<?php

class AudioManager {
    private $audios_data;

    public function __construct() {
        $this->audios_data = get_option('_audios');
    }

    public function getAllAudios() {
        return $this->audios_data;
    }

    public function getIntrodutoryAudios() {
        $audios = array();

        if (isset($this->audios_data['_audio-introdutorio'])) {
            $audios['_audio-introdutorio'] = $this->audios_data['_audio-introdutorio'];
        }

        if (isset($this->audios_data['_pos-intro'])) {
            $audios['_pos-intro'] = $this->audios_data['_pos-intro'];
        }

        return $audios;
    }

    public function getDestinyNumerologyAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_destino_516'])) {
            foreach ($this->audios_data['_numeros_destino_516'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getMasculineExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_masculinos'])) {
            foreach ($this->audios_data['_numeros_expressao_masculinos'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getFeminineExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_femininos'])) {
            foreach ($this->audios_data['_numeros_expressao_femininos'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getGenderlessExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_sem_genero'])) {
            foreach ($this->audios_data['_numeros_expressao_sem_genero'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getMarriedMaleMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_masculino_casado'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_masculino_casado'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    // Métodos para os novos tipos de dados

    public function getAccMasculineExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_acc_masculinos'])) {
            foreach ($this->audios_data['_numeros_expressao_acc_masculinos'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getAccFeminineExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_acc_femininos_copy'])) {
            foreach ($this->audios_data['_numeros_expressao_acc_femininos_copy'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getAccGenderlessExpressionAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_expressao_acc_sem_genero'])) {
            foreach ($this->audios_data['_numeros_expressao_acc_sem_genero'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getMarriedMaleMotivationTabAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_masculino_casado_tab'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_masculino_casado_tab'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getMarriedMaleMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_motivacao_masculino_casado'])) {
            foreach ($this->audios_data['_numeros_motivacao_masculino_casado'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getSingleMaleMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_masculino_solteiro'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_masculino_solteiro'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getMarriedFemaleMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_feminino_casada'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_feminino_casada'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getSingleFemaleMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_feminino_solteira'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_feminino_solteira'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }

    public function getOtherMotivationAudios() {
        $audios = array();

        if (isset($this->audios_data['_numeros_de_motivacao_outros'])) {
            foreach ($this->audios_data['_numeros_de_motivacao_outros'] as $item) {
                if (isset($item['_audio_do_numero'])) {
                    $audios[] = $item['_audio_do_numero'];
                }
            }
        }

        return $audios;
    }
}