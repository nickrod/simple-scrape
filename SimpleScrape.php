<?php

//

namespace nickrod\openconsult\tools;

//

class SimpleScrape
{
  // page object

  private $page;

  // curl object

  private $curl;

  // curl options

  private $curloptions = [];

  // dom document

  private $document;

  // dom xpath

  private $xpath;

  // query

  private $query;

  // constructor

  public function __construct($curl_options = [])
  {
    // check for curl options

    if (is_array($curl_options))
    {
      $this->curl_options = $curl_options;
    }

    // initialize objects

    $this->curl = curl_init();
    $this->document = new \DOMDocument();
    $this->document->preserveWhiteSpace = false;

    // load the page into dom

    $this->load();
  }

  // use curl to download the page

	private function getPage()
  {
    curl_setopt_array($this->curl, $this->curl_options);
    $this->page = curl_exec($this->curl);
    curl_close($this->curl);
	}

  // load the page into domdocument, remove errors

	private function loadPage()
  {
    if (!empty($this->page))
    {
      libxml_use_internal_errors(true);
      $this->document->loadHTML($this->page);
      libxml_clear_errors();
      $this->xpath = new \DOMXPath($this->document);
    }
	}

  // load the page into domdocument

	private function load()
  {
    $this->getPage();
    $this->loadPage();
  }

  // set the query

	public function setQuery($query)
  {
    $this->query = $query;
	}

  // parse page based on specified patterns

	public function parsePage($evaluate = false)
  {
    if (!empty($this->xpath) && !empty($this->query))
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
    else
    {
      return false;
    }
  }
}
