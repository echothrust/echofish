<?php
class ExportXMLHelper {

  public $table='';
  public $xml=null;
  public function __init($table)
	{
	  $this->table=$table;
	  $this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><{$this->table}></{$this->table}>');
	  $this->xml->addAttribute('generation_ts', time());
  }
  public function getXML() 
  {
	  $exported=Yii::app()->db->createCommand("SELECT * FROM {$this->table}")->queryAll();
    foreach($exported as $record)
    {
      $record_xml=$xml->addChild('record');
      $this->arrayTOattributes($record,$record_xml);
    }
    echo $this->xml->asXML();
	}

	protected function arrayTOattributes($arr,$xml)
	{
	  foreach($arr as $key=>$val)
	    $xml->addAttribute($key,CHtml::cdata($val));
	}
}