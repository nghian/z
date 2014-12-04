<?php
/**
 * Created by PhpStorm.
 * User: TruongNhat
 * Date: 12/2/2014
 * Time: 5:08 AM
 */

namespace common\models;


use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use Yii;

class CropModel extends Model
{
    public $source;
    public $height;
    public $width;
    public $x;
    public $y;
    private $_fileName;
    private $_response;

    public function rules()
    {
        return [
            ['source', 'required'],
            [['height', 'width', 'x', 'y'], 'integer']
        ];
    }

    public function crop()
    {
        if ($this->validate()) {
            if ((new Image())->crop($this->getUploadDir() . DIRECTORY_SEPARATOR . end(explode('/', $this->source)), $this->width, $this->height, [$this->x, $this->y])->save($this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getFileName())) {
                $this->_response = ['filename'=>$this->getFileName(),'url'=>$this->getUrl()];
                return true;
            }
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
            $this->_fileName = substr(uniqid(md5(rand()), true), 0, 10) . '_' . end(explode('/', $this->source));
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