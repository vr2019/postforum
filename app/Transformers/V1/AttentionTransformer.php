<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\AttentionModal;

class AttentionTransformer extends TransformerAbstract
{
    public function transform(AttentionModal $obj) {
        return [
            'attentionid'=>$obj['AttentionId'],
            'forumid'=>$obj['ForumId'],
            'userid'=>$obj['UserId'],
            'createtime'=>$obj['CreateTime'],
            'updatetime'=>$obj['UpdateTime']
        ];
    }
}