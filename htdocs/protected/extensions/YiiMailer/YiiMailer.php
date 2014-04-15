<?php
/**
 * YiiMailer class - wrapper for PHPMailer
 * Yii extension for sending emails using views and layouts
 * https://github.com/vernes/YiiMailer
 * Copyright (c) 2013 YiiMailer
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @package YiiMailer
 * @author Vernes Šiljegović
 * @copyright  Copyright (c) 2013 YiiMailer
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version 1.5, 2013-06-03
 */



/**
 * Include the the PHPMailer class.
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'PHPMailer'.DIRECTORY_SEPARATOR.'class.phpmailer.php');


class YiiMailer extends PHPMailer {
	
	/**
	 * Sets the CharSet of the message.
	 * @var string
	 */
	public $CharSet='UTF-8';
	
	/**
	 * Sets the text-only body of the message.
	 * @var string
	 */
	public $AltBody='';
	
	/**
	 * Default paths and private properties
	 */
	private $viewPath='application.views.mail';
	
	private $layoutPath='application.views.mail.layouts';
	
	private $baseDirPath='webroot.images.mail';

	private $testMode=false;

	private $savePath='webroot.assets.mail';
	
	private $layout;
	
	private $view;
	
	private $data;
	
	/**
	 * Constants
	 */
	const CONFIG_FILE='mail.php'; //Define the name of the config file

	const CONFIG_PARAMS='YiiMailer'; //Define the key of the Yii params for the config array
	
	/**
	 * Set and configure initial parameters
	 * @param string $view View name
	 * @param array $data Data array
	 * @param string $layout Layout name
	 */
	public function __construct($view='', $data=array(), $layout='')
	{
		//initialize config
		if(isset(Yii::app()->params[self::CONFIG_PARAMS]))
			$config=Yii::app()->params[self::CONFIG_PARAMS];
		else
			$config=require(Yii::getPathOfAlias('application.config').DIRECTORY_SEPARATOR.self::CONFIG_FILE);
		//set config
		$this->setConfig($config);
		//set view
		$this->setView($view);
		//set data
		$this->setData($data);
		//set layout
		$this->setLayout($layout);
	}
	
	/**
	 * Configure parameters
	 * @param array $config Config parameters
	 * @throws CException 
	 */
	private function setConfig($config)
	{
		if(!is_array($config))
			throw new CException("Configuration options must be an array!");
		foreach($config as $key=>$val)
		{
			$this->$key=$val;
		}
	}
	
	/**
	 * Set the view to be used
	 * @param string $view View file
	 * @throws CException 
	 */
	public function setView($view)
	{
		if($view!='')
		{
			if(!is_file($this->getViewFile($this->viewPath.'.'.$view)))
				throw new CException('View "'.$view.'" not found');
			$this->view=$view;
		}
	}
	
	/**
	 * Get currently used view
	 * @return string View filename
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * Clear currently used view
	 */
	public function clearView()
	{
		$this->view=null;
	}

	/**
	 * Send data to be used in mail body
	 * @param array $data Data array
	 */
	public function setData($data)
	{
		$this->data=$data;
	}
	
	/**
	 * Get current data array
	 * @return array Data array
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Clear current data array
	 */
	public function clearData()
	{
		$this->data=array();
	}
	
	/**
	 * Set layout file to be used
	 * @param string $layout Layout filename
	 * @throws CException 
	 */
	public function setLayout($layout)
	{
		if($layout!='')
		{
			if(!is_file($this->getViewFile($this->layoutPath.'.'.$layout)))
				throw new CException('Layout "'.$layout.'" not found!');
			$this->layout=$layout;
		}
	}
	
	/**
	 * Get current layout
	 * @return string Layout filename
	 */
	public function getLayout()
	{
		return $this->layout;
	}
	
	/**
	 * Clear current layout
	 */
	public function clearLayout()
	{
		$this->layout=null;
	}

	/**
	 * Set path for email views
	 * @param string $path Yii path
	 * @throws CException 
	 */
	public function setViewPath($path)
	{
		if (!is_string($path) && !preg_match("/[a-z0-9\.]/i",$path))
			throw new CException('Path "'.$path.'" not valid!');
		$this->viewPath=$path;
	}
	
	/**
	 * Get path for email views
	 * @return string Yii path 
	 */
	public function getViewPath()
	{
		return $this->viewPath;
	}
	
	/**
	 * Set path for email layouts
	 * @param string $path Yii path
	 * @throws CException 
	 */
	public function setLayoutPath($path)
	{
		if (!is_string($path) && !preg_match("/[a-z0-9\.]/i",$path))
			throw new CException('Path "'.$path.'" not valid!');
		$this->layoutPath=$path;
	}
	
	/**
	 * Get path for email layouts
	 * @return string Yii path 
	 */
	public function getLayoutPath()
	{
		return $this->layoutPath;
	}
	
	/**
	 * Set path for images to embed in email messages
	 * @param string $path Yii path
	 * @throws CException 
	 */
	public function setBaseDirPath($path)
	{
		if (!is_string($path) && !preg_match("/[a-z0-9\.]/i",$path))
			throw new CException('Path "'.$path.'" not valid!');
		$this->baseDirPath=$path;
	}
	
	/**
	 * Get path for email images
	 * @return string Yii path 
	 */
	public function getBaseDirPath()
	{
		return $this->baseDirPath;
	}
	
	/**
	 * Set From address and name
	 * @param string $address Email address of the sender
	 * @param string $name Name of the sender
	 * @param boolean $auto Also set the Reply-To
	 * @return boolean True on success, false if address not valid
	 */
	public function setFrom($address, $name = '', $auto = true)
	{
		return parent::SetFrom($address, $name, (int)$auto);
	}

	 public function getParent()
	 {
	   return parent;
	 }
	/**
	 * Set one or more email addresses to send to
	 * Valid arguments: 
	 * $mail->setTo('john@example.com');
	 * $mail->setTo(array('john@example.com','jane@example.com'));
	 * $mail->setTo(array('john@example.com'=>'John Doe','jane@example.com'));
	 * @param mixed $addresses Email address or array of email addresses
	 * @return boolean True on success, false if addresses not valid
	 */
	public function setTo($addresses)
	{
		$this->ClearAddresses();
		return $this->setAddresses('to',$addresses);
	}

	/**
	 * Set one or more CC email addresses
	 * @param mixed $addresses Email address or array of email addresses
	 * @return boolean True on success, false if addresses not valid
	 */
	public function setCc($addresses)
	{
		$this->ClearCCs();
		return $this->setAddresses('cc',$addresses);
	}

	/**
	 * Set one or more BCC email addresses
	 * @param mixed $addresses Email address or array of email addresses
	 * @return boolean True on success, false if addresses not valid
	 */
	public function setBcc($addresses)
	{
		$this->ClearBCCs();
		return $this->setAddresses('bcc',$addresses);
	}

	/**
	 * Set one or more Reply-To email addresses
	 * @param mixed $addresses Email address or array of email addresses
	 * @return boolean True on success, false if addresses not valid
	 */
	public function setReplyTo($addresses)
	{
		$this->ClearReplyTos();
		return $this->setAddresses('Reply-To',$addresses);
	}

	/**
	 * Set one or more email addresses of different kinds
	 * @param string $type Type of the recipient (to, cc, bcc or Reply-To)
	 * @param mixed $addresses Email address or array of email addresses
	 * @return boolean True on success, false if addresses not valid
	 */
	private function setAddresses($type,$addresses)
	{
		if(!is_array($addresses))
		{
			$addresses=(array)$addresses;
		}

		$result=true;
		foreach ($addresses as $key => $value) {
			if(is_int($key))
				$r=$this->AddAnAddress($type,$value);
			else
				$r=$this->AddAnAddress($type,$key,$value);
			if($result && !$r)
				$result=false;
		}

		return $result;
	}

	/**
	 * Set subject of the email
	 * @param string $subject Subject of the email
	 */
	public function setSubject($subject)
	{
		$this->Subject=$subject;
	}

	/**
	 * Set text body of the email
	 * @param string $body Textual body of the email
	 */
	public function setBody($body)
	{
		$this->Body=$body;
	}

	/**
	 * Set one or more email attachments
	 * Valid arguments: 
	 * $mail->setAttachment('something.pdf');
	 * $mail->setAttachment(array('something.pdf','something_else.pdf','another.doc'));
	 * $mail->setAttachment(array('something.pdf'=>'Some file','something_else.pdf'=>'Another file'));
	 * @param mixed $attachments Path to the file or array of files to attach
	 * @return boolean True on success, false if addresses not valid
	 */
	public function setAttachment($attachments)
	{
		if(!is_array($attachments))
			$attachments=(array)$attachments;

		$result=true;
		foreach ($attachments as $key => $value) {
			if(is_int($key))
				$r=$this->AddAttachment($value);
			else
				$r=$this->AddAttachment($key,$value);
			if($result && !$r)
				$result=false;
		}

		return $result;
	}

	/**
	 * Clear all recipients and attachments
	 */
	public function clear()
	{
		$this->ClearAllRecipients();
		$this->ClearReplyTos();
		$this->ClearAttachments();
	}

	/**
	 * Get current error message
	 * @return string Error message
	 */
	public function getError()
	{
		return $this->ErrorInfo;
	}
	
	/**
	 * Find the view file for the given view name
	 * @param string $viewName Name of the view
	 * @return string The file path or false if the file does not exist
	 */
	public function getViewFile($viewName)
	{
		//In web application, use existing method
		if(isset(Yii::app()->controller))
			return Yii::app()->controller->getViewFile($viewName);
		//resolve the view file
		//TODO: support for themes in console applications
		if(empty($viewName))
			return false;
		
		$viewFile=Yii::getPathOfAlias($viewName);
		if(is_file($viewFile.'.php'))
			return Yii::app()->findLocalizedFile($viewFile.'.php');
		else
			return false;
	}
	
	/**
	 * Render the view file
	 * @param string $viewName Name of the view
	 * @param array $viewData Data for extraction
	 * @return string The rendered result
	 * @throws CException
	 */
	public function renderView($viewName,$viewData=null)
	{
		//resolve the file name
		if(($viewFile=$this->getViewFile($viewName))!==false)
		{
			//use controller instance if available or create dummy controller for console applications
			if(isset(Yii::app()->controller))
				$controller=Yii::app()->controller;
			else
				$controller=new CController(__CLASS__);

			//render and return the result
			return $controller->renderInternal($viewFile,$viewData,true);
		}
		else
		{
			//file name does not exist
			throw new CException('View "'.$viewName.'" does not exist!');
		}
		
	}

	/**
	 * Generates HTML email, with or without layout
	 */
	public function render()
	{
		//render view as body if specified
		if(isset($this->view))
			$this->setBody($this->renderView($this->viewPath.'.'.$this->view, $this->data));
		
		//render with layout if given
		if($this->layout)
		{
			//has layout
			$this->MsgHTMLWithLayout($this->Body, Yii::getPathOfAlias($this->baseDirPath));
		}
		else
		{
			//no layout
			$this->MsgHTML($this->Body, Yii::getPathOfAlias($this->baseDirPath));
		}
	}
	
	/**
	 * Render HTML email message with layout
	 * @param string $message Email message
	 * @param string $basedir Path for images to embed in message
	 */
	protected function MsgHTMLWithLayout($message, $basedir = '')
	{
		$this->MsgHTML($this->renderView($this->layoutPath.'.'.$this->layout, array('content'=>$message,'data'=>$this->data)), $basedir);
	}

	/**
	 * Render message and send emails
	 * @return boolean True if sent successfully, false otherwise
	 */
	public function send()
	{
		//render message
		$this->render();
		
		//send the message
		try{
			//prepare the message
			if(!$this->PreSend())
				return false;

			//in test mode, save message as a file
			if($this->testMode)
				return $this->save();
			else
				return $this->PostSend();
		} catch (phpmailerException $e) {
			$this->mailHeader = '';
			$this->SetError($e->getMessage());
			if ($this->exceptions) {
				throw $e;
			}
			return false;
		}
	}

	/**
	 * Save message as eml file
	 * @return boolean True if saved successfully, false otherwise
	 */
	public function save()
	{
		$filename = date('YmdHis') . '_' . uniqid() . '.eml';
		$dir = Yii::getPathOfAlias($this->savePath);
		//check if dir exists and is writable
		if(!is_writable($dir))
			throw new CException('Directory "'.$dir.'" does not exist or is not writable!');

		try {
			$file = fopen($dir . DIRECTORY_SEPARATOR . $filename,'w+');
			fwrite($file, $this->GetSentMIMEMessage());
			fclose($file);

			return true;
		} catch(Exception $e) {
			$this->SetError($e->getMessage());

			return false;
		}
	}

}