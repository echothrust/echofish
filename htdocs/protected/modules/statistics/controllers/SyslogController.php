<?php

class SyslogController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
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

	/**
	 * Manages all models.
	 */
	public function actionDaily()
	{
		$model=new SyslogCountersDaily('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SyslogCountersDaily']))
			$model->attributes=$_GET['SyslogCountersDaily'];

		$this->render('daily',array(
			'model'=>$model,
		));
	}
	public function actionOverall()
	{
		$model=new SyslogCounters('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SyslogCounters']))
			$model->attributes=$_GET['SyslogCounters'];

		$this->render('overall',array(
			'model'=>$model,
		));
	}

}
