<?php

/**
 * This is the model class for table "abuser_incident".
 *
 * The followings are the available columns in table 'abuser_incident':
 * @property string $id
 * @property string $ip
 * @property string $trigger_id
 * @property string $counter
 * @property string $first_occurrence
 * @property string $last_occurrence
 * @property string $ts
 *
 * The followings are the available model relations:
 * @property AbuserTrigger $trigger
 */
class AbuserIncident extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   * 
   * @param string $className
   *          active record class name.
   * @return AbuserIncident the static model class
   */
  public $ipstr, $total_counter;
  public $intelligent = false;
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
    return 'abuser_incident';
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
        array ('ip, ts','required'),
        array ('ip','length','max' => 10),
        array (
            'trigger_id, counter, first_occurrence, last_occurrence',
            'length',
            'max' => 20 
        ),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array (
            'id, ip, intelligent,trigger_id, counter, first_occurrence, last_occurrence, ts,ipstr',
            'safe',
            'on' => 'search' 
        ),
        array (
            'id, ip, intelligent,trigger_id, counter, first_occurrence, last_occurrence, ts,ipstr',
            'safe',
            'on' => 'intelligent' 
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
        'trigger' => array (self::BELONGS_TO,'AbuserTrigger','trigger_id'),
        'AE' => array (
            self::HAS_MANY,
            'AbuserEvidence',
            'incident_id' 
        ),
        'evidence' => array (self::HAS_MANY,'Archive',array ('archive_id' => 'id'),'through' => 'AE') 
    );
  }
  
  /**
   *
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array (
        'id' => 'ID',
        'ip' => 'Ip',
        'ipstr' => 'IP',
        'trigger_id' => 'Trigger',
        'counter' => 'Counter',
        'first_occurrence' => 'First Occurrence',
        'last_occurrence' => 'Last Occurrence',
        'ts' => 'Ts' 
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
    
    $criteria->compare ( 'id', $this->id, true );
    //$criteria->compare ( 'ip', $this->ip, true );
    $withmask=explode('/',$this->ipstr);
    $ipaddr=Host::strip_comparison($withmask[0]);
    $cmp=Host::get_comparison($withmask[0]);
    if(isset($withmask[1]))
    {
       $netmask=Host::netmask($withmask[1]);
       if($netmask!==false)
       {
          $network=ip2long($ipaddr) & ip2long($netmask);
          $criteria->compare("ip & inet_aton('$netmask')",$network);
          $criteria->compare('ip',$cmp.ip2long($ipaddr));
       }
    }
    else
    {
       $criteria->compare('inet_ntoa(ip)',$ipaddr,true);
       if(ip2long($this->ip)!==false)
          $criteria->compare('ip',ip2long($ipaddr),false,'OR');
    }
    $criteria->compare ( 'trigger_id', $this->trigger_id, true );
    $criteria->compare ( 'counter', $this->counter, true );
    $criteria->compare ( 'inet_ntoa(ip)', $this->ipstr, true );
    $criteria->compare ( 'first_occurrence', $this->first_occurrence, true );
    $criteria->compare ( 'last_occurrence', $this->last_occurrence, true );
    $criteria->compare ( 'ts', $this->ts, true );
    if (Yii::app ()->user->getState ( 'pageSize', Yii::app ()->params ['defaultPageSize'] ) == 0)
      $pagination = false;
    else
      $pagination = array (
          'pageSize' => Yii::app ()->user->getState ( 'pageSize', Yii::app ()->params ['defaultPageSize'] ) 
      );
    
    if ($this->scenario == 'intelligent')
    {
      $trigger_timeout = 60 * 60;
      $criteria->compare ( 'abs(unix_timestamp(first_occurrence)-unix_timestamp(last_occurrence))', '>' . $trigger_timeout );
      // $criteria->addBetweenCondition($column, $valueStart, $valueEnd, 'AND');
    }
    return new CActiveDataProvider ( $this, array (
        'criteria' => $criteria,
        'pagination' => $pagination,
        'sort' => array (
            'defaultOrder' => 'last_occurrence DESC',
            'attributes' => array (
                'ipstr' => array (
                    'asc' => 'inet_ntoa(ip)',
                    'desc' => 'inet_ntoa(ip) DESC' 
                ),
                '*' 
            ) 
        ),
    ) );
  }
  
  
  public function defaultScope()
  {
    return array (
        'select' => '*,inet_ntoa(ip) as ipstr' 
    );
  }

  public function zero($save=false)
  {
    $this->counter=0;
    if($save) $this->save();
  }
}
