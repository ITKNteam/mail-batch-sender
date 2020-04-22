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

 
    
    <div id="wrapper">
        
        <?php 
         if (User::getRoleName() == 'guest'){
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [

                        ['label' => 'Login', 'url' => ['/site/login']] 
                ],
            ]);
            } else {
        
            
        ?>
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/site/index">SB Admin v2.0</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                
                 
                        
                <li><a href="/site/logout"><i class="fa fa-sign-out fa-fw"></i> Выход (<?= Yii::$app->user->identity->username?>)</a>
                        </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        
                        <li>
                            <a href="/site/index"><i class="fa fa-dashboard fa-fw"></i> Главная</a>
                        </li>
                        
                        <li>
                            <a href="/users/simple-batches"><i class="fa fa-table fa-fw"></i> Загрузки</a>
                        </li>
                        <li>
                            <a href="/users/simple-all-batches?CsvBatchesSearch[campaign_id]=35"><i class="fa fa-tablet fa-fw"></i> Все загрузки</a>
                        </li>
                        
                        
                        <li>
                            <a href="/users/simple-stat?campaign_id=35"><i class="fa fa-floppy-o fa-fw"></i> Общая статистика</a>
                        </li>
                        <li>
                            <a href="/users/simple-stat-2?campaign_id=35"><i class="fa fa-building fa-fw"></i> Статистика по городам</a>
                        </li>
                        
                         <?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
                            <li class="active" ><a href="/sys/all-cron-tasks">Системные задачи</a></li>
                        <?php } ?> 
                 
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        
            <?php } ?>

        <div id="page-wrapper">
            <?= $content ?>
            
        </div>
        <!-- /#page-wrapper -->

    </div>
   

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
