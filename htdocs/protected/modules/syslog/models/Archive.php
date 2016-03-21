<?php

/**
 * This is the model class for table "archive".
 *
 * The followings are the available columns in table 'syslog':
 * @property string $id
 * @property string $host
 * @property integer $facility
 * @property integer $priority
 * @property integer $level
 * @property string $program
 * @property string $pid
 * @property string $tag
 * @property string $msg
 * @property string $received_ts
 * @property string $created_at
 */
class Archive extends Log
{
	public $hostip;
	public $acknowledge=false;
	public $counter=0;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Syslog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'archive';
	}
}