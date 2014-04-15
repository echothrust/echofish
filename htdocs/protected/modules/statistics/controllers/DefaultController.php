<?php

class DefaultController extends Controller
{
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
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
			array('allow', // allow authenticated users
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex()
	{
		$archive=ArchiveCounters::model();
		$archive_daily=ArchiveCountersDaily::model();
		$syslog=SyslogCounters::model();
		$syslog_daily=SyslogCountersDaily::model();


		$this->render('index',array(
			'archive'=>$archive,
			'archive_daily'=>$archive_daily,
			'syslog'=>$syslog,
			'syslog_daily'=>$syslog_daily,
		));
	}
	public function actionHelp($section=null)
	{
	  $this->render('help',array('section'=>$section));
	}

	public function actionReport()
	{
	  //$this->layout='//layouts/print';
	  $this->renderPartial('report');
	}
	
}