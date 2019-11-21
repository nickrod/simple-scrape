<?php

//

use simplescrape\SimpleScrape;

//

require __DIR__ . '/../vendor/autoload.php';

//

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//

//$url = "http://mercadolatino.biz/Factsheet/02555-020-INDX.htm";
$url = "https://www.tutorialspoint.com/libxml-clear-errors-function-in-php";
$user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0";
//$query = "//a[@class='price']";
//$query = "//td[contains(@class, 'percent-change')]";
$query = "//td[contains(@align, 'center')]";

//

$curl_options = [
  CURLOPT_URL => $url,
  CURLOPT_USERAGENT => $user_agent,
  CURLOPT_RETURNTRANSFER => true
];

//

$scrape = new SimpleScrape(['curl_options' => $curl_options, 'query' => $query, 'utf8' => true]);
$scrape->load();
//$scrape->setQuery($query);
$nodes = $scrape->parsePage();

//

foreach ($nodes as $node)
{
  if ($node->firstChild->nodeName != "b")
  {
    echo $node->nodeValue . "<br />";
    //$attribute = $node->getAttribute('data-symbol');
  }
}
