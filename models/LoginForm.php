<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
      //      ['username', 'validateActivation'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates activation.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
//    public function validateActivation($attribute, $params)
//    {
//        if (!$this->hasErrors()) {
//            if (!User::validateActivation($this->username)) {
//                $this->addError($attribute, 'Вы ешё не активировали аккаунт.');
//            }
//           
//        }
//    }
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user && User::isLockedUser($user->username)){
                $this->addError($attribute, 'Аккаунт заблокирован.');
            }
            if (!$user || !$user->validatePassword($this->password)) {
            
                $this->addError($attribute, 'Неправильный пароль или логин.');
            }
            
                        
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            
             
          
            return Yii::$app->user->login($this->getUser(), 1200);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user =   User::findByUsername($this->username);
        }

        return $this->_user;
    }
    
    
    
}
