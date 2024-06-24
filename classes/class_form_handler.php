<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class FormHandler {
    public function __construct() {
        add_action('wp', [$this, 'handle_form_submission']);
        if (!session_id()) {
            session_start();
        }
    }

    public function handle_form_submission() {
        if (isset($_POST['numerology_submit_1'])) {
            $this->process_form();
        }
    }

    private function process_form() {
        if (!empty($_POST['name']) && !empty($_POST['birthdate'])) {
            $name = sanitize_text_field($_POST['name']);
            $birthdate = sanitize_text_field($_POST['birthdate']);

            $calculator = new NumerologyCalculator();
            $name_number = $calculator->calculate_name_number($name);
            $birth_number = $calculator->calculate_birth_number($birthdate);

            $_SESSION['name_number'] = $name_number;
            $_SESSION['birth_number'] = $birth_number;

            wp_redirect($_SERVER['REQUEST_URI']);
            exit;
        }
    }
}
