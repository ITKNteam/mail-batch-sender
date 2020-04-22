<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\AgencySettings;

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поля шаблона';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны содержания писем', 'url' => ['mail-tpl']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
     <?php
     echo DetailView::widget([
        'model' => $MailTplmodel,
        'attributes' => [
        [
                 'label'=>' Кампания',
                 'format' => 'raw',
                  'value'=> AgencySettings::getValue( $MailTplmodel->campaign_id),
                  
             ],    
        [
                 'label'=>' Группа',
                 'format' => 'raw',
                  'value'=> AgencySettings::getValue( $MailTplmodel->group_id),
                  
             ],
            

        ],
    ]); 
        ?>
    
    <p>
        <?= Html::a('Создать поле', ['create-mail-tpl-fileld?mail_tpl_id='.$MailTplmodel->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                 'label'=>'Поле',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->uField['new_field_name'];
                 },
                
                 
             ], 
                         
            'value',
        
            
           //  'uid_create',
               [
                 'label'=>'Дата создания',
                 'format' => 'raw',
                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->dt_create)); },
                 
             ],  
             [
                 'label'=>'Изменить',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a('Изменить', '/unisender/mail-fields-update?id='.$data->id,
                                                ['title' => Yii::t('yii', 'Изменить'), 'data-pjax' => '0']);
                            }
                 
             ],
             
            // 'last_uid',
            // 'dt_last',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
