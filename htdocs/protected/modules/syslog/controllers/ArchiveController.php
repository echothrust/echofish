<?php
class ArchiveController extends Controller {
	/**
	 *
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 *      using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $defaultAction = 'admin';
	public $layout = '//layouts/column2';

	/**
	 *
	 * @return array action filters
	 */
	public function filters() {
		return array (
				'accessControl'
		) // perform access control for CRUD operations
;
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 *
	 * @return array access control rules
	 */
	public function accessRules() {
		return array (
				array (
						'allow', // allow authenticated user to perform 'create' and 'update' actions
						'users' => array (
								'@'
						)
				),
				array (
						'deny', // deny all users
						'users' => array (
								'*'
						)
				)
		);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id
	 *        	the ID of the model to be deleted
	 */
	public function actionTruncate() {
		// we only allow deletion via POST request
		$conn = Yii::app ()->db;
		$mydb = Yii::app ()->db->createCommand ( "SELECT DATABASE()" )->queryScalar ();
		$AUTO_INCREMENT_NO = Yii::app ()->db->createCommand ()->select ( 'AUTO_INCREMENT' )->from ( 'INFORMATION_SCHEMA.TABLES' )->where ( 'TABLE_SCHEMA=:TS AND TABLE_NAME=:TN', array (
				':TS' => $mydb,
				':TN' => 'archive'
		) )->queryScalar ();
		$conn->createCommand ( 'TRUNCATE archive' )->execute ();
		$conn->createCommand ( 'TRUNCATE archive_unparse' )->execute ();
		$conn->createCommand ( "ALTER TABLE archive AUTO_INCREMENT=$AUTO_INCREMENT_NO" )->execute ();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (! isset ( $_GET ['ajax'] ))
			$this->redirect ( isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
					'admin'
			) );
	}
	public function actionAdmin($from = null, $to = null, $divider = 1) {
		$model = new Archive ( 'search' );
		$model->unsetAttributes (); // clear any default values
		if (isset ( $_GET ['archivePageSize'] )) {
			Yii::app ()->user->setState ( 'archivePageSize', ( int ) $_GET ['archivePageSize'] );
			unset ( $_GET ['archivePageSize'] ); // would interfere with pager and repetitive page size change
		}
		if (isset ( $_GET ['Archive'] ))
			$model->attributes = $_GET ['Archive'];

		if ($from != null && $to != null) {
			$model->fromTS = $from / intval ( $divider );
			$model->toTS = $to / intval ( $divider );
		}
		$this->render ( 'admin', array (
				'model' => $model
		) );
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param
	 *        	integer the ID of the model to be loaded
	 */
	public function loadModel($id) {
		$model = Archive::model ()->findByPk ( $id );
		if ($model === null)
			throw new CHttpException ( 404, 'The requested page does not exist.' );
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param
	 *        	CModel the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'archive-form') {
			echo CActiveForm::validate ( $model );
			Yii::app ()->end ();
		}
	}
}
