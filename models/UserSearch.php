<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\models;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use lnch\users\Finder;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
    /** @var string */
    public $username;

    /** @var string */
    public $email;

    /** @var int */
    public $creation_date;

    /** @var string */
    public $signup_ip;

    /** @var string */
    public $user_type;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'fieldsSafe' => [['username', 'email', 'signup_ip', 'creation_date', 'user_type'], 'safe'],
            'createdDefault' => ['creation_date', 'default', 'value' => null],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'        => Yii::t('user', 'Username'),
            'email'           => Yii::t('user', 'Email'),
            'creation_date'   => Yii::t('user', 'Registration time'),
            'signup_ip'       => Yii::t('user', 'Registration ip'),
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->finder->getUserQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if(!($this->load($params) && $this->validate())) 
        {
            return $dataProvider;
        }

        if($this->creation_date !== null) 
        {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'creation_date', $date, $date + 3600 * 24]);
        }
        
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['signup_ip' => $this->signup_ip])
            ->andFilterWhere(['user_type' => $this->user_type]);
        
        return $dataProvider;
    }
}