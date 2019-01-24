<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\ForumModal;

class ForumTransformer extends TransformerAbstract
{
    public function transform(ForumModal $obj) {
        return [
            'forumid'=>$obj['ForumId'],
            'userid'=>$obj['UserId'],
            'themeid'=>$obj['ThemeId'],
            'content'=>$obj['Content'],
            'imageids'=>$obj['ImageIds'],
            'createtime'=>$obj['CreateTime'],
            'updatetime'=>$obj['UpdateTime']
        ];
    }
}