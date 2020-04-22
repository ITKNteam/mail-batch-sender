<?php

/* @var $this yii\web\View */
$this->title = 'West BTL';
if (!\Yii::$app->user->isGuest)
  $this->title = 'Панель управления';
?>

<div class="row">
    &nbsp;
</div>

<div class="row">
    &nbsp;
</div>


<div class="row">
    &nbsp;
</div>


 <?php if (!\Yii::$app->user->isGuest)  {
     
    $inf =   \app\models\UnisenderCampaign::widgetLastCampaignStat();
    
    $res = $inf['res'];
    $name = $inf['name'];
    $CampaignStatus = $inf['CampaignStatus'];
    $contact_inf = \app\models\UnisenderContacts::getLastBatchInf();
    
    $contact_count = $contact_inf['new_contacts'];
    $contact_batch_id = $contact_inf['batch_id'];
    $contact_list_id = $contact_inf['list_id'];
    
    $batch_count = \app\models\AgencyCsvBatch::find()->count();
    $campaign_count = \app\models\UnisenderCampaign::find()->count();
    
     ?>
      <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-upload fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?= $batch_count?></div>
                                    <div>Всего загруженно csv файлов!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">Подробнее</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-users fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?= $contact_count?></div>
                                    <div>Контактов в последней загрузке!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">Подробнее</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-envelope fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?= $campaign_count?></div>
                                    <div><br>Проведенно рассылок!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">Подробнее</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
<!--                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-save fa-5x"></i>
                                </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">13</div>
                            <div><br>Заявки в подержку!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>-->
    </div>


    
    <div class="row">
            <div class="col-lg-6">
            <div class="panel panel-primary">
                     <div class="panel-heading">
                         Отчёт о статусах доставки последней рассылки - <?= $name?>
                     </div>
                     <div class="panel-body">
                                    <div class="row">

                    <div class="col-xs-6 text-right">
                    <?=  \dosamigos\chartjs\ChartJs::widget([
                        'type' => 'Doughnut',
                        'id'=>'ddd',

                        'options' => [
                            'defaults'=> 'defaultConfig',

                            'height' => 300,
                            'width' => 250
                        ],
                        'data' => [
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->ok_delivered,
                            'color'=>"#46BFB0",
                            'highlight'=> "#5AD3D1",
                            'label'=> "Доставленно : ".@$res['UnisenderAnswer']->result->data->ok_delivered
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->ok_read,
                            'color'=>"#F6B1BD",
                            'highlight'=> "#FAD3D1",
                            'label'=> "Прочитано : ". @$res['UnisenderAnswer']->result->data->ok_read
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->ok_unsubscribed,
                            'color'=>"#460FBD",
                            'highlight'=> "#460FB0",
                            'label'=> "Отписались : ". @$res['UnisenderAnswer']->result->data->ok_unsubscribed
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->err_user_unknown,
                            'color'=>"#F7464A",
                            'highlight'=> "#F74610",
                            'label'=> "Адрес не существует : ". @$res['UnisenderAnswer']->result->data->err_user_unknown
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->err_user_inactive,
                            'color'=>"#F7004A",
                            'highlight'=> "#5A00D1",
                            'label'=> "Адрес когда-то существовал, но сейчас отключен : ". @$res['UnisenderAnswer']->result->data->err_user_inactive
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->err_delivery_failed,
                            'color'=>"#ff22AA",
                            'highlight'=> "#5A22ff",
                            'label'=> "Доставка не удалась по иным причинам : ". @$res['UnisenderAnswer']->result->data->err_delivery_failed
                        ],
                            [
                            'value'=> @$res['UnisenderAnswer']->result->data->err_will_retry,
                            'color'=>"#BBc08D",
                            'highlight'=> "#5AD3D1",
                               'label'=> "Попытки продолжаются : ". @$res['UnisenderAnswer']->result->data->err_will_retry

                        ],





                        ]
                    ]);
                    ?>
                         </div>
                      <div class="col-xs-6">
                         <div id="js-legend_ddd" class="chart-legend"></div>
                     </div>
                     </div>
                 </div>

            </div>
            </div>
        
        <div class="col-lg-6">
            <div class="panel panel-primary">
                     <div class="panel-heading">
                         Стаус рассылки - <?= $name?>
                     </div>
                     <div class="panel-body">
                         <div class="row">
                             <div class="list-group">
                                 <span  class="list-group-item"><b>Статус рассылки :</b> <?= @$CampaignStatus['status']?></span>
                                 <span  class="list-group-item"><b>Дата и время создания рассылки :</b> <?= @$CampaignStatus['creation_time']?></span>
                                 <span  class="list-group-item"><b>Дата и время запуска рассылки  :</b> <?= @$CampaignStatus['start_time']?></span>
                             </div>
                             
                         </div>
                             
                     </div>
            </div>
            <div class="panel panel-primary">
                     <div class="panel-heading">
                          Справочные материалы
                     </div>
                     <div class="panel-body">
                         <div class="row">
                             <div class="list-group">
                                 <span  class="list-group-item"> <a target="_blank" href="https://www.dropbox.com/s/wc8zoo8e4oovori/email_send_extendet_stat.pdf?dl=0"><b><span class="glyphicon glyphicon-download-alt"></span> Скачать инструкцию по получению расширенного статуса рассылок</b></a></span>
                                 <span  class="list-group-item"> <a target="_blank" href="https://www.dropbox.com/s/bsys1lcssw1ky88/%20download-ext-stat-emails.pdf?dl=0"><b><span class="glyphicon glyphicon-download-alt"></span> Скачать инструкцию по выгрузке расширенного статуса рассылки</b></a></span>
                                 <span  class="list-group-item"><a target="_blank" href="https://www.dropbox.com/s/yrcoxw3if7piecb/download-email-stat.pdf?dl=0"><b><span class="glyphicon glyphicon-download-alt"></span> Скачать инструкцию по выгрузке результатов</b></a></span>
                                 <span  class="list-group-item"><a target="_blank" href="https://www.dropbox.com/s/g13ukh1ehri3sbx/manual-daily_mailing.pdf?dl=0"><b><span class="glyphicon glyphicon-download-alt"></span> Скачать инструкцию по повседневной загрузке файлов</b></a></span>
                             </div>
                             
                         </div>
                             
                     </div>
            </div>
            
            
            
         </div>
            
        
    </div>
    
 <?php } else {?>   
    <div class="jumbotron">
        <h1>West BTL</h1>

       
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Регистрация</h2>

                <p>Зарегистрируйтесь в системе. После активации вашего профиля, приступайте к работе.</p>

                <p><a class="btn btn-default" href="/users/registration">Зарегистрироваться  &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Вход</h2>

                <p>Ваш профиль активирован? Тогда входите и работайте.</p>

                <p><a class="btn btn-default" href="/site/login">Войти &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Востановление</h2>

                <p>Забыли пароль? Воспользуйтесь старницей востановления пароля</p>

                <p><a class="btn btn-default" href="/users/password-recovery">Восстановить &raquo;</a></p>
            </div>
        </div>

    </div>
 <?php }?>
    
    

