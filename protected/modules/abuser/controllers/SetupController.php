<?php

class SetupController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function actionActivate()
	{
      $setup=new setup();
      $setup->up();
	}

	public function actionDeactivate()
	{
		$setup=new setup();
		$setup->down();
	}

	public function actionConfigure()
	{
	}
}


class setup extends CDbMigration
{
    public function up()
    {
        Yii::app()->db->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
        $this->createTable('abuser_trigger', array(
            'id' => 'bigint primary key auto_increment',
            'facility' => 'tinyint',
            'severity' => 'tinyint',
            'program' => 'varchar(255)',
            'msg' => 'varchar(512)',
            'pattern' => 'varchar(512)',
            'grouping' => 'tinyint UNSIGNED',
            'capture' => 'tinyint UNSIGNED',
            'description'=>'text',
            'occurrence' => 'int',
            'priority'=>'tinyint',          
        ),'ENGINE=INNODB');

        $this->createTable('abuser_incident', array(
            'id' => 'bigint primary key auto_increment',
            'ip' => 'int unsigned not null',
            'trigger_id' => 'BIGINT',
            'counter' => 'BIGINT',
            'first_occurrence' => 'DATETIME',
            'last_occurrence' => 'DATETIME',
            'ts' => 'TIMESTAMP',
        ),'ENGINE=INNODB');

        $this->createIndex('idx_uniq_incident', 'abuser_incident','ip,trigger_id' , true); 
        $this->addForeignKey('fk_trigger_id','abuser_incident','trigger_id','abuser_trigger','id','CASCADE','CASCADE');
        $this->createTable('abuser_evidence', array(
            'incident_id' => 'bigint not null',
            'archive_id' => 'BIGINT UNSIGNED',
            'PRIMARY KEY (incident_id, archive_id)'
        ),'ENGINE=INNODB');
        $this->addForeignKey('fk_incident_id','abuser_evidence','incident_id','abuser_incident','id','CASCADE','CASCADE');
        $this->addForeignKey('fk_archive_id','abuser_evidence','archive_id','archive','id','CASCADE','CASCADE');

$log_evidence= <<< SQL
CREATE PROCEDURE abuser_log_evidence(IN abuser_id BIGINT UNSIGNED,IN entry_id BIGINT UNSIGNED)
BEGIN
  INSERT INTO abuser_evidence (incident_id,archive_id) VALUES (abuser_id,entry_id);
END;
SQL;

$parser_procedure= <<< SQL
CREATE PROCEDURE abuser_parser(IN aid BIGINT UNSIGNED,IN ahost BIGINT UNSIGNED,IN aprogram VARCHAR(255),IN afacility INT,in alevel INT,IN apid BIGINT,in amsg TEXT,in areceived_ts TIMESTAMP)
BEGIN
DECLARE done,mts INT DEFAULT 0; 
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = -1;

SELECT id,pattern,grouping,capture INTO mts,@pattern,@grouping,@capture FROM abuser_trigger WHERE 
    amsg LIKE msg AND 
    aprogram LIKE if(program='' or program is null,'%%',program) AND 
    afacility like if(facility<0,'%%',facility) AND  
    alevel like if(`severity`<0,'%%',`severity`)
    LIMIT 1;
  IF mts>0 THEN
  INSERT INTO abuser_incident (ip,trigger_id,counter,first_occurrence,last_occurrence) 
    VALUES (INET_ATON(PREG_CAPTURE(@pattern,amsg,@grouping,@capture)),
      mts,1,areceived_ts,areceived_ts)
    ON DUPLICATE KEY UPDATE counter=counter+1,last_occurrence=areceived_ts;
    SELECT id INTO @incident_id FROM abuser_incident WHERE ip=INET_ATON(PREG_CAPTURE(@pattern,amsg,@grouping,@capture)) AND trigger_id=mts;
    CALL abuser_log_evidence(@incident_id,aid);
  END IF;
END;
SQL;


    $this->execute('DROP PROCEDURE IF EXISTS `abuser_log_evidence`');
    $this->execute('DROP PROCEDURE IF EXISTS `abuser_parser`');
    $this->execute($log_evidence);
    $this->execute($parser_procedure);
    $this->execute('INSERT INTO archive_parser (ptype,name) value ("archive","abuser_parser")');
      
    }
 
    public function down()
    {
        Yii::app()->db->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
        $this->dropTable('abuser_evidence');
        $this->dropTable('abuser_incident');
        $this->dropTable('abuser_trigger');
        $this->execute('DELETE FROM archive_parser WHERE name="abuser_parser"');
        $this->execute('DROP PROCEDURE IF EXISTS `abuser_log_evidence`');
        $this->execute('DROP PROCEDURE IF EXISTS `abuser_parser`');
    }
}