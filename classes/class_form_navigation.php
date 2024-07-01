<?php

class FormNavigation {
    public function __construct() {
        add_action('template_redirect', [$this, 'handle_form_submission']);
    }

    public function handle_form_submission() {
        if (isset($_POST['numerology_submit_1'])) {
            $this->process_form_1();
        } elseif (isset($_POST['numerology_submit_2'])) {
            $this->process_form_2();
        } elseif (isset($_POST['numerology_submit_3'])) {
            $this->process_form_3();
        }
    }

    private function process_form_1() {
        if (!empty($_POST['name']) && !empty($_POST['birthdate'])) {
            $name = sanitize_text_field($_POST['name']);
            $birthdate = sanitize_text_field($_POST['birthdate']);

            $calculator = new NumerologyCalculator();
            $name_number = $calculator->calculate_name_number($name);
            $birth_number = $calculator->calculate_birth_number($birthdate);

            $_SESSION['name_number'] = $name_number;
            $_SESSION['birth_number'] = $birth_number;

            // Adicione um log para verificar se o processamento ocorreu corretamente
            error_log('Form-01 processado com sucesso.');

            // Redirecionar diretamente para o Form-02 após calcular
            wp_redirect(home_url('/form-02/'));
            exit;
        }
    }

    // Métodos process_form_2() e process_form_3() continuam como antes
}
