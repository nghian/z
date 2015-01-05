<?php
namespace common\components;

use yii\base\Component;
use yii\helpers\Html;
use Yii;

class Gravatar extends Component
{
    public $gravatarUrl = 'http://www.gravatar.com/avatar/';
    public $gravatarUrlSecure = 'https://secure.gravatar.com/avatar/';

    /**
     * @var boolean whether to use [[gravatarUrl]] or [[gravatarUrlSecure]] as base url.
     * If not set it will be detected by current request.
     */
    public $secure;

    /**
     * @var int image size in pixel
     * @link http://en.gravatar.com/site/implement/images/#size
     */
    public $size = 200;

    /**
     * @var string the email to use
     */
    public $email;

    /**
     * Can be one of '404', 'mm', 'identicon', 'monsterid', 'wavatar', 'retro' or a url to default image.
     * @var string default image to use if no gravatar available
     * @link http://en.gravatar.com/site/implement/images/#default-image
     */
    public $defaultImage = 'identicon';

    /**
     * Can be one of 'g', 'pg', 'r', 'x'
     * @var string gravatar image rating
     * @link http://en.gravatar.com/site/implement/images/#rating
     */
    public $rating = 'x';

    /**
     * @var string can be 'png' or 'jpg'
     * @link http://en.gravatar.com/site/implement/images/#base-request
     */
    public $fileType = 'png';

    /**
     * @var array html options for the image tag
     */
    public $options = [
        'class' => "img-thumbnail"
    ];

    private $_emailHash;

    public function getImageTag()
    {
        if (!isset($this->options['alt'])) {
            $this->options['alt'] = 'Gravatar image';
        }
        return Html::img($this->getImageUrl(), $this->options);
    }

    /**
     * @return string generates the gravatar image url
     */
    public function getImageUrl()
    {
        if ($this->secure === null) {
            $this->secure = Yii::$app->request->isSecureConnection;
        }
        $url = $this->secure ? $this->gravatarUrlSecure : $this->gravatarUrl;
        $url .= $this->getEmailHash() . (($this->fileType !== null) ? '.' . $this->fileType : '');

        $params = [
            'r' => $this->rating,
            's' => $this->size,
        ];
        if ($this->defaultImage !== null) {
            $params['d'] = $this->defaultImage;
        }

        $url .= '?' . http_build_query($params);
        return $url;
    }

    /**
     * Generates email hash for gravatar url
     *
     * @link http://en.gravatar.com/site/implement/hash/
     * @return string md5 hash of the trimmed lowercase [[email]]
     * @throws \yii\base\InvalidConfigException if no email has been specified.
     */
    public function getEmailHash()
    {
        if ($this->_emailHash !== null) {
            return $this->_emailHash;
        } elseif ($this->email === null) {
            throw new InvalidConfigException('No email specified for Gravatar image Widget.');
        }
        return $this->_emailHash = md5(strtolower(trim($this->email)));
    }

    /**
     * Sets the email hash to use for gravatar url
     * @param $hash
     */
    public function setEmailHash($hash)
    {
        $this->_emailHash = $hash;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getImageUrl();
    }
}