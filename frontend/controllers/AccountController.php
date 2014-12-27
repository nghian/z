<?php

namespace frontend\controllers;

use common\behaviors\LayoutsBehavior;
use common\models\CropModel;
use common\models\UploadForm;
use common\models\User;
use common\models\UserEmail;
use common\models\UserLogin;
use common\models\UserOAuth;
use common\models\UserProfile;
use frontend\models\AuthSignupForm;
use frontend\models\ChangePasswordForm;
use frontend\models\LoginForm;
use frontend\models\PasswordResetForm;
use frontend\models\RequestPasswordForm;
use frontend\models\RequestVerifyForm;
use frontend\models\SignupForm;
use yii\authclient\ClientInterface;
use yii\flash\Flash;
use yii\authclient\BaseClient;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class AccountController extends Controller
{
    public $defaultAction = 'profile';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['signup', 'auth-signup', 'login', 'authorize', 'password-request', 'request-password', 'request-verify', 'activated'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            [
                "class" => LayoutsBehavior::className(),
                "layouts" => [
                    'authorize' => ['login', 'signup', 'auth-signup', 'request-password', 'request-verify'],
                    'account' => ['index', 'emails', 'settings', 'change-login', 'profile'],
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'authorize' => [
                'class' => 'yii\authclient\AuthAction',
                'clientCollection' => 'oauth',
                'clientIdGetParamName' => 'client',
                'successCallback' => [$this, 'authorizeCallback'],
            ]
        ];
    }

    public function actionLogin()
    {
        $returnUrl = Yii::$app->request->get('ref');
        if ($returnUrl) {
            Yii::$app->user->setReturnUrl($returnUrl);
        }
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        if (Yii::$app->user->logout()) {
            return $this->goHome();
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Flash::alert(Flash::ALERT_SUCCESS, 'Your account has been initialized.');
                $this->goHome();
                Yii::$app->end();
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionAuthSignup()
    {
        Yii::$app->user->setReturnUrl(Yii::$app->request->get('ref'));
        $model = new AuthSignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->session->remove('authorize');
                Flash::alert(Flash::ALERT_SUCCESS, 'Your account has been initialized.');
                $this->goBack();
                Yii::$app->end();
            }
        }
        return $this->render('signup-auth', [
            'model' => $model,
        ]);
    }

    public function actionRequestPassword()
    {
        $model = new RequestPasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->getUser()->userLogin->setResetToken();
                if ($model->getUser()->userLogin->save()) {
                    if ($model->sendEmail('password-reset', 'Password reset for ' . Yii::$app->name)) {
                        Flash::alert(Flash::ALERT_SUCCESS, 'Check your email for further instructions');
                        return $this->goHome();
                    } else {
                        Flash::alert(Flash::ALERT_DANGER, 'Sorry, we are unable to reset password');
                    }
                }

            }
        }
        return $this->render('request-password', [
            'model' => $model,
        ]);
    }

    public function actionPasswordReset($token)
    {
        if (!User::validateResetToken($token)) {
            Flash::alert(Flash::ALERT_DANGER, 'The reset token invalid');
        } else {
            $userLogin = UserLogin::findByResetToken($token);
            if (!$userLogin) {
                Flash::alert(Flash::ALERT_DANGER, 'The reset token invalid');
            }
        }
        if (!isset($userLogin) || !$userLogin) {
            $this->redirect(['/account/password-request', 'ref' => 'try']);
            Yii::$app->end();
        }
        $model = new PasswordResetForm();
        if ($model->load(Yii::$app->request->post()) && $model->reset($userLogin)) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your login password has been saved successfully.');
            $this->goHome();
            Yii::$app->end();
        }
        return $this->render('password-reset', ['model' => $model]);
    }

    public function actionRequestVerify()
    {
        $model = new RequestVerifyForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->verify()) {
                $this->goHome();
            }
        }
        return $this->render('request-verify', [
            'model' => $model,
        ]);
    }

    public function actionVerify($token)
    {
        if (!User::validateResetToken($token)) {
            Flash::alert(Flash::ALERT_DANGER, 'The reset token invalid');
        } else {
            $userEmail = UserEmail::findByResetToken($token);
            if (!$userEmail) {
                Flash::alert(Flash::ALERT_DANGER, 'The reset token invalid');
            } else {
                $userEmail->verified();
                $this->goHome();
            }
        }
        if (!isset($userEmail) || !$userEmail) {
            $this->redirect(['/account/activate-request', 'ref' => 'try']);
            Yii::$app->end();
        }
    }

    public function actionEmails()
    {
        return $this->render('emails');
    }

    public function addEmail()
    {
        $model = new UserEmail([
            'user_id' => Yii::$app->user->id
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your new email address has been added successfully.');
            $this->refresh();
        }
        return $this->renderPartial('email-add', ['model' => $model]);
    }

    public function actionEmailDelete($email)
    {
        $model = UserEmail::findOne([
            'email' => $email,
            'user_id' => Yii::$app->user->id
        ]);
        if ($model && $model->delete()) {
            Flash::alert(Flash::ALERT_SUCCESS, "Your email {$email} was deleted");
        } else {
            Flash::alert(Flash::ALERT_SUCCESS, "Unable to delete this email {$email}");
        }
        $this->redirect(['emails']);
    }

    public function actionEmailVerify($email)
    {
        $model = UserEmail::findOne([
            'email' => $email,
            'user_id' => Yii::$app->user->id
        ]);
        if (!$model) {
            Flash::alert(Flash::ALERT_WARNING, "This email {$email} not found on your account");
        } else {
            $model->verifying();
        }
        $this->redirect(['emails']);
    }

    public function actionEmailPrimary($email)
    {
        $model = UserEmail::findOne([
            'email' => $email,
            'user_id' => Yii::$app->user->id
        ]);
        if (!$model) {
            Flash::alert(Flash::ALERT_WARNING, "This email {$email} not found on your account");
        } else {
            $model->primary();
        }
        $this->redirect(['emails']);
    }

    public function actionSettings()
    {
        return $this->render('settings');
    }

    public function settingPassword()
    {
        if (is_null(Yii::$app->user->identity->userLogin)) {
            Flash::alert(Flash::ALERT_DANGER, 'Your login not exist. Please create your account login first');
            Yii::$app->controller->redirect(['/account/create-login']);
            Yii::$app->end();
        }
        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your password has been changed successfully.');
        }
        return $this->renderPartial('setting-password', [
            'model' => $model,
        ]);
    }

    public function settingUsername()
    {
        if (is_null(Yii::$app->user->identity->userLogin)) {
            Flash::alert(Flash::ALERT_DANGER, 'Your login not exist. Please create your account login first');
            Yii::$app->controller->redirect(['/account/create-login']);
            Yii::$app->end();
        }
        $model = Yii::$app->user->identity->userLogin;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Flash::alert(Flash::ALERT_SUCCESS, 'Your username has been changed successfully.');
            }
        }
        return $this->renderPartial('setting-username', [
            'model' => $model,
        ]);
    }

    public function settingDelete()
    {
        return $this->renderPartial('setting-delete');
    }

    public function actionProfile()
    {
        return $this->render('profile');
    }

    public function profilePicture()
    {
        return $this->renderPartial('profile-picture');
    }

    public function actionPictureUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new UploadForm();
        if (Yii::$app->request->isPost && $model->upload()) {
            return $model->getResponse();
        }
    }

    public function actionPictureCrop()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new CropModel();
        if ($model->load(Yii::$app->request->post()) && $model->crop()) {
            Yii::$app->user->identity->userProfile->updateAttributes(['picture' => $model->getUrl()]);
            return $model->getResponse();
        }
    }

    public function profileUpdate()
    {
        $model = Yii::$app->user->identity->userProfile;
        if ($model instanceof UserProfile) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Flash::alert(Flash::ALERT_INFO, 'Your profile has been updated.');
                }
            }
            return $this->renderPartial('profile-update', [
                'model' => $model,
            ]);
        }
    }

    public function profilePrimary()
    {
        $userProfiles = Yii::$app->user->identity->userProfiles;
        if (!is_null($userProfiles)) {
            $profiles = ArrayHelper::map($userProfiles, 'id', 'name');
        }
        $id = Yii::$app->request->post('id');
        if ($id !== null) {
            $profile = UserProfile::findOne([
                'user_id' => Yii::$app->user->id,
                'id' => (int)$id
            ]);
            if (!$profile) {
                Flash::alert(Flash::ALERT_WARNING, 'This profile not found on your account.');
            } else {
                $profile->primary();
            }
            $this->refresh();
            Yii::$app->end();
        }
        return $this->renderPartial('profile-primary', [
            'profiles' => $profiles
        ]);
    }

    /**
     * @param UserProfile $profile
     * @param array $attributes
     * @param bool $replace
     * @return bool
     */
    public function updateProfileFields(UserProfile $profile, $attributes, $replace = false)
    {
        if ($replace) {
            $profile->attributes = $attributes;
        } else {
            foreach ($attributes as $attribute => $value) {
                if ($profile->hasAttribute($attribute) && empty($profile->{$attribute})) {
                    $profile->setAttribute($attribute, $value);
                }
            }
        }
        return $profile->save() !== false;
    }

    /**
     * @param ClientInterface $client
     */
    public function authorizeCallback(ClientInterface $client)
    {
        $requiredSignUp = false;
        $userAttributes = $client->userAttributes;
        //print_r($userAttributes);
        //die();
        if (Yii::$app->user->isGuest) {
            if (is_null($userOAuth = UserOAuth::findOne(['social_id' => $userAttributes['id']]))) {
                if (ArrayHelper::keyExists('email', $userAttributes)) {
                    if (!is_null($userEmail = UserEmail::findOne(['email' => $userAttributes['email']]))) {
                        $user = $userEmail->user;
                        if (!is_null($user->userLogin)) {
                            ArrayHelper::remove($userAttributes, 'email');
                            $this->updateProfileFields($user->userProfile, $userAttributes);
                            (new UserOAuth([
                                'user_id' => $user->id,
                                'client_id' => $client->id,
                                'social_id' => $userAttributes['id'],
                                'access_token' => serialize($client->accessToken)
                            ]))->save();
                        } else {
                            $requiredSignUp = true;
                        }
                    } else {
                        $requiredSignUp = true;
                    }
                } else {
                    $requiredSignUp = true;
                }
                if ($requiredSignUp) {
                    Yii::$app->session->set('authorize', [
                        'clientId' => $client->id,
                        'attributes' => $userAttributes,
                        'accessToken' => $client->accessToken
                    ]);
                    Yii::$app->user->setReturnUrl(['/account/auth-signup', 'ref' => Yii::$app->user->getReturnUrl()]);
                }
            } else {
                $user = $userOAuth->user;
                $userOAuth->access_token = serialize($client->accessToken);
                $userOAuth->save();
                $this->updateProfileFields($user->userProfile, $userAttributes);
            }
            if (isset($user) && !is_null($user)) {
                $user->login(true);
            }
        } else {
            $user_id = Yii::$app->user->id;
            if (!is_null($userOAuth = UserOAuth::findOne(['social_id' => $userAttributes['id']]))) {
                if ($userOAuth->user_id != $user_id) {
                    Flash::alert('danger', 'This social profile exist on another account');

                } else {
                    $userOAuth->updateAttributes(['access_token' => serialize($client->accessToken)]);
                }
            } else {
                if (ArrayHelper::keyExists('email', $userAttributes)) {
                    if (!is_null($userEmail = UserEmail::findOne(['email' => $userAttributes['email']])) && $userEmail->user_id != $user_id) {
                        Flash::alert('danger', "This email {$userAttributes['email']} exist on another account");
                    } else {
                        (new UserEmail(['email' => $userAttributes['email'], 'user_id' => $user_id]))->save();
                    }
                }
                ArrayHelper::remove($userAttributes, 'email');
                $this->updateProfileFields(Yii::$app->user->identity->userProfile, $userAttributes);
                (new UserOAuth([
                    'user_id' => $user_id,
                    'client_id' => $client->id,
                    'social_id' => $userAttributes['id'],
                    'access_token' => serialize($client->accessToken)
                ]))->save();
            }
        }
    }
}
