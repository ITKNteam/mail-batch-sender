<?php

namespace app\components;

use Yii;

use app\models\SysConfig;

use yii\base\Component;
use yii\base\Object;

class DConfig extends Component {
    protected $_items = array();

    const  STATUS_ACTIVE = 1;
    const  STATUS_PASSIVE = 0;

    public function init() {
        $rows = SysConfig::find()->all();
        foreach($rows as $row) {
            $this->_items[$row->param_name] = $row->param_value;
        }
    }

    public function get($name) {
        if(isset($this->_items[$name])) {
            return $this->_items[$name];
        } else {
            throw new Exception('Неизвестный параметр '.$name);
        }
    }
    
     

    public static function getStatusActive(){
        return self::STATUS_ACTIVE;
    }

    public static function getStatusPassive(){
        return self::STATUS_PASSIVE;
    }
            

    public static function getStaus(){
        return [self::STATUS_ACTIVE=>'Включен', self::STATUS_PASSIVE=>'Выключен'];
    }
    
     public static function getStausName($id){
        $stauses = self::getStaus(); 
        return $stauses[$id];
    }
    

}