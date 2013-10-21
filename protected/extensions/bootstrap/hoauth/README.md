hoauth
======

* hoauth extension provides simple integration with social network authorization lib [Hybridauth](http://hybridauth.sourceforge.net/) in Yii. (facebook, google, twitter, vkontakte and much more).
* Automatically finds and supports `yii-user` module ([instruction for yii-user](https://github.com/SleepWalker/hoauth/wiki/%5Binstall%5D-hoauth-and-yii-user-extension)).

Requirements
------------
* Yii 1.1 or above. (I have tested it only in 1.1.13)

Available social networks
-------------------------

* OpenID
* Google
* Facebook
* Twitter
* Yahoo
* MySpace
* Windows Live
* LinkedIn
* Foursquare
* Vkontakte
* AOL

Additional social networks providers can be found at HybridAuth [website](http://hybridauth.sourceforge.net/download.html). And how to configure them [here](http://hybridauth.sourceforge.net/userguide.html) at the bottom of the page.

A little about how it's woks
----------------------------

This extension authenticates and if it's need creates new user. When user was registered "locally" (so he has login (email) and password), then he can also log in with it's social account (extension checks if user with provided email exists in db, when yes, the he will be logged in and it is no matter how had he registered earlier - locally or not). After the user logged in he will be redirected to `Yii::app()->user->returnUrl`.

In future releases, when it will be needed I can implement "classical algorithm": either local authorization or social authorization.

**NOTE:** this extension requires `UserIdentity` class. It doesn't use `authenticate()` method of `UserIdentity` class. Class constructor called with parameters `new UserIdentity($mail, null)` and than called `CWebUser::login()` method (while authentication work did for us social network). When social network didn't give us user's email, the **hoauth** will ask user for email, when email exists in our db, the password will be asked too. At the end we bind provided by social network unique user identifier to user id for future sign in.

**NOTE 2:** This extension will also automatically create `user_oauth` table in your database. About it see "`UserOAuth` model" section.

Installation and Usage
----------------------

**1\.** Simply copy the files in your `extensions` directory (or in any other directory you want).

**2\.** Edit your controller source code (eg. `SiteController` class with `actionLogin()` method) to add new actions:
```php
class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
      'oauth' => array(
        // the list of additional properties of this action is below
        'class'=>'ext.hoauth.HOAuthAction',
        // Yii alias for your user's model, or simply class name, when it already on yii's import path
        // default value of this property is: User
        'model' => 'User', 
        // map model attributes to attributes of user's social profile
        // model attribute => profile attribute
        // the list of avaible attributes is below
        'attributes' => array(
          'email' => 'email',
          'fname' => 'firstName',
          'lname' => 'lastName',
          'gender' => 'genderShort',
          'birthday' => 'birthDate',
          // you can also specify additional values, 
          // that will be applied to your model (eg. account activation status)
          'acc_status' => 1,
        ),
      ),
      // this is an admin action that will help you to configure HybridAuth 
      // (you must delete this action, when you'll be ready with configuration, or 
      // specify rules for admin role. User shouldn't have access to this action!)
      'oauthadmin' => array(
        'class'=>'ext.hoauth.HOAuthAdminAction',
      ),
		);
	}
}
```

**3\.** Add the `findByEmail` method to your user`s model class:
```php
  /**
   * Returns User model by its email
   * 
   * @param string $email 
   * @access public
   * @return User
   */
  public function findByEmail($email)
  {
    return self::model()->findByAttributes(array('email' => $email));
  }
```

**4\.** Visit your `oauthadmin` action (eg. http://yoursite.com/site/oauthadmin) to create the HybridAuth config. For your `HybridAuth Endpoint URL` use this: http://yoursite.com/site/oauth. After install you can leave `install.php` in your file system, while it's in Yii protected directory. But you must **remove** `oauthadmin` action, or make such rules, that give access only for admin users. Config file can be found at `application.config.hoauth`

**5\.** Add social login widget to your login page view (you can use `route` property, when you placing your widget not in the same module/controller as your `oauth` action):
```php
<?php $this->widget('ext.hoauth.widget.HOAuth'); ?>
```

**Optional:**
**6\.** When you planning to use social networks like **Twitter**, that returns no email from user profile, you should declare `verifyPassword($password)` method in `User` model, that should take the password (not hash) and return `true` if it is valid.
**7\.** You can also declare the `sendActivationMail()` method, that should mark the user account as inactive and send the mail for activation. This method, when it's exists will be used for social networks like **Twitter**, that give us no data about user's email (because we need to proof that user entered the right email).

Available social profile fields
-------------------------------

You can find them at HybridAuth [website](http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html).
And here is some additional fields, that I needed in my project, you can use them too:
* `birthDate` - The full date of birthday (eg. 1991-09-03)
* `genderShort` - short representation of gender (eg. 'm', 'f')

Additional properties for `HOAuthAction`
----------------------------------------
* `useYiiUser` - enables support for `yii-user` (default: false). `hoauth` will find `yii-user` module automatically, so you can leave this property as default. You may also leave `attributes` and `model` properties as default.
* `enabled` - defines whether the ouath functionality is active. Useful for example for CMS, where user can enable or disable oauth functionality in control panel. (default: true)
* `scenario` - scenario name for the $model (optional)
* `loginAction` - name of a local login action (should be in the same controller as `oauth` action). (default: 'actionLogin')
* `duration` - 'remember me' duration in ms. (default: 2592000 //30days)
* `usernameAttribute` - you can specify username attribute, when it must be unique (like in `yii-user` extension), that hoauth will try to validate it's uniqueness.

`UserOAuth` model
-----------------

`UserOAuth` model used to bind social services to user's account and to store session with social network profile. If you want to use this data (user profile) later, please use `UserOAuth::getProfile()` method:
```php
$userOAuths = UserOAuth::model()->findUser(5); // find all authorizations from user with id=5
foreach($userOAuths as $userOAuth)
{
  $profile = $userOAuth->profile;
  echo "Your email is {$profile->email} and social network - {$userOAuth->provider}<br />";
}
```
or
```php
$userOAuth = UserOAuth::model()->findUser(5, "Google"); // find all authorizations from user with id=5
$profile = $userOAuth->profile;
echo "Your email is {$profile->email} and social network - {$userOAuth->provider}<br />";
```
About how to use HybridAuth object you can read [here](http://hybridauth.sourceforge.net/userguide.html).

Sources
-------

* [HybridAuth] (http://hybridauth.sourceforge.net)
* [Zocial CSS3 Buttons] (https://github.com/samcollins/css-social-buttons/)
* [Project page on Yii] (http://yiiframework.com/extension/hoauth/)
* [instruction for yii-user](https://github.com/SleepWalker/hoauth/wiki/%5Binstall%5D-hoauth-and-yii-user-extension)
