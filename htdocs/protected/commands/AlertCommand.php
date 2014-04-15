<?php
Yii::import('application.modules.abuser.models.*');

class AlertCommand extends CConsoleCommand {
    /*
     * mail abuser incidents that made them selvs public 
     * durint the last $interval minutes
     */
    public function actionAbuser($email,$interval=1,$zero=false) {
        date_default_timezone_set('UTC');
	      $criteria=new CDbCriteria;
    		$criteria->condition='(abuserIncidents.last_occurrence between NOW()-interval :interval minute and NOW() and abuserIncidents.counter>=occurrence) /*OR abuserIncidents.counter>occurrence*/';
    		$criteria->params=array(':interval'=>intval($interval));
    		$criteria->order="abuserIncidents.counter DESC";
        $AT=AbuserTrigger::model()->with('abuserIncidents')->findAll($criteria);
        if(!$AT)
            exit;
        $message=$this->render('abuser_report',array('model'=>$AT));
        $this->email_report($email,$message);
        if($zero!==false)
          foreach($AT as $a)
            foreach($a->abuserIncidents as $b)
              $b->zero(true);
      // mail latest abuser incidents
    }
    private function render($template, array $data = array()){
        $path = Yii::getPathOfAlias('application.views.email').'/'.$template.'.php';
        if(!file_exists($path)) throw new Exception('Template '.$path.' does not exist.');
        return $this->renderFile($path, $data, true);
    }

  private function email_report($email,$message)
  { 
      $data=array('message' => $message, 'name' => $email, 'email'=>$email,'description' => 'Echofish Report');
      $mail = new YiiMailer('report_attachment',$data);
      $mail->IsSMTP();
      $mail->SMTPAuth = false;
      //set properties
      $mail->setFrom(Yii::app()->params['adminEmail'], 'Echofish');
      $mail->setSubject("Echofish Report");
      $mail->setTo($email);
      if (!$mail->send()) {
        var_dump($mail->ErrorInfo);
      }
  }
}