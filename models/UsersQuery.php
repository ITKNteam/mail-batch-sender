<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UsersCert]].
 *
 * @see User
 */
class UsersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Users[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Users|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    
    
//    
//    user_pic is not null
//    and sex = 0
//    and points_balance <> 0
//    and statusId = 1 
    
//    public function NotWomen($winers_id = [])
//    {
//        return $this->andWhere(['not in',  'user_id', $winers_id]);
//    }
}