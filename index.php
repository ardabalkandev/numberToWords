<?php
require('numberToWords.php');
$converter = new NumberToWords();
echo $converter->convert('12237842,53', 'TR');
echo "<br>";
echo $converter->convert('14111842,53', 'EN');
?>