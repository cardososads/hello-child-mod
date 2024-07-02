<?php
class NumerologyCalculator {

    // Função para calcular o número de destino
    public function calculateDestinyNumber($birthDate) {
        // Supondo que a data de nascimento esteja no formato 'DD-MM-YYYY'
        $parts = explode('-', $birthDate);

        // Extrai dia, mês e ano
        $day = intval($parts[0]);
        $month = intval($parts[1]);
        $year = intval($parts[2]);

        // Reduz cada parte a um único dígito ou número mestre
        $day = $this->reduceToSingleDigit($this->sumDigits($day));
        $month = $this->reduceToSingleDigit($this->sumDigits($month));
        $year = $this->reduceToSingleDigit($this->sumDigits($year));

        // Soma as reduções e reduz a um único dígito ou número mestre
        $destinyNumber = $this->reduceToSingleDigit($day + $month + $year);

        return $destinyNumber;
    }

    // Função para calcular o número de expressão
    public function calculateExpressionNumber($fullName) {
        $fullNameValue = $this->convertNameToNumber($fullName);
        return $this->reduceToSingleDigit($fullNameValue);
    }

    // Função para calcular o número de motivação
    public function calculateMotivationNumber($fullName) {
        $vowelValue = $this->convertVowelsToNumber($fullName);
        return $this->reduceToSingleDigit($vowelValue);
    }

    // Converte letras do nome para números baseados na numerologia cabalística
    private function convertNameToNumber($name) {
        $name = strtoupper(preg_replace('/[^A-Z]/', '', $name)); // Remove não-letras
        $charValues = array(
            'A' => 1, 'J' => 1, 'S' => 1,
            'B' => 2, 'K' => 2, 'T' => 2,
            'C' => 3, 'L' => 3, 'U' => 3,
            'D' => 4, 'M' => 4, 'V' => 4,
            'E' => 5, 'N' => 5, 'W' => 5,
            'F' => 6, 'O' => 6, 'X' => 6,
            'G' => 7, 'P' => 7, 'Y' => 7,
            'H' => 8, 'Q' => 8, 'Z' => 8,
            'I' => 9, 'R' => 9
        );
        $sum = 0;
        foreach (str_split($name) as $char) {
            $sum += $charValues[$char];
        }
        return $sum;
    }

    // Converte vogais do nome para números baseados na numerologia cabalística
    private function convertVowelsToNumber($name) {
        $name = strtoupper(preg_replace('/[^AEIOU]/', '', $name)); // Mantém apenas vogais
        $charValues = array(
            'A' => 1, 'E' => 5, 'I' => 9,
            'O' => 6, 'U' => 3
        );
        $sum = 0;
        foreach (str_split($name) as $char) {
            $sum += $charValues[$char];
        }
        return $sum;
    }

    // Soma os dígitos de um número
    private function sumDigits($number) {
        return array_sum(str_split($number));
    }

    // Reduz um número para um único dígito, exceto os números mestres 11, 22 e 33
    private function reduceToSingleDigit($number) {
        while ($number > 9 && !in_array($number, [11, 22, 33])) {
            $number = array_sum(str_split($number));
        }
        return $number;
    }
}
