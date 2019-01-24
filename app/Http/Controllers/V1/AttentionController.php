<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\AttentionModal;
use App\Modal\V1\ForumModal;

use Storage;
use App\Transformers\V1\AttentionTransformer;
use Dingo\Api\Routing\Helpers;

class AttentionController extends BaseController{
    
    use Helpers;

    private $userId = '';

    public function AddAttention(Request $request){
        $this->userId = $this->checkAuthUser($request);
        
        $v = $this->validate($request, [
            'forumid'=>'required|integer'
        ]);
        $this->checkForumIsExist($v['forumid']);
        $this->checkIsAttention($v['forumid'], $this->userId);

        $arr = array(
            'ForumId'=>$v['forumid'],
            'UserId'=>$this->userId,
            'CreateTime'=>date('Y-m-d H:i:s'),
            'UpdateTime'=>date('Y-m-d H:i:s')
        );

        $ret = AttentionModal::create($arr);
        return $this->response->item($ret, new AttentionTransformer);
    }

    public function DeleteAttention(Request $request, $attentionid){
        $this->userId = $this->checkAuthUser($request);
        
        $attention = $this->checkIsMyAttention($attentionid, $this->userId);
        if($attention){
            $attention->delete();
        }

        return $this->response->noContent();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////
    private function checkForumIsExist($forumid){
        $obj = ForumModal::find($forumid);
        if(!$obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('帖子不存在');
        }
        return $obj;
    }
    public function checkIsAttention($forumid, $userid){
        $obj = AttentionModal::where([
            ['ForumId', '=', $forumid],
            ['UserId', '=', $userid]
        ])->first();
        if($obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('您已经关注了');
        }
    }
    public function checkIsMyAttention($attentionid, $userid){
        $attention = $obj = AttentionModal::where([
            ['AttentionId', '=', $attentionid],
            ['UserId', '=', $userid]
        ])->first();
        if(!$attention){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('关注不存在');
        }
        return $attention;
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        if(!$userid){
            throw new UnauthorizedHttpException('no auth');
        }
        return $userid;
    }
}