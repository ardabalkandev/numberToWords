<?php
class NumberToWords {
    private $ones;
    private $teens;
    private $tens;
    private $scales;
    private $decimalSeparator;
    private $language;

    public function __construct($language = 'EN') {
        $this->setLanguage($language);
    }

    public function setLanguage($language) {
        $this->language = strtoupper($language);

        switch ($this->language) {
            case 'TR': // TÜRKÇE
                $this->ones = [0 => 'SIFIR', 1 => 'BİR', 2 => 'İKİ', 3 => 'ÜÇ', 4 => 'DÖRT', 5 => 'BEŞ', 6 => 'ALTI', 7 => 'YEDİ', 8 => 'SEKİZ', 9 => 'DOKUZ'];
                $this->teens = []; // Türkçede özel teen sayıları yok, tens ile normalde işlenecek.
                $this->tens = [10 => 'ON', 20 => 'YİRMİ', 30 => 'OTUZ', 40 => 'KIRK', 50 => 'ELLİ', 60 => 'ALTMIŞ', 70 => 'YETMİŞ', 80 => 'SEKSEN', 90 => 'DOKSAN'];
                $this->scales = [1000000000 => 'MİLYAR', 1000000 => 'MİLYON', 1000 => 'BİN', 100 => 'YÜZ'];
                $this->decimalSeparator = 'VİRGÜL';
                break;
            case 'EN': // İNGİLİZCE
            default:
                $this->ones = [0 => 'ZERO', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE', 6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE'];
                $this->teens = [11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN', 14 => 'FOURTEEN', 15 => 'FIFTEEN', 16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 19 => 'NINETEEN'];
                $this->tens = [10 => 'TEN', 20 => 'TWENTY', 30 => 'THIRTY', 40 => 'FORTY', 50 => 'FIFTY', 60 => 'SIXTY', 70 => 'SEVENTY', 80 => 'EIGHTY', 90 => 'NINETY'];
                $this->scales = [1000000000 => 'BILLION', 1000000 => 'MILLION', 1000 => 'THOUSAND', 100 => 'HUNDRED'];
                $this->decimalSeparator = 'POINT';
                break;
        }
    }

    public function convert($number, $language = 'EN') {
        $this->setLanguage($language);

        // Ondalık sayılar varsa onlarla ilgilenelim
        if (strpos($number, ',') !== false || strpos($number, '.') !== false) {
            return $this->convertDecimal($number);
        }

        // 0-9 arası sayılar
        if ($number < 10) {
            return $this->ones[$number];
        }

        // 11-19 arası sayılar (TEENS kontrolü)
        if ($number >= 11 && $number <= 19) {
            return $this->teens[$number];
        }

        // 10-99 arası sayılar
        if ($number < 100) {
            $tensPart = intval($number / 10) * 10;
            $onesPart = $number % 10;
            return $onesPart == 0 ? $this->tens[$tensPart] : $this->tens[$tensPart] . ' ' . $this->ones[$onesPart];
        }

        // 100-999 arası sayılar
        if ($number < 1000) {
            $hundreds = intval($number / 100);
            $remainder = $number % 100;
            $hundredText = ($hundreds == 1 && $this->language == 'TR') ? 'YÜZ' : $this->ones[$hundreds] . ' ' . $this->scales[100]; 
            return $remainder == 0 ? $hundredText : $hundredText . ' ' . $this->convert($remainder, $language);
        }

        // Daha büyük sayılar için scale'leri kullan
        foreach ($this->scales as $scale => $scaleText) {
            if ($number >= $scale) {
                $scaleCount = intval($number / $scale);
                $remainder = $number % $scale;

                $scaleWord = ($scaleCount == 1 && $scale >= 1000 && $this->language == 'TR') 
                    ? $scaleText // 1 olduğu durumlarda 'BİR' yazılmadan sadece ölçek adı eklenir (örneğin 'BİN')
                    : $this->convert($scaleCount, $language) . ' ' . $scaleText;

                return $remainder == 0 ? $scaleWord : $scaleWord . ' ' . $this->convert($remainder, $language);
            }
        }

        return 'UNSUPPORTED NUMBER';
    }

    private function convertDecimal($number) {
        // Ondalık sayıyı virgül veya nokta ile ayır
        $parts = preg_split('/[,.]/', $number);
        $wholePart = intval($parts[0]);
        $decimalPart = substr($parts[1], 0, 2); // Ondalık kısmı sadece iki basamak al
        
        // Tam sayı kısmını dönüştür
        $wholePartText = $this->convert($wholePart, $this->language);

        // Ondalık kısmı dönüştür
        $decimalPartValue = intval($decimalPart);
        if ($this->language == 'TR' && $decimalPart[0] == '0') {
            // Türkçe'de ondalık kısmın ilk basamağı 0 ise
            $decimalPartText = $this->convert($decimalPartValue, $this->language);
            return $wholePartText . ' ' . $this->decimalSeparator . ' ' . $this->ones[0] . ' ' . $decimalPartText;
        } elseif ($this->language == 'EN' && $decimalPart[0] == '0') {
            // İngilizce'de ondalık kısmın ilk basamağı 0 ise
            return $wholePartText . ' ' . $this->decimalSeparator . ' ' . $this->ones[0] . ' ' . $this->convert($decimalPartValue, $this->language);
        } else {
            // Normal durum
            $decimalPartText = $this->convert($decimalPartValue, $this->language);
            return $wholePartText . ' ' . $this->decimalSeparator . ' ' . $decimalPartText;
        }
    }
}
?>