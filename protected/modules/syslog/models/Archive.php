<?php

/**
 * This is the model class for table "archive".
 *
 * The followings are the available columns in table 'archive':
 * @property string $id
 * @property string $host
 * @property string $facility
 * @property string $priority
 * @property string $level
 * @property string $program
 * @property string $pid
 * @property string $tag
 * @property string $msg
 * @property string $received_ts
 * @property string $created_at
 * @property string $updated_at
 */
class Archive extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'archive';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('host, created_at, updated_at', 'required'),
			array('host, facility, priority, level, pid', 'length', 'max'=>20),
			array('program, tag', 'length', 'max'=>255),
			array('msg, received_ts', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, host, facility, priority, level, program, pid, tag, msg, received_ts, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'host' => 'Host',
			'facility' => 'Facility',
			'priority' => 'Priority',
			'level' => 'Level',
			'program' => 'Program',
			'pid' => 'Pid',
			'tag' => 'Tag',
			'msg' => 'Msg',
			'received_ts' => 'Received Ts',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('host',$this->host,true);
		$criteria->compare('facility',$this->facility,true);
		$criteria->compare('priority',$this->priority,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('program',$this->program,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('received_ts',$this->received_ts,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Archive the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
