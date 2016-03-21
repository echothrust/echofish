<?php

/**
 * Generic class for Log table format operations
 * extended by syslog and archive
 */

Abstract class Log extends CActiveRecord
{
	public $hostip;
	public $acknowledge=false;
	public $counter=0;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				array('host', 'required'),
				array('facility, priority, level', 'numerical', 'integerOnly'=>true),
				array('host', 'length', 'max'=>20),
				array('program, tag', 'length', 'max'=>255),
				array('pid', 'length', 'max'=>11),
				array('msg, received_ts, created_at,hostip,acknowledge,counter', 'safe'),
				// The following rule is used by search().
				array('id, host, massack,facility, priority, level, program, pid, tag, msg, received_ts, created_at,hostip,counter', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
				'lHost'=>array(self::HAS_ONE,'Host',array('id'=>'host')),
				'facil'=>array(self::HAS_ONE,'Facility',array('num'=>'facility')),
				'sever'=>array(self::HAS_ONE,'Severity',array('num'=>'level')),
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
				'hostip' => 'Host IP',
				'facility' => 'Facility',
				'priority' => 'Priority',
				'level' => 'Level',
				'program' => 'Program',
				'pid' => 'Pid',
				'tag' => 'Tag',
				'msg' => 'Msg',
				'received_ts' => 'Received Ts',
				'created_at' => 'Created At',
				'total_memos' => 'Memos',
		);
	}

	public function defaultScope()
	{
		return array(
				'select'=>'t.*,inet6_ntoa(host.ip) as hostip',
				'join'=>'LEFT JOIN host ON host=host.id',
				'order'=>'received_ts DESC',
		);
	}


	public function total($field)
	{
		return intval(Yii::app()->db->createCommand()
				->select("count(distinct `$field`)")
				->from($this->tableName)
				->queryScalar());
	}
	public function distinct($field)
	{
		$c=new CDbCriteria;
		$c->select="*,count(*) as counter";
		$c->group="$field";
		$c->order='counter desc';
		return new CActiveDataProvider($this, array(
				'criteria'=>$c,));

	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->together = true;
		$criteria->compare('id',$this->id,true);
		if(filter_var($this->hostip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			$criteria->compare('inet6_ntoa(host.ip)',$this->hostip,false,'OR');
		else
			$criteria->compare('inet6_ntoa(host.ip)',$this->hostip,true,'OR');

		$criteria->compare('host.fqdn',$this->hostip,true,'OR');
		$criteria->compare('host.short',$this->hostip,true,'OR');
		$criteria->compare('facility',$this->facility);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('level',$this->level);
		$criteria->compare('program',$this->program,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('received_ts',$this->received_ts,true);
		$criteria->compare('created_at',$this->created_at,true);
		if(Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize'])==0)
			$pagination=false;
		else
			$pagination=array('pageSize'=>Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']));
		if($this->acknowledge!==false)
		{
			$this->acknowledge_logs();
			$this->unsetAttributes();
			return new CActiveDataProvider($this, array(
					'sort'=>array(
							'defaultOrder'=>'received_ts DESC',
							'attributes'=>array(
									'hostip'=>array(
											'asc'=>'INET6_NTOA(host.ip)',
											'desc'=>'INET6_NTOA(host.ip) DESC',
									),
									'*',
							),
					),
					'pagination'=>$pagination,
			));
		}
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'sort'=>array(
						'defaultOrder'=>'received_ts DESC',
						'attributes'=>array(
								'hostip'=>array(
										'asc'=>'INET6_NTOA(host.ip)',
										'desc'=>'INET6_NTOA(host.ip) DESC',
								),
								'*',
						),
				),
				'pagination'=>$pagination,
		));
	}
	
	public function acknowledge_logs()
	{
		
	}
}