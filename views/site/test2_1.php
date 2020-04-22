<?php

/* @var $this yii\web\View */


use app\models\AgencySettings;
use app\models\UnisenderMailTplFileds;
use app\models\BatchData;
use app\models\AgencyCsvBatch;
use app\models\UnisenderList;
use app\models\CompareDataEmail;
use app\models\UnisenderSubscribe;
use app\models\CsvBatches;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use yii\data\SqlDataProvider;


$this->title = 'Загрузка контактов';
?>

<?php

?>


<?php 
      
   


       ?>


<pre>
<?php
 
$batch_id = 287;
  $data_insert = app\models\BatchSteps::testRunPrepare($batch_id);
 print_r($data_insert);
 
 

       ?>

</pre>

  <?php
 
                             
                             ?>
                             

