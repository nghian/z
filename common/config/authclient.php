<?php
return [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\GoogleOAuth',
            'clientId' => '423624264928.apps.googleusercontent.com',
            'clientSecret' => 'lyPxSQHvbrrY6iUPcz9DQCwI',
            'normalizeUserAttributeMap' => [
                'email' => ['emails', '0', 'value'],
                'first_name' => ['name', 'givenName'],
                'last_name' => ['name', 'familyName'],
                'name' => 'displayName',
                'verified' => function ($response) {
                    return intval($response['verified']);
                },
                'picture' => function ($response) {
                    return str_replace('?sz=50', '', $response['image']['url']);
                },
                'locale' => 'language',
                'location' => 'currentLocation',
                'company' => ['organizations', 0, 'name'],
            ]
        ],
        'github' => [
            'class' => 'yii\authclient\clients\Github',
            'clientId' => 'f14d2b85548573f534f6',
            'clientSecret' => '717a65f0d3bd8788c677cddec9ab699cdf1f8c2d',
            'normalizeUserAttributeMap' => [
                'username' => 'login',
                'picture' => 'avatar_url',
                'website' => 'blog',
                'first_name' => function ($response) {
                    return array_shift(array_values(explode(' ', $response['name'])));
                },
                'last_name' => function ($response) {
                    return end(explode(' ', $response['name']));
                },
                'created_at' => function ($response) {
                    return strtotime($response['created_at']);
                },
                'updated_at' => function ($response) {
                    return strtotime($response['updated_at']);
                }
            ]
        ],
        'facebook' => [
            'class' => 'yii\authclient\clients\Facebook',
            'clientId' => '111068765684175',
            'clientSecret' => 'fa5685f1c9b6b5e13c8efec3eea2f658',
            'scope' => 'email public_profile user_about_me read_stream publish_actions',
            'normalizeUserAttributeMap' => [
                'locale' => function ($response) {
                    return array_shift(array_values(explode('_', $response['locale'])));
                },
                'birthday' => function ($response) {
                    return date('Y-m-d', strtotime($response['birthday']));
                },
                'website' => function ($response) {
                    return isset($response['website']) ? 'http://' . $response['website'] : '';
                },
                'picture' => function ($response) {
                    return 'https://graph.facebook.com/v2.2/' . $response['id'] . '/picture';
                }
            ]
        ],
        'twitter' => [
            'class' => 'yii\authclient\clients\Twitter',
            'consumerKey' => '8nOKPhsCQw04x5keX0fO0zT4f',
            'consumerSecret' => 'GIycUMi0wWhexQEJ13cVF58ZXU6IXdHqatQYjC8lu5Pt5Kcwd8',
            'normalizeUserAttributeMap' => [
                'username' => 'screen_name',
                'bio' => 'description',
                'locale' => 'lang',
                'picture' => 'profile_image_url',
                'website' => 'url',
                'first_name' => function ($response) {
                    return array_shift(array_values(explode(' ', $response['name'])));
                },
                'last_name' => function ($response) {
                    return end(explode(' ', $response['name']));
                },
                'verified' => function ($response) {
                    return intval($response['verified']);
                },
                'created_at' => function ($response) {
                    return strtotime($response['created_at']);
                },
            ]
        ],
        'linkedin' => [
            'class' => 'yii\authclient\clients\LinkedIn',
            'clientId' => 'icddntdzwtqj',
            'clientSecret' => '7fKPq2J2eRmAp9Er',
            'scope' => 'r_basicprofile r_fullprofile r_emailaddress r_contactinfo',
            'normalizeUserAttributeMap' => [
                'email' => 'email-address',
                'first_name' => 'first-name',
                'last_name' => 'last-name',
                'picture'=>'picture-url',
                'bio'=>'headline',
                'location'=>'main-address',
                'birthday'=>function($attributes){
                    return $attributes['date-of-birth']['year'].'-'.$attributes['date-of-birth']['month'].'-'.$attributes['date-of-birth']['day'];
                }
            ]
        ],
    ]
];