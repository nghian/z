<?php
return [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\GoogleOAuth',
            'clientId' => '327215521469-rnks8ji7e4jbgo2ijg9dcoslvgv6dtkp.apps.googleusercontent.com',
            'clientSecret' => 's5HsWi0SEVKtj6fwXzEckGYP',
            'normalizeUserAttributeMap' => [
                'email' => ['emails', '0', 'value'],
                'name' => 'displayName',
                'verified' => function ($attributes) {
                    return intval($attributes['verified']);
                },
                'picture' => function ($attributes) {
                    return isset($attributes['image']['url']) ? str_replace('sz=50', 'sz=400', $attributes['image']['url']): null;
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
                'picture' => function ($attributes) {
                    return isset($attributes['avatar_url']) ? $attributes['avatar_url'] . '&s=400' : null;
                },
                'website' => 'blog',
                'created_at' => function ($attributes) {
                    return strtotime($attributes['created_at']);
                },
                'updated_at' => function ($attributes) {
                    return strtotime($attributes['updated_at']);
                }
            ]
        ],
        'facebook' => [
            'class' => 'yii\authclient\clients\Facebook',
            'clientId' => '111068765684175',
            'clientSecret' => 'fa5685f1c9b6b5e13c8efec3eea2f658',
            'scope' => 'email public_profile user_about_me read_stream publish_actions user_birthday user_website user_hometown read_friendlists',
            'normalizeUserAttributeMap' => [
                'location' => ['hometown', 'name'],
                'locale' => function ($attributes) {
                    return isset($attributes['locale']) ? array_shift(array_values(explode('_', $attributes['locale']))) : null;
                },
                'birthday' => function ($attributes) {
                    return isset($attributes['birthday']) ? date('Y-m-d', strtotime($attributes['birthday'])) : null;
                },
                'website' => function ($attributes) {
                    return isset($attributes['website']) ? 'http://' . $attributes['website'] : '';
                },
                'picture' => function ($attributes) {
                    return 'https://graph.facebook.com/v2.2/' . $attributes['id'] . '/picture?width=400&height=400';
                }
            ]
        ],
        'twitter' => [
            'class' => 'yii\authclient\clients\Twitter',
            'consumerKey' => '8nOKPhsCQw04x5keX0fO0zT4f',
            'consumerSecret' => 'GIycUMi0wWhexQEJ13cVF58ZXU6IXdHqatQYjC8lu5Pt5Kcwd8',
            'normalizeUserAttributeMap' => [
                'bio' => 'description',
                'locale' => 'lang',
                'picture' => 'profile_image_url',
                'website' => 'url',
                'created_at' => function ($attributes) {
                    return isset($attributes['created_at']) ? strtotime($attributes['created_at']) : time();
                }
            ]
        ],
        'linkedin' => [
            'class' => 'yii\authclient\clients\LinkedIn',
            'clientId' => 'icddntdzwtqj',
            'clientSecret' => '7fKPq2J2eRmAp9Er',
            'scope' => 'r_basicprofile r_fullprofile r_emailaddress r_contactinfo',
            'normalizeUserAttributeMap' => [
                'email' => 'email-address',
                'name' => function ($attributes) {
                    return $attributes['first-name'] . ' ' . $attributes['last-name'];
                },
                'picture' => 'picture-url',
                'bio' => 'headline',
                'location' => 'main-address',
                'birthday' => function ($attributes) {
                    return $attributes['date-of-birth']['year'] . '-' . $attributes['date-of-birth']['month'] . '-' . $attributes['date-of-birth']['day'];
                }
            ]
        ],
    ]
];