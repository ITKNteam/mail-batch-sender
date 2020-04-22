<?php

/* @var $this yii\web\View */


use app\models\AgencySettings;
use app\models\BatchData;
use app\models\AgencyCsvBatch;

$this->title = 'My test';
?>

<?php
//$file_name = 'daaff89c.csv';
//$path = UPLOAD_DIR.'file/';
//$full_path = $path . $file_name;
//
// $group_id = 3;
// $agency_id = 2;
// 
// $batch = new AgencyCsvBatch();
//                
//    $batch->agency_id = $agency_id;
//    $batch->user_id = Yii::$app->user->id;
//    $batch->batch_date = date("Y-m-d H:i:s");
//    $batch->save();
//    $batch_id = $batch->id;
// 
// 
// 
//               
//  $RowsTemplate = (new \yii\db\Query())
//    ->select(['agency_settings.id', 'sys_parametr.name'])
//    ->from('agency_settings')
//    ->join('LEFT JOIN', 'sys_parametr', 'sys_parametr.id = agency_settings.sys_parametr_id')
//    ->where(['sys_parametr.group_id' => $group_id])
//    ->orderBy('row_order')      
//    ->all();
//
//   $fp = fopen($full_path,'r') or die("can't open file");
//    $handle = fopen('php://memory', 'w+');
//               // fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($full_path)));
//                fwrite($handle,  file_get_contents($full_path));
//                rewind($handle);
//                    
//                $i=0;
//                $row_num = 1;
//                while($csv_line = fgetcsv($handle,0, ",")) {
//                    //$data[] = $csv_line;
//                     
//                     for ($k = 0, $j = count($RowsTemplate); $k < $j; $k++) {
//                       if ($csv_line[$k]==null) {
//                           $v_val ='';    
//                        }else {
//                           $v_val = $csv_line[$k];
//                        }
//                            
//                            $data[] = ['batch_id'=>$batch_id,
//                                      'param_id'=>$RowsTemplate[$i]['id'],
//                                      'row_order'=>$row_num,
//                                      'value'=>$v_val
//                                    
//                            ]; 
//                           ++$i;        
//                            
//                       
//                        
//                    }
//                    $i=0;
//                    ++$row_num;
//                    
//                   
//                    
//                }
//                $db = Yii::$app->db;
//                $sql = $db->queryBuilder->batchInsert(BatchData::tableName(), [
//                            'batch_id',
//                            'agency_parametr_id',
//                            'string_order',
//                            'value',
//                            ], $data);
//                $db->createCommand($sql )->execute();
//                  fclose($handle);
 ?>

<pre>
    <?php
     print_r($RowsTemplate);
     print_r($data);
    ?>
</pre>
