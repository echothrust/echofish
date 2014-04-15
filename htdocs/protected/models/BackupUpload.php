<?php
 
class BackupUpload extends CFormModel {
 
    public $backup;
 
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            //note you wont need a safe rule here
            array('backup', 'file', 'allowEmpty' => true, 'safe'=>true,'types' => 'xml'),
        );
    }
 
}