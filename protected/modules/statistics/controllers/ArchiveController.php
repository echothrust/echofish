<?php

class ArchiveController extends Controller
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
		$model=new ArchiveCountersDaily('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ArchiveCountersDaily']))
			$model->attributes=$_GET['ArchiveCountersDaily'];

		$this->render('daily',array(
			'model'=>$model,
		));
	}
	public function actionOverall()
	{
		$model=new ArchiveCounters('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ArchiveCounters']))
			$model->attributes=$_GET['ArchiveCounters'];

		$this->render('overall',array(
			'model'=>$model,
		));
	}

}
