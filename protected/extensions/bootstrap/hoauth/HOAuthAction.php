<?php
/**
 * HOAuthAction - this the main class in hoauth extension.
 * 
 * @uses CAction
 * @version 1.2.2
 * @copyright Copyright &copy; 2013 Sviatoslav Danylenko
 * @author Sviatoslav Danylenko <dev@udf.su> 
 * @license PGPLv3 ({@link http://www.gnu.org/licenses/gpl-3.0.html})
 * @link https://github.com/SleepWalker/hoauth
 */

/**
 * HOAuthAction provides simple integration with social network authorization lib Hybridauth in Yii.
 *
 * HOAuthAction requires, that your user model implements findByEmail() method, that should return user model by its email.
 *
 * Avaible social networks: 
 *    OpenID, Google, Facebook, Twitter, Yahoo, MySpace, Windows Live, LinkedIn, Foursquare, AOL
 * Additional social networks can be found at: {@link http://hybridauth.sourceforge.net/download.html}
 *
 * Social Auth widget:
 *    <?php $this->widget('ext.hoauth.HOAuthWidget', array(
 *      'controllerId' => 'site', // id of controller where is your oauth action (default: site)
 *    )); ?>
 * uses a little modified Zocial CSS3 buttons: {@link https://github.com/samcollins/css-social-buttons/}
 */
class HOAuthAction extends CAction
{
  /**
   * @var boolean $enabled defines whether the ouath functionality is active. Useful for example for CMS, where user can enable or disable oauth functionality in control panel
   */
  public $enabled = true;

  /**
   * @var string $model yii alias for user model (or class name, when this model class exists in import path)
   */
  public $model = 'User';

  /**
   * @var array $attributes attributes synchronisation array (user model attribute => oauth attribute). List of avaible profile attributes you can see at {@link http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html "HybridAuth's Documentation"}.
   *
   * Additional attributes:
   *    birthDate - The full date of birthday, eg. 1991-09-03
   *    genderShort - short representation of gender, eg. 'm', 'f'
   *
   * You can also set attributes, that you need to save in model too, eg.:
   *    'attributes' => array(
   *      'is_active' => 1,
   *      'date_joined' => new CDbExpression('NOW()'),
   *    ),
   *
   * @see HOAuthAction::$avaibleAtts
   */
  public $attributes;

  /**
   * @var string $scenario scenario name for the $model (optional)
   */
  public $scenario = 'insert';

  /**
   * @var string $loginAction name of a local login action
   */
  public $loginAction = 'actionLogin';

  /**
   * @var integer $duration how long the script will remember the user
   */
  public $duration = 2592000; // 30 days

  /**
   * @var boolean $useYiiUser enables support of Yii user module
   */
  public static $useYiiUser = false;

  /**
   * @var string $usernameAttribute you can specify the username attribute, when user must fill it
   */
  public $usernameAttribute = false;

  /**
   * @var string $_emailAttribute
   */
  protected $_emailAttribute = false;

  /**
   * @var array $avaibleAtts Hybridauth attributes that support by this script (this a list of all avaible attributes in HybridAuth 2.0.11) + additional attributes (see $attributes)
   */
  protected $_avaibleAtts = array('identifier', 'profileURL', 'webSiteURL', 'photoURL', 'displayName', 'description', 'firstName', 'lastName', 'gender', 'language', 'age', 'birthDay', 'birthMonth', 'birthYear', 'email', 'emailVerified', 'phone', 'address', 'country', 'region', 'city', 'zip', 'birthDate', 'genderShort');

  /**
   * @var ALIAS the alias of extension (you can change this, when you have put this extension in another dir)
   */
  const ALIAS = 'ext.hoauth';

  public function run()
  {
    $path = dirname(__FILE__);
    // checking if we have `yii-user` module (I think that `UWrelBelongsTo` is unique class name from `yii-user`)
    if($this->useYiiUser || file_exists(Yii::getPathOfAlias('application.modules.user.components') . '/UWrelBelongsTo.php'))
    {
      $this->useYiiUser = true;
      // settung up yii-user's user model
      Yii::import('application.modules.user.models.*');
      Yii::import(self::ALIAS . '.DummyUserIdentity');

      // prepering attributes array for `yii-user` module
      if(!is_array($this->attributes))
        $this->attributes = array();

      $this->attributes = CMap::mergeArray($this->attributes, array(
        'email' => 'email',
        'status' => User::STATUS_ACTIVE,
      ));

      $this->usernameAttribute = 'username';
      $this->_emailAttribute = 'email';
    }else{
      Yii::import($this->model, true);
      $this->model = substr($this->model, strrpos($this->model, '.'));

      if(!method_exists($this->model, 'findByEmail'))
        throw new Exception("Model '{$this->model}' must implement the 'findByEmail' method");

      $this->_emailAttribute = array_search('email', $this->attributes);
    }

    if(!isset($this->attributes) || !is_array($this->attributes) || !count($this->attributes))
      throw new CException('You must specify the model attributes for ' . __CLASS__);

    if(!in_array('email', $this->attributes))
      throw new CException("You forgot to bind 'email' field in " . __CLASS__ . "::attributes property.");

    // openId login
    if($this->enabled)
    {
      if(isset($_GET['provider']))
      {
        Yii::import(self::ALIAS . '.models.UserOAuth');
        Yii::import(self::ALIAS . '.models.HUserInfoForm');
        $this->oAuth($_GET['provider']);
      }else{
        require($path.'/hybridauth/index.php');
        Yii::app()->end();
      }
    }

    Yii::app()->controller->{$this->loginAction}();
  }

  /**
   * Initiates authorithation with specified $provider and then authenticates the user, when all goes fine
   * 
   * @param mixed $provider provider name for HybridAuth
   * @access protected
   * @return void
   */
  protected function oAuth( $provider )
  {
    try{
      // trying to authenticate user via social network
      $oAuth = UserOAuth::model()->authenticate( $provider );
      $userProfile = $oAuth->profile;

      // If we already have a user logged in, associate the authenticated provider with the logged-in user
      if(!Yii::app()->user->isGuest) {
        $oAuth->bindTo(Yii::app()->user->id);
      }
      else {
        if($oAuth->isBond)
        {
          // this social network account is bond to existing local account
          Yii::log("Logged in with existing link with '$provider' provider", CLogger::LEVEL_INFO, 'hoauth.'.__CLASS__);
          if($this->useYiiUser)
            $user = User::model()->findByPk($oAuth->user_id);
          else
            $user = call_user_func(array($this->model, 'model'))->findByPk($oAuth->user_id);
        }

        if(!$oAuth->isBond || !$user)
        {
          if(!empty($userProfile->emailVerified))
          {
            // checking whether we already have a user with specified email
            if($this->useYiiUser)
              $user = User::model()->findByAttributes(array('email' => $userProfile->emailVerified));
            else
              $user = call_user_func(array($this->model, 'model'))->findByEmail($userProfile->emailVerified);
          }

          if(!$user)
          {
            if($this->useYiiUser)
            {
              $profile = new Profile();
              // enabling register mode
              // old versions of yii
              $profile->regMode = true;
              // new version, when regMode is static property
              $prop = new ReflectionProperty('Profile', 'regMode');
              if($prop->isStatic())
                Profile::$regMode = true;
            }

            // registering a new user
            $user = new $this->model($this->scenario);
            $this->populateModel($user, $userProfile);

            // trying to fill email and username fields
            if(empty($userProfile->emailVerified) || $this->usernameAttribute || !$user->validate())
            {
              $scenario = empty($userProfile->emailVerified) && $this->usernameAttribute
                ? 'both' 
                : (empty($userProfile->emailVerified)
                ? 'email' : 'username');

              $form = new HUserInfoForm($scenario, $user, $this->_emailAttribute, $this->usernameAttribute);

              $form->setAttributes(array(
                'email' => $userProfile->email,
                'username' => $userProfile->displayName,
              ), false);

              if(!$form->validateUser())
              {
                $this->controller->render(self::ALIAS.'.views.form', array(
                  'form' => $form,
                ));
                Yii::app()->end();
              }

              // updating attributes in $user model (if needed)
              $form->sync();

              if($form->model !== $user)
                // user provided correct password for existing account
                // so we using the model of that account
                $user = $form->model;
            }

            // the model won't be new, if user provided email and password of existing account
            if($user->isNewRecord) 
            {
              if(!$user->save())
                throw new Exception("Error, while saving {$this->model} model:\n\n" . var_export($user->errors, true));

              if($this->useYiiUser)
              {
                $profile->user_id = $user->primaryKey;
                $profile->first_name = $userProfile->firstName;
                $profile->last_name = $userProfile->lastName;

                if(!$profile->save())
                  throw new Exception("Error, while saving " . get_class($profile) . "  model:\n\n" . var_export($user->errors, true));
              }
            }
          }
        }

        // checking if current user is not banned or anything else
        $this->yiiUserCheckAccess($user);

        // sign user in
        $identity = $this->useYiiUser
          ? new DummyUserIdentity($user->primaryKey, $user->email)
          : new UserIdentity($user->email, null);

        if(!Yii::app()->user->login($identity,$this->duration))
          throw new Exception("Can't sign in, something wrong with UserIdentity class.");

        if(!$oAuth->bindTo($user->primaryKey))
          throw new Exception("Error, while binding user to provider:\n\n" . var_export($oAuth->errors, true));
      }
    }
    catch( Exception $e ){
      if(YII_DEBUG)
      {
        $error = "";

        // Display the recived error
        switch( $e->getCode() ){ 
        case 0 : $error = "Unspecified error."; break;
        case 1 : $error = "Hybriauth configuration error."; break;
        case 2 : $error = "Provider not properly configured."; break;
        case 3 : $error = "Unknown or disabled provider."; break;
        case 4 : $error = "Missing provider application credentials."; break;
        case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
        case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
        $oAuth->logout(); 
        break;
        case 7 : $error = "User not connected to the provider."; 
        $oAuth->logout(); 
        break;
        case 8 : $error = "Provider does not support this feature.";  break;
        }

        $error .= "\n\n<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
        Yii::log($error, CLogger::LEVEL_INFO, 'hoauth.'.__CLASS__);

        echo $error;
        Yii::app()->end();
      }
    }

    $returnUrl = $this->useYiiUser ? Yii::app()->modules['user']['returnUrl'] : Yii::app()->user->returnUrl;
    Yii::app()->controller->redirect($returnUrl);
  }

  /**
   * Populates User model with data from social network profile
   * 
   * @param CActiveRecord $user users model
   * @param mixed $profile HybridAuth user profile object
   * @access protected
   */
  protected function populateModel($user, $profile)
  {
    foreach($this->attributes as $attribute => $pAtt)
    {
      if(in_array($pAtt, $this->_avaibleAtts))
      {
        switch($pAtt)
        {
        case 'genderShort':
          $gender = array('female'=>'f','male'=>'m');
          $att = $gender[$profile->gender];
          break;
        case 'birthDate':
          $att = $profile->birthYear 
            ? sprintf("%04d-%02d-%02d", $profile->birthYear, $profile->birthMonth, $profile->birthDay)
            : null;
          break;
        case 'email':
          $att = 'emailVerified';
          break;
        default:
          $att = $profile->$pAtt;
        }
        if(!empty($att))
          $user->$attribute = $att;
      }else{
        $user->$attribute = $pAtt;
      }
    }
  }

  /**
   * Checks wheter the $user can be logged in
   */
  protected function yiiUserCheckAccess($user)
  {
    if(!$this->useYiiUser)
      return false;

    if($user->status==0&&Yii::app()->getModule('user')->loginNotActiv==false)
      $error = UserIdentity::ERROR_STATUS_NOTACTIV;
    else if($user->status==-1)
      $error = UserIdentity::ERROR_STATUS_BAN;
    else 
      $error = UserIdentity::ERROR_NONE;

    if($error)
    {
      $this->controller->render(self::ALIAS.'.views.yiiUserError', array(
        'errorCode' => $error,
        'user' => $user,
      ));
      Yii::app()->end();
    }
  }

  public function getUseYiiUser()
  {
    return self::$useYiiUser;
  }

  public function setUseYiiUser($value)
  {
    self::$useYiiUser = $value;
  }

  public static function t($message,$params=array(),$source=null,$language=null)
  {
    return Yii::t('HOAuthAction.root', $message,$params,$source,$language);
  }
}
