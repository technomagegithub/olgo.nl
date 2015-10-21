<?php
error_reporting(E_ERROR | E_PARSE);

class pageloader {
   private $ch;
   private $result;

   public function pageloader() {
     $this->ch = curl_init();  
   }
   
   public function loadurl($url) {
        // set url 
        curl_setopt($this->ch, CURLOPT_URL, $url); 

        //return the transfer as a string 
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $this->result = curl_exec($this->ch); 

        if ($this->result == false) 
          return false;

	return true;
   }
   
   public function getResult() {
     return $this->result;
  }
}


$pageloader = new pageloader();
$doc = new DOMDocument();

$result = $pageloader->loadurl("http://www.brickwatch.net/nl/set/7280/Straight-Crossroad-Plates.html");        
if ($result) {
  $doc->loadHTML($pageloader->getResult());

  $xpath = new DOMXpath($doc);

  $elements = $xpath->query('//*[(@id = "prices")]');
  if (!is_null($elements)) {
    foreach ($elements as $element) {
      echo "<br/>[". $element->nodeName. "]";

      $nodes = $element->childNodes;
      foreach ($nodes as $node) {
        if ($node->hasAttribute("title"))
          echo$node->getAttribute("title");
        echo $node->nodeValue. "\n";
      }
    }
  }
}


?>
