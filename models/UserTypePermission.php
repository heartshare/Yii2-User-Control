<?php

namespace lnch\users\models;

use Yii;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use lnch\users\traits\ModuleTrait;
    
/**
 * Token Active Record model.
 *
 * @property string     group   
 * @property string     permission
 * @property integer    min_user_type
 *
 * @author Tom Lynch <tom@lnch.co.uk>
 */
class UserTypePermission extends ActiveRecord
{
    use ModuleTrait;

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%lnch_user_type_permissions}}';
    }
    
    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['group', 'permission'];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        return ArrayHelper::merge($scenarios, [

        ]);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'requiredAttributes' => [['group', 'permission', 'min_user_type'], 'required'],

            'groupString'   => ['group', 'string', 'max' => 256],

            'permissionString'  => ['permission', 'string', 'max' => 256],

            'minUserTypeInteger' => ['min_user_type', 'integer'],
        ];
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if($insert) 
        {
            
        }
        return parent::beforeSave($insert);
    }
}