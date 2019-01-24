<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\CommentModal;

class CommentTransformer extends TransformerAbstract
{
    public function transform(CommentModal $obj) {
        return $obj->attributesToArray();
    }
}