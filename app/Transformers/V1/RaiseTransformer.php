<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\RaiseModal;

class RaiseTransformer extends TransformerAbstract
{
    public function transform(RaiseModal $obj) {
        return [
            'raiseid'=>$obj['RaiseId'],
            'forumid'=>$obj['ForumId'],
            'userid'=>$obj['UserId'],
            'createtime'=>$obj['CreateTime'],
            'updatetime'=>$obj['UpdateTime']
        ];
    }
}