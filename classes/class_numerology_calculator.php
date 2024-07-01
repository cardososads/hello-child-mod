<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NumerologyCalculator {
    private $letter_values = [
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9,
        'J' => 1, 'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5, 'O' => 6, 'P' => 7, 'Q' => 8, 'R' => 9,
        'S' => 1, 'T' => 2, 'U' => 3, 'V' => 4, 'W' => 5, 'X' => 6, 'Y' => 7, 'Z' => 8
    ];

    private $vowel_values = ['A', 'E', 'I', 'O', 'U'];
    private $master_numbers = [11, 22, 33];

    public function calculate_name_number($name) {
        return $this->calculate_sum($name);
    }

    public function calculate_birth_number($date) {
        list($day, $month, $year) = explode('/', $date);
        $sum = $this->reduce_to_single_digit($day) + $this->reduce_to_single_digit($month) + $this->reduce_to_single_digit($year);
        return $this->reduce_to_single_digit($sum);
    }

    public function calculate_expression_number($name) {
        return $this->calculate_sum($name);
    }

    public function calculate_soul_urge_number($name) {
        return $this->calculate_sum($name, true);
    }

    public function calculate_personality_number($name) {
        return $this->calculate_sum($name, false);
    }

    public function calculate_destiny_number($name) {
        return $this->calculate_sum($name);
    }

    private function calculate_sum($name, $only_vowels = null) {
        $name = strtoupper(str_replace(' ', '', $name));
        $sum = 0;

        foreach (str_split($name) as $char) {
            if (isset($this->letter_values[$char])) {
                if (is_null($only_vowels) || ($only_vowels && in_array($char, $this->vowel_values)) || (!$only_vowels && !in_array($char, $this->vowel_values))) {
                    $sum += $this->letter_values[$char];
                }
            }
        }

        return $this->reduce_to_single_digit($sum);
    }

    private function reduce_to_single_digit($number) {
        while ($number >= 10 && !in_array($number, $this->master_numbers)) {
            $number = array_sum(str_split($number));
        }
        return $number;
    }
}
