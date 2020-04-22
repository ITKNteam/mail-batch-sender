<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны содержания писем';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать ', ['create-mail-tpl'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                 'label'=>' Агентство',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->agency['name'];
                 },
                
                 
             ], 
             [
               'label'=>' mail_subject',
                'attribute'=>'mail_subject',
               'format' => 'raw',
                 'value'=>function ($data) {return Html::a($data->mail_subject, '/unisender/update-mail-tpl?id='.$data->id,
                                              ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                          }

           ], 
                         
            'mail_subject',
            'mail_body',
            [
                 'label'=>' Группа',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->group['value'];
                 },
                
                 
             ],
            [
                 'label'=>' Кампания',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->campaign['value'];
                 },
                
                 
             ],
//            [
//                 'label'=>' Статус',
//                 'format' => 'raw',
//                  'value'=> function ($data) {
//        //$data['agency']['name'],
//                     return $data->status['value'];
//                 },
//                
//                 
//             ],
//            
//           //  'uid_create',
//               [
//                 'label'=>'Дата создания',
//                 'format' => 'raw',
//                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->dt_create)); },
//                 
//             ],  
             [
                 'label'=>'Дополнительные поля',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a('Управлять', '/unisender/mail-fields?mail_tpl_id='.$data->id,
                                                ['title' => Yii::t('yii', 'Управлять'), 'data-pjax' => '0']);
                            }
                 
             ],
             
            // 'last_uid',
            // 'dt_last',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
