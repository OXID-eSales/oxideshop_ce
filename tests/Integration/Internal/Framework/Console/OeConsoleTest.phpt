--TEST--
Test for oe-console list 
--ARGS--
list
--FILE--
<?php
passthru(__DIR__ . '/../../../../../bin/oe-console');
?>
--EXPECTREGEX_EXTERNAL--
Fixtures/output-oe-console.txt