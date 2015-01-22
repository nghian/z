<?php
namespace frontend\controllers;

use common\models\User;
use common\rbac\AuthorRule;
use common\rbac\UserRule;
use frontend\models\ContactForm;
use yii\flash\Flash;
use yii\web\Controller;
use Yii;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Flash::alert(Flash::ALERT_SUCCESS, 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Flash::alert(Flash::ALERT_DANGER, 'There was an error sending email.');
            }
            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionPhpinfo()
    {
        phpinfo();
    }
    public function actionTest()
    {
        $user = User::findOne(1);
        $user->role = User::ROLE_ADMIN;
        $user->save();
    }

    public function actionRbac()
    {
        //if (!Yii::$app->user->isGuest && Yii::$app->user->id == 1) {

        $auth = Yii::$app->authManager;
        $user = new UserRule();
        $auth->add($user);
        $rule = new AuthorRule();
        $auth->add($rule);
        $create = $auth->createPermission('create');
        $create->description = 'Create new own item';
        $create->ruleName = $user->name;
        $auth->add($create);
        $update = $auth->createPermission('update');
        $update->description = 'update own item';
        $update->ruleName = $rule->name;
        $auth->add($update);
        $delete = $auth->createPermission('delete');
        $delete->description = 'Delete own item';
        $delete->ruleName = $rule->name;
        $auth->add($delete);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $manager = $auth->createRole('manager');
        $manager->description = 'Manager';
        $auth->add($manager);
        $editor = $auth->createRole('editor');
        $editor->description = 'Editor';
        $auth->add($editor);
        $register = $auth->createRole('register');
        $register->description = 'Register';
        $auth->add($register);
        $banned = $auth->createRole('banned');
        $banned->description = 'Banned';
        $auth->add($banned);
        $auth->addChild($admin, $create);
        $auth->addChild($admin, $update);
        $auth->addChild($admin, $delete);
        $auth->addChild($admin, $manager);
        $auth->addChild($admin, $editor);
        $auth->addChild($admin, $register);
        $auth->addChild($admin, $banned);

        $auth->addChild($manager, $create);
        $auth->addChild($manager, $update);
        $auth->addChild($manager, $delete);
        $auth->addChild($manager, $editor);
        $auth->addChild($manager, $register);
        $auth->addChild($manager, $banned);

        $auth->addChild($editor, $create);
        $auth->addChild($editor, $update);
        $auth->addChild($editor, $delete);
        $auth->addChild($editor, $register);
        $auth->addChild($editor, $banned);

        $auth->addChild($register, $create);
        $auth->addChild($register, $update);
        $auth->addChild($register, $delete);
        //} else {
        //  echo "bla bla";
        //}
    }
}
