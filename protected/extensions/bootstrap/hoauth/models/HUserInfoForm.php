<?php
/**
 * HUserInfoForm used to collect username and email, when provider doesn't give it.
 * When user provides existing email, then model will ask for password and when it will be correct, 
 * then user can link curren provider to the local account.
 * 
 * @uses CFormModel
 * @version 1.2.2
 * @copyright Copyright &copy; 2013 Sviatoslav Danylenko
 * @author Sviatoslav Danylenko <dev@udf.su> 
 * @license PGPLv3 ({@link http://www.gnu.org/licenses/gpl-3.0.html})
 * @link https://github.com/SleepWalker/hoauth
 */
class HUserInfoForm extends CFormModel {
  /**
   * @var $email
   */
  public $email;

  /**
   * @var $username
   */
  public $username;

  /**
   * @var $password
   */
  public $password;

  protected $_form = false;

  /**
   * @var CActiveRecord $model the model of the User
   */
  public $model;

  /**
   * @var string $nameAtt name of the username attribute from $model
   */
  public $nameAtt;

  /**
   * @var string $emailAtt name of the username attribute from $model
   */
  public $emailAtt;

	public function rules()
	{
		return array(
			array('username', 'required', 'on' =>'username, username_pass, both, both_pass'),
			array('email', 'required', 'on' =>'email, email_pass, both, both_pass'),
      array('email', 'email', 'on' =>'email, email_pass, both, both_pass'),
      array('password', 'validatePassword', 'on' => 'email_pass, username_pass, both_pass'),
      array('password', 'unsafe', 'on' => 'email, username, both'),
		);
	}

  /**
   * Scenario is required for this model, and also we need info about model, that we will be validating
   * 
   * @access public
   */
  public function __construct($scenario, $model, $emailAtt, $nameAtt)
  {
    parent::__construct($scenario);
    $this->nameAtt = $nameAtt;
    $this->emailAtt = $emailAtt;
    $this->model = $model;
  }

	public function attributeLabels()
	{
		return array(
			'email'=>HOAuthAction::t('Email'),
			'username'=>HOAuthAction::t('Nickname'),
			'password'=>HOAuthAction::t('Password'),
		);
  }

  /**
   * Validates password, when password is correct, then sets the 
   * {@link HUserInfoForm::model} variable to new User model  
   * 
   * @access public
   * @return void
   */
  public function validatePassword($attribute,$params)
  {
    if(HOAuthAction::$useYiiUser)
    {
      $user = User::model()->notsafe()->findByAttributes(array('email'=>$this->email));
      if(Yii::app()->getModule('user')->encrypting($this->password)!==$user->password)
        $this->addError('password', HOAuthAction::t('Sorry, but password is incorrect'));
      else
        // setting up the current model, to use it later in HOAuthAction
        $this->model = $user;
    }else{
      $user = $this->model->findByEmail($this->email);
      if($this->verifyPassword($this->password))
        $this->addError('password', HOAuthAction::t('Sorry, but password is incorrect'));
      else
        // setting up the current model, to use it later in HOAuthAction
        $this->model = $user;      
    }
  }

  /**
   * Switch to the password scenario, when we dealing with passwords
   */
  public function afterConstruct()
  {
    parent::afterConstruct();
    if(isset($_POST) && !empty($_POST[__CLASS__]['password']))
      $this->scenario .= '_pass';
  }

  /**
   * @access public
   * @return CForm instance
   */
  public function getForm()
  {
    if(!$this->_form)
    {
      $this->_form = new CForm(array(
        'elements' => array(
          '<div class="form">',
          $this->header,
          'username' => array(
            'type' => 'text',
          ),
          'email' => array(
            'type' => 'text',
          ),
          'password' => array(
            'type' => 'password',
          ),
        ),
        'buttons'=>array(
          'submit'=>array(
            'type'=>'submit',
            'label'=>HOAuthAction::t('Submit'),
          ),
          '</div>',
        ),
        'activeForm'=>array(
          'id'=> strtolower(__CLASS__) . '-form',
          'enableAjaxValidation'=>false,
          'enableClientValidation'=>true,
          'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => true,
          ),
        ),
      ), $this);
    }
    return $this->_form;
  }

  /**
   * Validate shortcut for CForm class instance
   */
  public function getIsFormValid()
  {
    return $this->form->submitted('submit') && $this->form->validate();
  }

  /**
   * The main function of this class. Here we validating user input with 
   * provided {@link HUserInfoForm::model} class instance. We also trying 
   * to catch the case, when user enters email or username of existing account. 
   * In this case HUserInfoForm will be switched to `_pass` scenarios.
   * 
   * @access public
   * @return boolean true if the user input is valid for both {@link HUserInfoForm::model} and HUserInfoForm models
   */
  public function validateUser()
  {
    $user = $this->model;
    $emailAtt = $this->emailAtt;
    $nameAtt = $this->nameAtt;
    if(!$this->isFormValid)
      return false;

    $validators = array();
    if($nameAtt)
    {
      $user->$nameAtt = $this->username;
      $attributes[] = $nameAtt;
      $validators = $user->getValidators($nameAtt);
    }
    if($emailAtt)
    {
      $user->$emailAtt = $this->email;
      $attributes[] = $emailAtt;
      $validators = array_merge($validators, $user->getValidators($emailAtt));
    }

    foreach($validators as $validator)
    {
      foreach($attributes as $attribute)
      {
        // we need to determine if we have a new errors
        $errorsBefore = count($user->getErrors($attribute));
        $validator->validate($user, array($attribute));
        $errorsAfter =  count($user->getErrors($attribute));
        if(get_class($validator) == 'CUniqueValidator' && $errorsBefore < $errorsAfter)
        {
          // we ignore uniqness checks (this checks if user with specified email or username registered), 
          // because we will ask user for password, to check if this account belongs to him
          $errors = $user->getErrors($attribute);
          $ignored[] = end($errors);
        }
      }
    }

    $errors = array(
      'email' => $user->getErrors($emailAtt),
      'username' => $user->getErrors($nameAtt),
    );

    if(count($ignored))
    {
      //removing ignored errors
      foreach($ignored as $message)
      {
        foreach(array('email', 'username') as $attribute)
        {
          $index = array_search($message, $errors[$attribute]);
          if($index !== false)
          {
            if(strpos($this->scenario, '_pass') === false || empty($this->password))
              $errors[$attribute][$index] = HOAuthAction::t("This $attribute is taken by another user. If this is your account, enter password in field below or change $attribute and leave password blank.");
            else
              // when we have scenario with '_pass' and we are here, than user entered valid password, so we simply unsetting errors from uniqness check
              unset($errors[$attribute][$index]);
          }
        }
      }
      if(strpos($this->scenario, '_pass') === false)
        $this->scenario .= '_pass';
    }

    $this->addErrors($errors);

    return !$this->hasErrors();
  }

  /**
   * Transfers collected values to the {@link HUserInfoForm::model}
   * 
   * @access public
   * @return void
   */
  public function sync()
  {
    // syncing only when we have a new model
    if($this->model->isNewRecord && !$this->hasErrors() && strpos($this->scenario, '_pass') === false)
    {
      $this->model->setAttributes(array(
        $this->emailAtt => $this->email,
        $this->nameAtt => $this->username,
      ), false);

      if(HOAuthAction::$useYiiUser)
      {
        $this->model->superuser = 0;
        $this->model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
         $this->model->activkey=UserModule::encrypting(microtime().$this->model->email);

        // why not to put this code not in controller, but in the User model of `yii-user` module?
        // for now I can only copy-paste this code from controller...
        if (Yii::app()->getModule('user')->sendActivationMail) {
          $activation_url = Yii::app()->createAbsoluteUrl('/user/activation/activation',array("activkey" => $this->model->activkey, "email" => $this->model->email));
          UserModule::sendMail($this->model->email,UserModule::t("You registered on {site_name}",array('{site_name}'=>Yii::app()->name)),UserModule::t("To activate your account, please go to {activation_url}",array('{activation_url}'=>$activation_url)));
        }
      }else{
        if(!method_exists($this->model, 'sendActivationMail'))
          $this->model->sendActivationMail();
      }
    }
  }

  /**
   * Different form headers for different scenarios  
   * 
   * @access public
   * @return void
   */
  public function getHeader()
  {
    switch($this->scenario)
    {
    case 'both':
      $header = HOAuthAction::t('Please specify your nickname and email to end with registration.');
      break;
    case 'username':
      $header = HOAuthAction::t('Please specify your nickname to end with registration.');
      break;
    case 'email':
      $header = HOAuthAction::t('Please specify your email to end with registration.');
      break;
    }

    return "<p class=\"hFormHeader\">$header</p>";
  }
}
