<?php

require "../../../vendor/autoload.php";

$fixture = [];

$discount = require "discount.php";

$fixture['oxdiscount'] = [$discount['testcatdiscount']['oxdiscount']];
$fixture['oxobject2discount'] = $discount['testcatdiscount']['oxobject2discount'];

$products = require "product.php";

$rows = [];
foreach ($products as $id => $array) {
    $rows[] = $array;
}

$fixture['oxarticles'] = $rows;

$descriptions = require "product_description.php";

$rows = [];
foreach ($descriptions as $id => $array) {
    $rows[] = $array;
}

$fixture['oxartextends'] = $rows;

$voucher = require 'voucher.php';

$fixture['oxvoucherseries'] = [$voucher['testvoucher4']['oxvoucherseries']];
$fixture['oxvouchers'] = $voucher['testvoucher4']['oxvouchers'];

$yaml = \Symfony\Component\Yaml\Yaml::dump($fixture);

file_put_contents("db_fixture.yml", $yaml);
