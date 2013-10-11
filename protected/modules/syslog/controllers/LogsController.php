<?php

class LogsController extends Controller
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','acknowledge','massack'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Acknowledge a syslog entry and other similar.
 	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionAcknowledge($id)
	{
		$model=Syslog::model()->findByPk($id);
		if($model)
		{
			$c=new CDbCriteria;
			$c->condition='host=:host and facility=:facility and level=:level and pid=:pid and msg like :msg';
			$c->params=array(
				':host'=>$model->host,
				':facility'=>$model->facility,
				':level'=>$model->level,
				':pid'=>$model->pid,
				':msg'=>$model->msg,
			);
			Syslog::model()->deleteAll($c);
		}
		$this->redirect(array('admin'));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Syslog');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Syslog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Syslog']))
			$model->attributes=$_GET['Syslog'];
		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionMassack()
	{
		$model=new Syslog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Syslog']))
		{
			$model->attributes=$_POST['Syslog'];
			$model->acknowledge=true;
			var_dump($model->search()->getData());
		}
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Syslog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='syslog-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
