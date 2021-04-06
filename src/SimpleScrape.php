<?php

//

declare(strict_types=1);

//

namespace simplescrape;

//

class SimpleScrape
{
  // page object

  private $page = '';

  // curl object

  private $curl;

  // curl options

  private $curl_options;

  // force utf8 encoding

  private $utf8 = false;

  // dom document

  private $document;

  // dom xpath

  private $xpath;

  // query

  private $query;

  // constructor

  public function __construct(array $options = [])
  {
    // set curl options

    if (isset($options['curl_options']))
    {
      $this->setCurlOptions($options['curl_options']);
    }

    // set query

    if (isset($options['query']))
    {
      $this->setQuery($options['query']);
    }

    // set encoding to utf-8

    if (isset($options['utf8']))
    {
      $this->setUtf8($options['utf8']);
    }

    // init dom document

    $this->init();
  }

  // use curl to download the page

  private function getPage(): void
  {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, $this->curl_options);
    $this->page .= curl_exec($this->curl);
    curl_close($this->curl);
  }

  // load the page into domdocument, remove errors

  private function loadPage(): void
  {
    libxml_use_internal_errors(true);
    $this->document->loadHTML($this->page);
    libxml_clear_errors();
    $this->xpath = new \DOMXPath($this->document);
  }

  // get and load the page into dom document

  public function load(): void
  {
    if ($this->curl_options === null)
    {
      throw new \InvalidArgumentException('curl_options has not been set');
    }
    else
    {
      $this->getPage();
      $this->loadPage();
    }
  }

  //

  private function init(): void
  {
    $this->document = new \DOMDocument();
    $this->document->preserveWhiteSpace = false;
  }

  // set curl options

  public function setCurlOptions(array $curl_options): void
  {
    $this->curl_options = $curl_options;
  }

  // set utf8

  public function setUtf8(bool $utf8): void
  {
    if ($utf8 === true)
    {
      $this->page = '<' . '?xml encoding="utf-8" ?' . '>';
    }

    //

    $this->utf8 = $utf8;
  }

  // set the query

  public function setQuery(string $query): void
  {
    $this->query = $query;
  }

  // parse page based on specified patterns

  public function parsePage(): \DOMNodeList
  {
    if ($this->query === null)
    {
      throw new \InvalidArgumentException('query has not been set');
    }
    else
    {
      return $this->xpath->query($this->query);
    }
  }
}
