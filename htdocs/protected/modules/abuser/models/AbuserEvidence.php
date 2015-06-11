<?php

/**
 * This is the model class for table "abuser_evidence".
 *
 * The followings are the available columns in table 'abuser_evidence':
 * @property string $incident_id
 * @property string $archive_id
 *
 * The followings are the available model relations:
 * @property Archive $archive
 */
class AbuserEvidence extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *          active record class name.
	 * @return AbuserEvidence the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model ( $className );
	}

	/**
	 *
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'abuser_evidence';
	}

	/**
	 *
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
				array ('incident_id', 'required'),
				array ('incident_id', 'length','max' => 10),
				array ('archive_id', 'length', 'max' => 20),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array ('incident_id, archive_id', 'safe', 'on' => 'search'
				)
		);
	}

	/**
	 *
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
				'incident' => array (
						self::BELONGS_TO,
						'AbuserIncident',
						'incident_id'
				),
				'archive' => array (
						self::BELONGS_TO,
						'Archive',
						'archive_id'
				)
		);
	}

	/**
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array (
				'incident_id' => 'Incident',
				'archive_id' => 'Archive'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria = new CDbCriteria ();

		$criteria->compare ( 'incident_id', $this->incident_id, true );
		$criteria->compare ( 'archive_id', $this->archive_id, true );

		if (Yii::app ()->user->getState ( 'pageSize', Yii::app ()->params ['defaultPageSize'] ) == 0)
			$pagination = false;
		else
			$pagination = array (
					'pageSize' => Yii::app ()->user->getState ( 'pageSize', Yii::app ()->params ['defaultPageSize'] )
			);

		return new CActiveDataProvider ( $this, array (
				'criteria' => $criteria,
				'pagination' => $pagination
		) );
	}
}