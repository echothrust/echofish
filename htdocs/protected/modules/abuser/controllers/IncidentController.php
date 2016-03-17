<?php
class IncidentController extends Controller {
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
  
  public function actionReset($id)
  {
    $model = $this->loadModel ( $id );
    $model->counter = 0;
    if ($model->save ())
    {
      foreach ( $model->AE as $ae )
      {
        $s = Syslog::model ()->findByPk ( $ae->archive_id );
        if ($s)
          $s->delete ();
      }
      $this->redirect ( array (
          'view',
          'id' => $model->id 
      ) );
    }
  }

  public function actionZeromass($ids)
  {
    $idz=explode(",",$ids);
    foreach($idz as $i=>$id)
        $this->loadModel ( intval($id) )->zero(true);
    if (! isset ( $_GET ['ajax'] ))
      $this->redirect ( isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
          'admin' 
      ) );//    echo $this->createUrl ( 'admin' );
    Yii::app ()->end ();
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
  public function actionWhois($id)
  {
    $model = $this->loadModel ( $id );
    $whois = new Whois ();
    $this->renderPartial ( 'whois', array (
        'model' => $model,
        'data' => $whois->whoislookup ( $model->ipstr ) 
    ) );
  }
  public function actionCheckbl($id)
  {
    $model = $this->loadModel ( $id );
    $octet = explode ( '.', $model->ipstr );
    $revip = sprintf ( "%d.%d.%d.%d", $octet [3], $octet [2], $octet [1], $octet [0] );
    $dnsbls = array (
        "xbl.spamhaus.org",
        "cbl.abuseat.org",
        "zen.spamhaus.org",
        "dul.dnsbl.sorbs.net" 
    );
    $this->renderPartial ( 'checkbl', array (
        'model' => $model,
        'revip' => $revip,
        'dnsbl' => $dnsbls 
    ) );
  }
  // xbl.spamhaus.org
  /**
   * Lists all models.
   */
  public function actionIndex()
  {
    $dataProvider = new CActiveDataProvider ( 'AbuserIncident' );
    
    $this->render ( 'index', array (
        'dataProvider' => $dataProvider 
    ) );
  }
  
  /**
   * Manages all models.
   */
  public function actionAdmin()
  {
    $model = new AbuserIncident ( 'search' );
    $model->unsetAttributes (); // clear any default values
    if (isset ( $_GET ['pageSize'] ))
    {
      Yii::app ()->user->setState ( 'pageSize', ( int ) $_GET ['pageSize'] );
      unset ( $_GET ['pageSize'] ); // would interfere with pager and repetitive page size change
    }
    if (isset ( $_GET ['AbuserIncident'] ))
      $model->attributes = $_GET ['AbuserIncident'];
    if (isset ( $_GET ['ajax'] ))
      $this->renderPartial ( '_grid', array (
          'model' => $model 
      ) );
    else
      $this->render ( 'admin', array (
          'model' => $model 
      ) );
  }
  public function actionIntelligent()
  {
    $model = new AbuserIncident ( 'intelligent' );
    $model->unsetAttributes (); // clear any default values
    if (isset ( $_GET ['AbuserIncident'] ))
      $model->attributes = $_GET ['AbuserIncident'];
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
    $model = AbuserIncident::model ()->findByPk ( $id );
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
    if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'abuser-incident-form')
    {
      echo CActiveForm::validate ( $model );
      Yii::app ()->end ();
    }
  }
}
