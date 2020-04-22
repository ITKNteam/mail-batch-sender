<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\usersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление пользователями';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?php  echo $this->title;?>
        </h1>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            'id',
             
             'email:email',
            
          
               
            [
                 'label'=>'Блокировка',
                 'format' => 'raw',
                 'value'=>function ($data) { if (Yii::$app->user->id ==$data->id)
                                                    return '';
                                            $status = $data->status_id ? 0:1;
                                            return Html::a( $data->status_id ? 'Блокировать' : 'Разблокировать', '/users/update?a=lock&uid='.$data->id.'&status_id='.$status,
                                                ['title' => Yii::t('yii', 'Блокировка'), 'data-pjax' => '0']);
                            }
             ],        
           
             [
                 'label'=>'Дата регистрации',
                 'format' => 'raw',
                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->reg_date)); },
                 
             ],   
                     
             [
                 'label'=>'Профиль',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( $data->username, '/users/update?id='.$data->id,
                                                ['title' => Yii::t('yii', 'Профиль'), 'data-pjax' => '0']);
                            }
                 
             ],        
                     
             

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
