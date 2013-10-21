<?php

/**
 * This is the model class for table "abuser_trigger".
 *
 * The followings are the available columns in table 'abuser_trigger':
 * @property string $id
 * @property integer $facility
 * @property integer $severity
 * @property string $program
 * @property string $msg
 * @property string $pattern
 * @property integer $grouping
 * @property integer $capture
 * @property string $description
 * @property integer $occurrence
 * @property integer $priority
 *
 * The followings are the available model relations:
 * @property AbuserIncident[] $abuserIncidents
 */
class AbuserTrigger extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AbuserTrigger the static model class
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
		return 'abuser_trigger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('facility, severity, grouping, capture, occurrence, priority', 'numerical', 'integerOnly'=>true),
			array('program', 'length', 'max'=>255),
			array('msg, pattern', 'length', 'max'=>512),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, facility, severity, program, msg, pattern, grouping, capture, description, occurrence, priority', 'safe', 'on'=>'search'),
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
			'abuserIncidents' => array(self::HAS_MANY, 'AbuserIncident', 'trigger_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'facility' => 'Facility',
			'severity' => 'Severity',
			'program' => 'Program',
			'msg' => 'Msg',
			'pattern' => 'Pattern',
			'grouping' => 'Grouping',
			'capture' => 'Capture',
			'description' => 'Description',
			'occurrence' => 'Occurrence',
			'priority' => 'Priority',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('facility',$this->facility);
		$criteria->compare('severity',$this->severity);
		$criteria->compare('program',$this->program,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('pattern',$this->pattern,true);
		$criteria->compare('grouping',$this->grouping);
		$criteria->compare('capture',$this->capture);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('occurrence',$this->occurrence);
		$criteria->compare('priority',$this->priority);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}