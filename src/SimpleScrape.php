<?php

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

  public function __construct($options = [])
  {
    // set curl options

    if (!empty($options['curl_options']))
    {
      $this->setCurlOptions($options['curl_options']);
    }

    // set query

    if (!empty($options['query']))
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

  private function getPage()
  {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, $this->curl_options);
    $this->page .= curl_exec($this->curl);
    curl_close($this->curl);
  }

  // load the page into domdocument, remove errors

  private function loadPage()
  {
    libxml_use_internal_errors(true);
    $this->document->loadHTML($this->page);
    libxml_clear_errors();

    //

    if ($this->utf8)
    {
      foreach ($this->document->childNodes as $item)
      {
        if ($item->nodeType == XML_PI_NODE)
        {
          $this->document->removeChild($item);
        }
      }

      //

      $this->document->encoding = 'utf-8';
    }

    //

    $this->xpath = new \DOMXPath($this->document);
  }

  // get and load the page into dom document

  public function load()
  {
    if (empty($this->curl_options))
    {
      throw new \InvalidArgumentException("'curl_options' has not been set");
    }
    else
    {
      $this->getPage();
      $this->loadPage();
    }
  }

  //

  private function init()
  {
    $this->document = new \DOMDocument();
    $this->document->preserveWhiteSpace = false;
  }

  // set curl options

  public function setCurlOptions($curl_options)
  {
    if (!is_array($curl_options))
    {
      throw new \InvalidArgumentException("'curl_options' must be an array");
    }
    else
    {
      $this->curl_options = $curl_options;
    }
  }

  // set utf8

  public function setUtf8($utf8)
  {
    if (!is_bool($utf8))
    {
      throw new \InvalidArgumentException("'utf8' must be a boolean");
    }
    else
    {
      if ($utf8)
      {
        $this->page = "<" . "?xml encoding='utf-8' ?" . ">";
      }

      //

      $this->utf8 = $utf8;
    }
  }

  // set the query

  public function setQuery($query)
  {
    if (!is_string($query))
    {
      throw new \InvalidArgumentException("'query' must be a string");
    }
    else
    {
      $this->query = $query;
    }
  }

  // parse page based on specified patterns

  public function parsePage($evaluate = false)
  {
    if (!is_bool($evaluate))
    {
      throw new \InvalidArgumentException("'evaluate' must be a boolean");
    }
    elseif (empty($this->query))
    {
      throw new \InvalidArgumentException("'query' has not been set");
    }
    else
    {
      if (!$evaluate)
      {
        return $this->xpath->query($this->query);
      }
      else
      {
        return $this->xpath->evaluate($this->query);
      }
    }
  }
}
