<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\ThemeModal;

class ThemeTransformer extends TransformerAbstract
{
    public function transform(ThemeModal $obj) {
        return [
            'themeid'=>$obj['ThemeId'],
            'themename'=>$obj['ThemeName'],
            'createtime'=>$obj['CreateTime'],
            'updatetime'=>$obj['UpdateTime']
        ];
    }
}