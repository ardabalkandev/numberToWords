<?php
class NumberToWords {
    private $ones;
    private $teens;
    private $tens;
    private $scales;
    private $decimalSeparator;
    private $language;
    private $defaultLanguage = 'EN'; // Varsayılan dil İngilizce

    public function __construct($language = 'EN') {
        $this->setLanguage($language);
    }

    public function setLanguage($language) {
        $this->language = strtoupper($language);
        $languageFile = __DIR__ . "/languages/{$this->language}.php";

        if (file_exists($languageFile)) {
            $langData = include($languageFile);
            $this->ones = $langData['ones'];
            $this->teens = $langData['teens'];
            $this->tens = $langData['tens'];
            $this->scales = $langData['scales'];
            $this->decimalSeparator = $langData['decimal_separator'];
        } else {
            // Eğer dil dosyası bulunamazsa varsayılan dili yükle
            $this->loadDefaultLanguage();
        }
    }

    private function loadDefaultLanguage() {
        $defaultLanguageFile = __DIR__ . "/languages/{$this->defaultLanguage}.php";

        if (file_exists($defaultLanguageFile)) {
            $langData = include($defaultLanguageFile);
            $this->ones = $langData['ones'];
            $this->teens = $langData['teens'];
            $this->tens = $langData['tens'];
            $this->scales = $langData['scales'];
            $this->decimalSeparator = $langData['decimal_separator'];
        } else {
            throw new Exception("Default language file for '{$this->defaultLanguage}' not found.");
        }
    }

    public function convert($number, $language = null) {
        
        // Eğer yeni bir dil verilmişse dil değiştir
        if ($language) {
            $this->setLanguage($language);
        }

        // Ondalık sayılar varsa onlarla ilgilenelim
        if (strpos($number, ',') !== false || strpos($number, '.') !== false) {
            return $this->convertDecimal($number);
        }

        return $this->convertNumber($number);
    }

    private function convertNumber($number) {
        if ($number < 10) {
            return $this->ones[$number];
        }

        if ($number >= 11 && $number <= 19) {
            return $this->teens[$number];
        }

        if ($number < 100) {
            return $this->handleTens($number);
        }

        if ($number < 1000) {
            return $this->handleHundreds($number);
        }

        return $this->handleScales($number);
    }

    private function handleTens($number) {
        $tensPart = intval($number / 10) * 10;
        $onesPart = $number % 10;
        return $onesPart == 0 ? $this->tens[$tensPart] : $this->tens[$tensPart] . ' ' . $this->ones[$onesPart];
    }

    private function handleHundreds($number) {
        $hundreds = intval($number / 100);
        $remainder = $number % 100;
        $hundredText = ($hundreds == 1 && $this->language === 'TR') ? $this->scales[100] : $this->ones[$hundreds] . ' ' . $this->scales[100];

        return $remainder == 0 ? $hundredText : $hundredText . ' ' . $this->convertNumber($remainder);
    }

    private function handleScales($number) {
        foreach ($this->scales as $scale => $scaleText) {
            if ($number >= $scale) {
                $scaleCount = intval($number / $scale);
                $remainder = $number % $scale;

                // Türkçe'de 'BİR BİN' yerine sadece 'BİN' yazılması gerekiyor
                $scaleWord = ($scaleCount == 1 && $scale >= 1000 && $this->language === 'TR') 
                    ? $scaleText 
                    : $this->convertNumber($scaleCount) . ' ' . $scaleText;

                return $remainder == 0 ? $scaleWord : $scaleWord . ' ' . $this->convertNumber($remainder);
            }
        }
        return 'UNSUPPORTED NUMBER';
    }

    private function convertDecimal($number) {
        $parts = preg_split('/[,.]/', $number);
        $wholePart = intval($parts[0]);
        $decimalPart = substr($parts[1], 0, 2); // İki basamağa kadar
        $wholePartText = $this->convertNumber($wholePart);
        $decimalPartValue = intval($decimalPart);

        $decimalPartText = $decimalPart[0] == '0' ? $this->ones[0] . ' ' . $this->convertNumber($decimalPartValue) : $this->convertNumber($decimalPartValue);
        return $wholePartText . ' ' . $this->decimalSeparator . ' ' . $decimalPartText;
    }
}

?>