<?php
require('numberToWords.php');
$converter = new NumberToWords();
echo $converter->convert('987114321123,02', 'TR');
echo "<br>";
echo $converter->convert('987154321123,9923', 'EN');
?>