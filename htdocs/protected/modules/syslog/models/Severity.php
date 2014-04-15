<?php

/**
 * This is the model class for table "syslog_severity".
 *
 * The followings are the available columns in table 'syslog_severity':
 * @property string $name
 * @property string $description
 * @property string $num
 */
class Severity extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   *
   * @param string $className
   *          active record class name.
   * @return SyslogSeverity the static model class
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
    return 'syslog_severity';
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
        array (
            'name, description',
            'length',
            'max' => 255 
        ),
        array (
            'num',
            'length',
            'max' => 20 
        ),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array (
            'name, description, num',
            'safe',
            'on' => 'search' 
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
    return array ();
  }
  
  /**
   *
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array (
        'name' => 'Name',
        'description' => 'Description',
        'num' => 'Num' 
    );
  }
  
  /**
   * Retrieves a list of models based on the current search/filter conditions.
   *
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search()
  {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.
    $criteria = new CDbCriteria ();
    
    $criteria->compare ( 'name', $this->name, true );
    $criteria->compare ( 'description', $this->description, true );
    $criteria->compare ( 'num', $this->num, true );
  if(Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize'])==0)
    $pagination=false;
  else
	 $pagination=array('pageSize'=>Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']));
      
    return new CActiveDataProvider ( $this, array (
        'criteria' => $criteria,
        'pagination'=>$pagination, 
    ) );
  }
  public function getLabel()
  {
    switch (intval($this->num))
    {
      case "0" :
      case "1" :
      case "2" :
        return 'important';
        break;
      case 3 :
        return 'inverse';
        break;
      case 4 :
        return 'important';
        break;
      case 5 :
        return 'warning';
        break;
      case 6 :
      case 7 :
      default :
        return 'default';
    }
  return 'default';
  }
}