<?php

/**
 * This is the model class for table "whitelist".
 *
 * The followings are the available columns in table 'whitelist':
 * @property string $id
 * @property string $description
 * @property string $host
 * @property string $facility
 * @property string $level
 * @property string $program
 * @property string $pattern
 */
class Whitelist extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Whitelist the static model class
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
		return 'whitelist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('host', 'required'),
			array('host, facility, level', 'length', 'max'=>20),
			array('program', 'length', 'max'=>50),
			array('pattern', 'length', 'max'=>512),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, host, facility, level, program, pattern', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'description' => 'Description',
			'host' => 'Host',
			'facility' => 'Facility',
			'level' => 'Level',
			'program' => 'Program',
			'pattern' => 'Pattern',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('host',$this->host,true);
		$criteria->compare('facility',$this->facility,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('program',$this->program,true);
		$criteria->compare('pattern',$this->pattern,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}