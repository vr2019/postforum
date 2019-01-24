<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\RaiseModal;
use App\Modal\V1\ForumModal;

use Storage;
use App\Transformers\V1\RaiseTransformer;
use Dingo\Api\Routing\Helpers;

class RaiseController extends BaseController{
    
    use Helpers;

    private $userId = '';

    public function AddRaise(Request $request){
        $this->userId = $this->checkAuthUser($request);
        
        $v = $this->validate($request, [
            'forumid'=>'required|integer'
        ]);
        $this->checkForumIsExist($v['forumid']);
        $this->checkIsRaise($v['forumid'], $this->userId);

        $arr = array(
            'ForumId'=>$v['forumid'],
            'UserId'=>$this->userId,
            'CreateTime'=>date('Y-m-d H:i:s'),
            'UpdateTime'=>date('Y-m-d H:i:s')
        );

        $ret = RaiseModal::create($arr);
        return $this->response->item($ret, new RaiseTransformer);
    }

    public function DeleteRaise(Request $request, $raiseid){
        $this->userId = $this->checkAuthUser($request);
        
        $raise = $this->checkIsMyRaise($raiseid, $this->userId);
        if($raise){
            $raise->delete();
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
    public function checkIsRaise($forumid, $userid){
        $obj = RaiseModal::where([
            ['ForumId', '=', $forumid],
            ['UserId', '=', $userid]
        ])->first();
        if($obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('您已经赞了');
        }
    }
    public function checkIsMyRaise($raiseid, $userid){
        $raise = $obj = RaiseModal::where([
            ['RaiseId', '=', $raiseid],
            ['UserId', '=', $userid]
        ])->first();
        if(!$raise){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('赞不存在');
        }
        return $raise;
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        if(!$userid){
            throw new UnauthorizedHttpException('no auth');
        }
        return $userid;
    }
}