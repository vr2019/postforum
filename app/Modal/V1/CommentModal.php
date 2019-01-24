<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class CommentModal extends Model{
    //数据库表名
    protected $table = 'comment';

    protected $primaryKey = 'CommentId';

    //数据库字段
    protected $fillable = ['ReplayCommentId', 'ForumId', 'UserId', 'Content', 'CreateTime', 'UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}