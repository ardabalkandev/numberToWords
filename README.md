# Number to Words Converter

This PHP class converts numbers into their word representations in both Turkish (TR) and English (EN). It handles integers and decimal numbers with specific formatting rules for each language.

## Features

- Convert numbers to words in Turkish and English.
- Handles large numbers, including billions, millions, thousands, and hundreds.
- Correctly formats decimal numbers according to language-specific rules.
- Ensures specific number formatting, such as omitting "BİR" for hundreds in Turkish when applicable.

## Installation

You can clone this repository or download the source code directly.

```bash
git clone https://github.com/ardabalkandev/number-to-words.git
```

## Usage
```
require 'NumberToWords.php';

$converter = new NumberToWords();

// Convert number to Turkish words
echo $converter->convert('987154321123,02', 'TR');
// Output: DOKUZ YÜZ SEKSEN YEDİ MİLYAR YÜZ ELLİ DÖRT MİLYON ÜÇ YÜZ YİRMİ BİR BİN YÜZ YİRMİ ÜÇ VİRGÜL SIFIR İKİ

// Convert number to English words
echo $converter->convert('987154321123.02', 'EN');
// Output: NINE HUNDRED EIGHTY SEVEN BILLION ONE HUNDRED FIFTY FOUR MILLION THREE HUNDRED TWENTY ONE THOUSAND ONE HUNDRED TWENTY THREE POINT ZERO TWO
```

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. You are free to use, modify, and distribute this project as you see fit. For more details, please refer to the [LICENSE](LICENSE) file.
