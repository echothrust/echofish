<?php

/**
 * This is the model class for table "user_oauth".
 *
 * The followings are the available columns in table 'user_oauth':
 * @property integer $user_id
 * @property string $provider name of provider
 * @property string $identifier unique user authentication id that was returned by provider
 * @property string $profile_cache
 * @property string $session_data session data with user profile
 *
 *
 * @version 1.2.2
 * @copyright Copyright &copy; 2013 Sviatoslav Danylenko
 * @author Sviatoslav Danylenko <dev@udf.su> 
 * @license PGPLv3 ({@link http://www.gnu.org/licenses/gpl-3.0.html})
 * @link https://github.com/SleepWalker/hoauth
 */
class UserOAuth extends CActiveRecord
{
  /**
   * @var $_hybridauth HybridAuth class instance
   */
  protected $_hybridauth;

  /**
   * @var $_adapter HybridAuth adapter  
   */
  protected $_adapter;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserOAuth the static model class
	 */
	public static function model($className=__CLASS__)
  {
    try
    {
      $model = parent::model($className);

      // db updates 'on the fly'
      $model->updateDb($model);

      return $model;
    }
    catch(CDbException $e)
    {
      self::createDbTable();
    }
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
    if(!empty(Yii::app()->db->tablePrefix))
      return '{{user_oauth}}';
    else
      return 'user_oauth';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
		);
  }

  public function afterFind()
  {
    parent::afterFind();

    if(!empty($this->profile_cache))
      $this->profile_cache = (object)unserialize($this->profile_cache);
  }

  public function beforeSave() 
  {
    if(!empty($this->profile_cache))
      $this->profile_cache = serialize((array)$this->profile_cache);

    return parent::beforeSave();
  }

  /**
   * @static
   * @access public
   * @return configuration array of HybridAuth lib
   */
  public static function getConfig()
  {
    $config = self::getConfigPath();

    if(!file_exists($config))
    {
      $oldConfig = dirname(__FILE__) . '/../hybridauth' . '/config.php';

      if(file_exists($oldConfig))
      {
        // TODO: delete this in next versions
        if (is_writable($yiipath) && is_writable($oldConfig)) // trying to move old config to the new dir
          rename($oldConfig, $config);
        else
          $config = $oldConfig;
      }
      else
        throw new CException("The config.php file doesn't exists");
    }

    return require($config);
  }

  /**
   * @return path to the HybridAuth config file
   */
  public static function getConfigPath()
  {
    $config = Yii::app()->params['hoauth']['configAlias'];
    if(empty($config))
    {
      $yiipath = Yii::getPathOfAlias('application.config.hoauth');
    }
    $config = $yiipath . '.php';

    return $config;
  }
  
  /**
   * @access public
   * @return array of UserOAuth models
   */
  public function findUser($user_id, $provider = false)
  {
    $params = array('user_id' => $user_id);
    if($provider)
    {
      $params['provider'] = $provider;
      return $this->findByAttributes($params);
    }
    else
      return $this->findAllByAttributes($params);
  }

  /**
   * @access public
   * @return Auth class. With restored users authentication session data
   * @link http://hybridauth.sourceforge.net/userguide.html
   * @link http://hybridauth.sourceforge.net/userguide/HybridAuth_Sessions.html
   */
  public function getHybridAuth()
  {
    if(!isset($this->_hybridauth))
    {
      $path = dirname(__FILE__) . '/../hybridauth';

      require_once($path.'/Hybrid/Auth.php');
      $this->_hybridauth = new Hybrid_Auth( self::getConfig() );

      if(!empty($this->session_data))
        $this->_hybridauth->restoreSessionData($this->session_data);
    }

    return $this->_hybridauth;
  }

  /**
   * @access public
   * @return Adapter for current provider or null, when we have no session data.
   * @link http://hybridauth.sourceforge.net/userguide.html
   */
  public function getAdapter()
  {
    if(!isset($this->_adapter) && isset($this->session_data) && isset($this->provider))
      $this->_adapter = $this->hybridAuth->getAdapter($this->provider);

    return $this->_adapter;
  }

  /**
   * authenticates user by specified adapter  
   * 
   * @param string $provider 
   * @access public
   * @return void
   */
  public function authenticate($provider)
  {
    if(empty($this->provider))
    {
      try
      {
        $this->_adapter = $this->hybridauth->authenticate($provider);
        $this->identifier = $this->profile->identifier;
        $this->provider = $provider;
        $oAuth = self::model()->findByPk(array('provider' => $this->provider, 'identifier' => $this->identifier));
        if($oAuth)
          $this->setAttributes($oAuth->attributes, false);
        else
          $this->isNewRecord = true;

        $this->session_data = $this->hybridauth->getSessionData();
        return $this;
      }
      catch( Exception $e )
      {
        $error = "";
        switch( $e->getCode() )
        { 
          case 6 : //$error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
          case 7 : //$error = "User not connected to the provider."; 
            $this->logout();
            return $this->authenticate($provider);
          break;
        }
      }
    }

    return null;
  }

  /**
   * Breaks HybridAuth session and logs user from sn out.
   *
   * @access public
   */
  public function logout()
  {
    $this->_adapter->logout();
    $this->unsetAttributes(); 
  }

  /**
   * @access public
   * @return Hybrid_User_Profile user social profile object
   */
  public function getProfile()
  {
    $profile = $this->adapter->getUserProfile();
    //caching profile
    $this->profile_cache = $profile;

    return $profile;
  }

  /**
   * binds local user to current provider 
   * 
   * @param mixed $user_id id of the user
   * @access public
   * @return whether the model successfully saved
   */
  public function bindTo($user_id)
  {
    $this->user_id = $user_id;
    return $this->save();
  }

  /**
   * @access public
   * @return whether this social network account bond to existing local account
   */
  public function getIsBond()
  {
    return !empty($this->user_id);
  }

  /**
   * Getter for cached profile.
   * We implement this method, because in older version of hoauth was no profile cache. So we need to fill db with caches
   * The second reason is camelCase
   */
  public function getProfileCache()
  {
    if(empty($this->profile_cache))
    {
      $this->profile_cache = $this->profile;
      $this->save();
    }

    return $this->profile_cache;
  }

  /**
   * creates table for holding provider bindings  
   */
  protected static function createDbTable()
  {
    //TODO: remove me in newer versions
    if(Yii::app()->db->getSchema()->getTable('user_oauth') !== null && !empty(Yii::app()->db->tablePrefix))
    {
      // providing table rename, to handle support of prefixed tables in v.1.2.2
      Yii::app()->db->createCommand('RENAME TABLE  `user_oauth` TO `tbl_user_oauth`')->execute();
      Yii::app()->controller->refresh();
    }

    $sql = file_get_contents(dirname(__FILE__).'/user_oauth.sql');
    $sql = strtr($sql, array('{{user_oauth}}' => Yii::app()->db->tablePrefix . 'user_oauth'));
    Yii::app()->db->createCommand($sql)->execute();
    Yii::app()->controller->refresh();
  }

  /**
   * Runs DB updates on the fly
   */
  public function updateDb($model)
  {
    $updates = array();
    // the try statement to correct my stupid column names in v1.0.1 of hoauth
    // sory about this
    try
    {
      $model->provider=$model->provider;
    }
    catch(Exception $e)
    {
      ob_start();
?>
ALTER TABLE  <?php echo '`' . $model->tableName() . '`'; ?> CHANGE  `name`  `provider` VARCHAR( 45 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE  `value`  `identifier` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
<?php
      $updates[] = ob_get_clean();
    }

    // profile caching since v.1.2.2
    try
    {
      $model->profile_cache=$model->profile_cache;
    }
    catch(Exception $e)
    {
      ob_start();
?>
  ALTER TABLE <?php echo '`' . $model->tableName() . '`'; ?> ADD  `profile_cache` TEXT NOT NULL AFTER  `identifier`
<?php
      $updates[] = ob_get_clean();
    }

    if(count($updates))
    {
      foreach($updates as $sql)
      {
        Yii::app()->db->createCommand($sql)->execute();
      }
      Yii::app()->controller->refresh();
    }
  }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
    return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
}
