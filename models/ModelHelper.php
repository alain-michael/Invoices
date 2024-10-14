<?php

namespace app\models;

use Yii;

class ModelHelper
{
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $formName = (new $modelClass)->formName();
        $post = Yii::$app->request->post($formName);
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(array_map(fn($model) => $model->id, $multipleModels));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        return $models;
    }
}
