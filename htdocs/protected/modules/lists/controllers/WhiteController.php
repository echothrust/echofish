<?php
class WhiteController extends Controller {
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
        'accessControl'  // perform access control for CRUD operations
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
            'allow', // allow authenticated users
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
    $model = new Whitelist ();
    
    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);
    
    if (isset ( $_POST ['Whitelist'] ))
    {
      $model->attributes = $_POST ['Whitelist'];
      $conn = Yii::app ()->db;
      $trans = $conn->beginTransaction ();
      try
      {
        $model->save ();
        $trans->commit ();
      } catch ( Exception $e )
      {
        $trans->rollback ();
      }
      $this->redirect ( array (
          'admin' 
      ) );
    }
    
    $this->render ( 'create', array (
        'model' => $model 
    ) );
  }
  
  /**
   * Creates a new model .
   *
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionFromsyslog($syslog_id)
  {
    $entry = Syslog::model ()->findByPk ( $syslog_id );
    
    $model = new Whitelist ();
    $model->host = $entry->hostip;
    $model->facility = $entry->facility;
    $model->level = $entry->level;
    $model->program = $entry->program;
    $model->pattern = addcslashes ( $entry->msg, '\\' );
    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);
    
    if (isset ( $_POST ['Whitelist'] ))
    {
      $model->attributes = $_POST ['Whitelist'];
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
    
    if (isset ( $_POST ['Whitelist'] ))
    {
      $model->attributes = $_POST ['Whitelist'];
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
    if (Yii::app ()->request->isPostRequest)
    {
      // we only allow deletion via POST request
      $this->loadModel ( $id )->delete ();
      
      // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
      if (! isset ( $_GET ['ajax'] ))
        $this->redirect ( isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
            'admin' 
        ) );
    } else
      throw new CHttpException ( 400, 'Invalid request. Please do not repeat this request again.' );
  }
  public function actionOptimise()
  {
    // we only allow deletion via POST request
    $conn = Yii::app ()->db;
    $conn->createCommand ( 'CALL delete_duplicate_whitelist()' )->execute ();
    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (! isset ( $_GET ['ajax'] ))
      $this->redirect ( isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
          'admin' 
      ) );
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
        foreach ( ( array ) $record->attributes () as $val )
        {
          if (Whitelist::model ()->findByAttributes ( $val ) === null)
          {
            $tr = new Whitelist ();
            $tr->attributes = $val;
            if ($tr->validate ())
              $tr->save ();
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
  
  /**
   * Manages all models.
   */
  public function actionAdmin()
  {
    $model = new Whitelist ( 'search' );
    $model->unsetAttributes (); // clear any default values
    if (isset ( $_GET ['pageSize'] ))
    {
      Yii::app ()->user->setState ( 'pageSize', ( int ) $_GET ['pageSize'] );
      unset ( $_GET ['pageSize'] ); // would interfere with pager and repetitive page size change
    }
    if (isset ( $_GET ['Whitelist'] ))
      $model->attributes = $_GET ['Whitelist'];
    
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
    $model = Whitelist::model ()->findByPk ( $id );
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
    if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'whitelist-form')
    {
      echo CActiveForm::validate ( $model );
      Yii::app ()->end ();
    }
  }
  public function actionExport()
  {
    $exported = Yii::app ()->db->createCommand ( "SELECT * FROM whitelist" )->queryAll ();
    $xml = new SimpleXMLElement ( '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><whitelist></whitelist>' );
    $xml->addAttribute ( 'generation_ts', time () );
    foreach ( $exported as $record )
    {
      $record_xml = $xml->addChild ( 'record' );
      $this->arrayTOattributes ( $record, $record_xml );
    }
    header ( 'Content-disposition: attachment; filename="whitelist-backup.xml"' );
    header ( 'Content-type: text/xml' );
    echo $xml->asXML ();
    Yii::app ()->end ();
  }
  protected function arrayTOattributes($arr, $xml)
  {
    foreach ( $arr as $key => $val )
      $xml->addAttribute ( $key, $val );
  }
}
