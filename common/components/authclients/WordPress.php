<?php
/**
 * Created by PhpStorm.
 * User: TruongNhat
 * Date: 11/16/2014
 * Time: 1:35 AM
 */

namespace common\components\authclients;


use yii\authclient\OAuth2;

class WordPress extends OAuth2{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://public-api.wordpress.com/oauth2/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://public-api.wordpress.com/oauth2/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://public-api.wordpress.com/rest/v1';


    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('me', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'wordpress';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'WordPress';
    }
} 