<?php
//

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;


use yii\widgets\ListView;



/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Справочники');

$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Системные настройки'), 'url' => ['/sys']];
$this->params['breadcrumbs'][] = $this->title;




?>
<div class="users-index">

<h1><?= $this->title?></h1>


<div class="row">
    <div class="col-lg-3">
        
        <div class="list-group">
        <?php 
        echo ListView::widget([
        'dataProvider' => $HndbNameProvider,
        'itemView' => '_hndb_name',
    ]);
        ?>
    </div>
        </div>
     <div class="col-lg-7">
         
       <?php if($handbook_name_id){?>  
        <p>
            <?= Html::a(Yii::t('app', 'Create'), ['/sys/create-handbook?handbook_name_id='.$handbook_name_id], ['class' => 'btn btn-success']) ?>
        </p>

       <?php }
       
    
        
       echo GridView::widget([
           
        'dataProvider' => $HndbProvider,
        'columns' => [
   

            'id',
              [
                   'attribute'=>'value',
                   'format' => 'raw',
                    'value'=>function ($data) {
                     return Html::a($data->value, ['/sys/update?m=handbook&id='.$data->id]);
                 },
             ],
                         
             
         
                          
             

            ['class' => 'yii\grid\ActionColumn',
                
                 'buttons'=>[
                  'delete'=>function ($url, $model) {
                        return   Html::a( '<span class="glyphicon  glyphicon-trash"></span>', '/sys/delete?m=conf&id='.$model->id,
                                                ['title' => Yii::t('yii', 'Удалить'), 'data-pjax' => '0']);
               }
            ],
           'template'=>'{delete}',
                ],
        ],
    ]); ?>
        
    </div>
</div>   

</div>
