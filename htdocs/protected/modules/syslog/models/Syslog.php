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
class Syslog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Syslog the static model class
	 */
	public $hostip;
	public $acknowledge=false;
	public $counter=0;
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

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('host', 'required'),
				array('facility, priority, level', 'numerical', 'integerOnly'=>true),
				array('host', 'length', 'max'=>20),
				array('program, tag', 'length', 'max'=>255),
				array('pid', 'length', 'max'=>11),
				array('msg, received_ts, created_at,hostip,acknowledge,counter', 'safe'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, host, massack,facility, priority, level, program, pid, tag, msg, received_ts, created_at,hostip,counter', 'safe', 'on'=>'search'),
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
				'lHost'=>array(self::HAS_ONE,'Host',array('id'=>'host')),
				'facil'=>array(self::HAS_ONE,'Facility',array('num'=>'facility')),
				'sever'=>array(self::HAS_ONE,'Severity',array('num'=>'level')),
				'memos'=>array(self::HAS_MANY,'SyslogMemo',array('syslog_id'=>'id')),
				'total_memos'=>array(self::STAT,'SyslogMemo','syslog_id'),

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

	public function scopes()
	{
		return array(
				'published'=>array(
						'condition'=>'status=1',
				),
				'recently'=>array(
						'order'=>'create_time DESC',
						'limit'=>5,
				),
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
				->from('syslog')
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
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$criteria->together = true;
		$criteria->compare('id',$this->id,true);
		//$criteria->compare('host',$this->host,true);
		$withmask=explode('/',$this->hostip);
		$ip=Host::strip_comparison($withmask[0]);
		$cmp=Host::get_comparison($withmask[0]);
		if(isset($withmask[1]))
		{
			$netmask=Host::netmask($withmask[1]);
			if($netmask!==false)
			{
				$network=ip2long($ip) & ip2long($netmask);
				$criteria->compare("host.ip & inet_aton('$netmask')",$network);
				$criteria->compare('host.ip',$cmp.ip2long($ip),true);
			}
		} 
		else
		{ 
			$criteria->compare('inet6_ntoa(host.ip)',$ip,true,'OR');
			if(filter_var($this->hostip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
				$criteria->compare('inet6_ntoa(host.ip)',$ip,false,'OR');
			$criteria->compare('host.fqdn',$this->hostip,true,'OR');
			$criteria->compare('host.short',$this->hostip,true,'OR');
		}
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
			if($criteria->condition!="")
				$cmd = Yii::app()->db->createCommand("DELETE syslog.* FROM syslog LEFT JOIN host ON host.id=syslog.host WHERE ".$criteria->condition);
			else
				$cmd = Yii::app()->db->createCommand("DELETE syslog.* FROM syslog");
			foreach($criteria->params as $key=>$val) $cmd->bindParam($key,$val);
			$cmd->execute();
//			Syslog::model()->deleteAll($criteria->condition,$criteria->params);
			$this->unsetAttributes();
			return new CActiveDataProvider($this, array(
					'sort'=>array(
							'defaultOrder'=>'received_ts DESC',
							'attributes'=>array(
									'hostip'=>array(
											'asc'=>'inet6_ntoa(host.ip)',
											'desc'=>'inet6_ntoa(host.ip) DESC',
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
										'asc'=>'inet6_ntoa(host.ip)',
										'desc'=>'inet6_ntoa(host.ip) DESC',
								),
								'*',
						),
				),
				'pagination'=>$pagination,
		));
	}
	public function getMsg()
	{
		return wordwrap($this->msg,75,"\n",true);
	}
}