<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoiceItemsSearch extends Items
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'description', 'image'], 'safe'],
            [['price'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = Items::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['price' => $this->price]);

        return $dataProvider;
    }
}
