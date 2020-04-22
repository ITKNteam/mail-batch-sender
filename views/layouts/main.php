<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use \app\models\User;

AppAsset::register($this);



?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'West BTL',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    
    
    if (User::getRoleName() == 'Administrator'){
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
              [ 'label' => 'Загрузка',
                'items' => [
                     //  ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load']],
                       ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load-huge']],
                        '<li class="divider"></li>',
                       ['label' => 'Архив загрузок', 'url' => ['/users/batches']],
                       
                    ],
                ], 

             ['label' => 'Пользователи', 'url' => ['//users/list']] ,
             ['label' => 'Отчеты', 'url' => ['/unisender/campaign-reports']] ,
             
             [ 'label' => 'Unisender',
                'items' => [
                       ['label' => 'Письмо подтверждения', 'url' => ['/unisender/confirm-letter']],
                       ['label' => 'Списки 2', 'url' => ['/unisender']],
                        
                       ['label' => 'Дополнительные поля', 'url' => ['/unisender/fields']],
                       ['label' => 'Шаблоны содержания писем', 'url' => ['/unisender/mail-tpl']],
                       ['label' => 'E-mail для массовой рассылки', 'url' => ['/unisender/email']],
                       ['label' => 'Сопоставление полей', 'url' => ['/compare']],
                       ['label' => 'Запуск рассылки', 'url' => ['/unisender/campaign']],
                       '<li class="divider"></li>',
                       ['label' => 'Отчеты рассылок', 'url' => ['/unisender/stat']],
                       
                    ],
                ], 
             [ 'label' => 'CRM',
                'items' => [
                       ['label' => 'Отправка данных', 'url' => ['/crm']],
                        
                        '<li class="divider"></li>',
                       ['label' => 'Сводные отчеты', 'url' => ['/crm/stat']],
                       
                    ],
                ], 
             [ 'label' => 'Система',
                'items' => [
                        ['label' => 'Агентства', 'url' => ['/agency']] ,
                        ['label' => 'Права', 'url' => ['/admin']] ,
                        
                        '<li class="divider"></li>',
                       ['label' => 'Настройки', 'url' => ['/crm/stat']],
                       
                    ],
                ], 
             
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ],
        ],
    ]);
        
    } 
    
    if (User::getRoleName() == 'Manager'){
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
             ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load-huge']],
             ['label' => 'Пользователи', 'url' => ['//users/list']],
             ['label' => 'Лог загрузок', 'url' => ['/site/about']] ,
           
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ],
        ],
    ]);
    }
    if (User::getRoleName() == 'guest'){
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
        
                ['label' => 'Login', 'url' => ['/site/login']] 
        ],
    ]);
    }
    
    
    
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
