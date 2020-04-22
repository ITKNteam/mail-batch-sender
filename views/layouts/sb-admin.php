<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\SBAdminAsset;
use app\models\User;



SBAdminAsset::register($this);



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
    
<style>
    .bs-callout {
    border-left: 3px solid #eee;
    margin: 20px 0;
    padding: 20px;
}

.bs-callout-warning {
    background-color: #fcf8f2;
    border-color: #f0ad4e;
}
.bs-callout-warning h4 {
    color: #f0ad4e;
}

.bs-callout-danger {
    background-color: #fdf7f7;
    border-color: #d9534f;
}
.bs-callout-danger h4 {
    color: #d9534f;
}
</style>
</head>
<body>
<?php $this->beginBody() ?>

<div id="wrap">
    
    
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
                       ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load-huge']],
                      // ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load']],
                        '<li class="divider"></li>',
                       ['label' => 'Архив загрузок', 'url' => ['/users/batches']],
                       ['label' => 'Все загрузки', 'url' => ['/users/crm-users']],
                       
                    ],
                ], 

             ['label' => 'Пользователи', 'url' => ['/users/list']] ,
             ['label' => 'Отчеты', 'url' => ['/unisender/campaign-reports/']] ,
             [ 'label' => 'Unisender',
                'items' => [
                       
                       ['label' => 'Списки ', 'url' => ['/unisender']],
                        ['label' => 'Письмо подтверждения', 'url' => ['/unisender/confirm-letter']],
                       ['label' => 'Дополнительные поля', 'url' => ['/unisender/fields']],
                       ['label' => 'Шаблоны содержания писем', 'url' => ['/unisender/mail-tpl-2']],
                       ['label' => 'E-mail для массовой рассылки', 'url' => ['/unisender/email']],
                  //     ['label' => 'Сопоставление полей', 'url' => ['/compare']],
                       ['label' => 'Запуск рассылки', 'url' => ['/unisender/campaign']],
                      
                       
                    ],
                ], 
//             [ 'label' => 'CRM',
//                'items' => [
//                       ['label' => 'Отправка данных', 'url' => ['/crm']],
//                        
//                        '<li class="divider"></li>',
//                       ['label' => 'Сводные отчеты', 'url' => ['/crm/stat']],
//                       
//                    ],
//                ],
            
             [ 'label' => 'Система',
                'items' => [
                     //   ['label' => 'Агентства', 'url' => ['/agency']] ,
                        ['label' => 'Права', 'url' => ['/admin']] ,
                        
              //          '<li class="divider"></li>',
            //           ['label' => 'Настройки', 'url' => ['/crm/stat']],
                       
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
              [ 'label' => 'Загрузка',
                'items' => [
                       //['label' => 'Загрузка CSV', 'url' => ['/users/csv-load']],
                       ['label' => 'Загрузка CSV', 'url' => ['/users/csv-load-huge']],
                        '<li class="divider"></li>',
                       ['label' => 'Архив загрузок', 'url' => ['/users/batches']],
                       
                    ],
                ], 

             ['label' => 'Отчеты', 'url' => ['/unisender/campaign-reports/']] ,
             [ 'label' => 'Unisender',
                'items' => [
                       
                       ['label' => 'Списки ', 'url' => ['/unisender']],
                        ['label' => 'Письмо подтверждения', 'url' => ['/unisender/confirm-letter']],
                       ['label' => 'Дополнительные поля', 'url' => ['/unisender/fields']],
                       ['label' => 'Шаблоны содержания писем', 'url' => ['/unisender/mail-tpl-2']],
                       ['label' => 'E-mail для массовой рассылки', 'url' => ['/unisender/email']],
                  //     ['label' => 'Сопоставление полей', 'url' => ['/compare']],
                       ['label' => 'Запуск рассылки', 'url' => ['/unisender/campaign']],
                      
                       
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
                <!-- /.sidebar-collapse -->



            <div class="container">
                <div class="row">
                <div class="col-lg-12 breadcrumb">
                    
                <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            
            
                
                <!-- /.col-lg-12 -->
            
        
        <?= $content ?>
    </div>
</div>
 
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Shavrak <?= date('Y') ?></p>

        <p class="pull-right"></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
