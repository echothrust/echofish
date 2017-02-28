<?php

/**
 * This is the model class for table "syslog".
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
class Syslog extends Log
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
		return 'syslog';
	}

	public function acknowledge_logs($criteria=null)
	{
		if($criteria->condition!="")
			$cmd = Yii::app()->db->createCommand("DELETE t1.* FROM syslog as t1 LEFT JOIN host ON host.id=t1.host WHERE ".$criteria->condition);
		else
			$cmd = Yii::app()->db->createCommand("DELETE t1.* FROM syslog as t1");
		$cmd->execute($criteria->params);
	}
}