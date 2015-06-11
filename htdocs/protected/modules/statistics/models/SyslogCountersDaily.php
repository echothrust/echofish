<?php

/**
 * This is the model class for table "syslog_counters_daily".
 *
 * The followings are the available columns in table 'syslog_counters_daily':
 * @property string $ctype
 * @property string $name
 * @property string $val
 * @property string $ts
 */
class SyslogCountersDaily extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'syslog_counters_daily';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ctype, name', 'required'),
			array('ctype', 'length', 'max'=>32),
			array('name', 'length', 'max'=>255),
			array('val', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ctype, name, val, ts', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ctype' => 'Ctype',
			'name' => 'Name',
			'val' => 'Val',
			'ts' => 'Ts',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ctype',$this->ctype,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('val',$this->val,true);
		$criteria->compare('ts',$this->ts,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SyslogCountersDaily the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function scopes()
	{
		return array(
		'todays'=>array(
				'condition'=>'ts=date(now())'
			),
		'todays_facilities'=>array(
				'condition'=>'ctype="facility" and ts=date(now())',
				'order'=>'CONVERT(name,UNSIGNED INTEGER)',
			),
		'todays_severities'=>array(
				'condition'=>'ctype="level" and ts=date(now())',
				'order'=>'CONVERT(name,UNSIGNED INTEGER)',
			),
		'todays_programs'=>array(
				'condition'=>'ctype="program" and ts=date(now()) and val>0',
				'order'=>'val desc,name',
			),

		'todays_hosts'=>array(
				'select'=>'ctype,(SELECT host.fqdn FROM `host` WHERE id=name) as name,val',
				'condition'=>'ctype="host" and ts=date(now())',
				'order'=>'name',
			),

		'today_totals'=>array(
				'select'=>'ctype,"all" as name, sum(val)',
				'group'=>'ctype',
				'condition'=>'ts=date(now())',
			),
		);
	}
	public function getConverted_name()
	{
		switch($this->ctype)
		{
			case 'host':
				return long2ip($this->name);
			default:
				return $this->name;
		}
	}

}
