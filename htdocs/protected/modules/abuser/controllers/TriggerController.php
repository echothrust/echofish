<?php
class TriggerController extends Controller {
  /**
   *
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   *      using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column2';
  
  /**
   *
   * @return array action filters
   */
  public function filters()
  {
    return array (
        'accessControl', // perform access control for CRUD operations
        'postOnly + delete'  // we only allow deletion via POST request
        );
  }
  
  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   *
   * @return array access control rules
   */
  public function accessRules()
  {
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
   * Displays a particular model.
   *
   * @param integer $id
   *          the ID of the model to be displayed
   */
  public function actionView($id)
  {
    $this->render ( 'view', array (
        'model' => $this->loadModel ( $id ) 
    ) );
  }
  
  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate()
  {
    $model = new AbuserTrigger ();
    
    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);
    
    if (isset ( $_POST ['AbuserTrigger'] ))
    {
      $model->attributes = $_POST ['AbuserTrigger'];
      if ($model->save ())
        $this->redirect ( array (
            'view',
            'id' => $model->id 
        ) );
    }
    
    $this->render ( 'create', array (
        'model' => $model 
    ) );
  }
  
  /**
   * Creates a new model based on a specific message from Syslog.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionFromsyslog($syslog_id)
  {
  	$entry = Syslog::model ()->findByPk ( $syslog_id );
  
  	$model = new AbuserTrigger ();
  	$model->facility = $entry->facility;
  	$model->severity = $entry->level;
  	$model->program = $entry->program;
  	$model->msg = addcslashes ( $entry->msg, '\\' );
  	$model->pattern = '/^(.*?)/';
  	$model->grouping = 1;
  	$model->capture = 1;
  	$model->occurrence = 1;
  	$model->priority = 1;
  	// Uncomment the following line if AJAX validation is needed
  	// $this->performAjaxValidation($model);
  
  	if (isset ( $_POST ['AbuserTrigger'] ))
  	{
  		$model->attributes = $_POST ['AbuserTrigger'];
  		if ($model->save ())
  			$this->redirect ( array (
  					'/syslog/logs/admin'
  			) );
  	}
  	$this->render ( 'create', array (
  			'model' => $model
  	) );
  }
  
  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *          the ID of the model to be updated
   */
  public function actionUpdate($id)
  {
    $model = $this->loadModel ( $id );
    
    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);
    
    if (isset ( $_POST ['AbuserTrigger'] ))
    {
      $model->attributes = $_POST ['AbuserTrigger'];
      if ($model->save ())
        $this->redirect ( array (
            'view',
            'id' => $model->id 
        ) );
    }
    
    $this->render ( 'update', array (
        'model' => $model 
    ) );
  }
  
  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   *
   * @param integer $id
   *          the ID of the model to be deleted
   */
  public function actionDelete($id)
  {
    $this->loadModel ( $id )->delete ();
    
    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (! isset ( $_GET ['ajax'] ))
      $this->redirect ( isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
          'admin' 
      ) );
  }
  
  /**
   * Lists all models.
   */
  public function actionIndex()
  {
    $dataProvider = new CActiveDataProvider ( 'AbuserTrigger' );
    $this->render ( 'index', array (
        'dataProvider' => $dataProvider 
    ) );
  }
  
  /**
   * Manages all models.
   */
  public function actionAdmin()
  {
    $model = new AbuserTrigger ( 'search' );
    $model->unsetAttributes (); // clear any default values
  	  if (isset($_GET['pageSize'])) 
  	{
      Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
      unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
    }
    if (isset ( $_GET ['AbuserTrigger'] ))
      $model->attributes = $_GET ['AbuserTrigger'];
    if (isset ( $_GET ['ajax'] ))
      $this->renderPartial ( '_grid', array (
          'model' => $model 
      ) );
    else
      $this->render ( 'admin', array (
          'model' => $model 
      ) );
  }
  
  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   *
   * @param
   *          integer the ID of the model to be loaded
   */
  public function loadModel($id)
  {
    $model = AbuserTrigger::model ()->findByPk ( $id );
    if ($model === null)
      throw new CHttpException ( 404, 'The requested page does not exist.' );
    return $model;
  }
  
  /**
   * Performs the AJAX validation.
   *
   * @param
   *          CModel the model to be validated
   */
  protected function performAjaxValidation($model)
  {
    if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'abuser-trigger-form')
    {
      echo CActiveForm::validate ( $model );
      Yii::app ()->end ();
    }
  }
  public function actionExport()
  {
    $exported = Yii::app ()->db->createCommand ( "SELECT * FROM abuser_trigger" )->queryAll ();
    $xml = new SimpleXMLElement ( '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><abuser_trigger></abuser_trigger>' );
    $xml->addAttribute ( 'generation_ts', time () );
    foreach ( $exported as $record )
    {
      $record_xml = $xml->addChild ( 'record' );
      $this->arrayTOattributes ( $record, $record_xml );
    }
    header('Content-disposition: attachment; filename="trigger-backup.xml"');
    header ( 'Content-type: text/xml' );
    echo $xml->asXML ();
    Yii::app ()->end ();
  }
  protected function arrayTOattributes($arr, $xml)
  {
    foreach ( $arr as $key => $val )
      $xml->addAttribute ( $key, $val );
  }
  public function actionUpload()
  {
    $model = new BackupUpload ();
    $form = new CForm ( 'application.models.uploadForm', $model );
    if ($form->submitted ( 'submit' ) && $form->validate ())
    {
      $form->model->backup = CUploadedFile::getInstance ( $form->model, 'backup' );
      $fc = file_get_contents ( $form->model->backup->tempName );
      $xml = new SimpleXMLElement ( $fc, LIBXML_NOCDATA );
      foreach ( $xml->record as $record )
      {
        foreach((array)$record->attributes() as $val)
        {
          if(AbuserTrigger::model()->findByAttributes($val)===null)
          {
            $tr=new AbuserTrigger;
            $tr->attributes=$val;
            if($tr->validate())
                $tr->save();
          }
        }
          
      }
      Yii::app ()->user->setFlash ( 'success', 'File Uploaded' );
      $this->redirect ( array (
          'upload' 
      ) );
    }
    $this->render ( 'upload', array (
        'form' => $form 
    ) );
  }
}
