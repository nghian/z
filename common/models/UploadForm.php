<?php
/**
 * Created by PhpStorm.
 * User: TruongNhat
 * Date: 11/11/2014
 * Time: 2:07 AM
 */

namespace common\models;


use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\UploadedFile;
use Yii;

/**
 * Class UploadForm
 * @package common\models
 * @property UploadedFile|string $file
 */
class UploadForm extends Model
{
    public $file;
    public $type = 'image';
    public $ruleOptions = [];

    private $_response = [];
    private $_fileName;
    private $_definedTypes = ['file', 'image'];

    public function rules()
    {
        $rule = ['file'];
        if (!in_array($this->type, $this->_definedTypes)) {
            throw new InvalidConfigException('Invalid config property $type');
        }
        array_push($rule, $this->type);
        if ($this->type == 'image') {
            $validatorClass = 'yii\validators\ImageValidator';
        } else {
            $validatorClass = 'yii\validators\FileValidator';
        }
        if (!empty($this->ruleOptions)) {
            $validator = new $validatorClass;
            foreach ($this->ruleOptions as $property => $value) {
                if ($validator->hasProperty($property)) {
                    $rule[$property] = $value;
                }
            }
        }
        return [$rule];
    }

    public function attributeLabels()
    {
        return ['file' => 'Upload from computer'];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->file = UploadedFile::getInstance($this, 'file');
            return true;
        }
        return false;
    }

    public function upload()
    {
        if ($this->validate() && ($this->file instanceof UploadedFile)) {
            if ($this->getUploadDir()) {
                if (isset($this->ruleOptions['maxFiles']) && $this->ruleOptions['maxFiles'] > 1) {
                    foreach ($this->file as $file) {
                        $_model = new UploadForm();
                        $_model->file = $file;
                        if ($_model->file->saveAs($this->getUploadDir() . DIRECTORY_SEPARATOR . $_model->getFileName())) {
                            $this->_response[] = ['fileName' => $_model->getFileName(), 'url' => $_model->getUrl()];
                        }
                    }
                    if (!empty($this->_response)) {
                        return true;
                    }
                } else {
                    if ($this->file->saveAs($this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getFileName())) {
                        $this->_response = ['fileName' => $this->getFileName(), 'url' => $this->getUrl()];
                        return true;
                    }
                }
            }
            $this->file->reset();
        }else{
            $this->_response = ['status'=>false,'message'=>'do not validated'];
        }
        return false;
    }

    public function getOwnerFolder()
    {
        return Yii::$app->user->isGuest ? 'guest' : Yii::$app->user->id;
    }

    public function getUploadDir()
    {
        $uploadDir = Yii::getAlias(Yii::$app->params['uploadDir']) . DIRECTORY_SEPARATOR . $this->getOwnerFolder();
        if (FileHelper::createDirectory($uploadDir)) {
            return $uploadDir;
        }
        return false;
    }

    public function getFileName()
    {
        if (!$this->_fileName) {
            $this->_fileName = substr(uniqid(md5(rand()), true), 0, 10) . '_' . Inflector::slug($this->file->baseName) . '.' . $this->file->extension;
        }
        return $this->_fileName;
    }

    public function getUrl()
    {
        $url = Yii::$app->params['uploadUrl'] . '/' . $this->getOwnerFolder() . '/' . $this->getFileName();
        return Yii::$app->urlManager->createAbsoluteUrl([$url]);
    }

    public function getResponse()
    {
        return $this->_response;
    }
}