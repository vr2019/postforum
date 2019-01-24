<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\CommentModal;
use App\Modal\V1\ForumModal;

use Storage;
use App\Transformers\V1\CommentTransformer;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CommentController extends BaseController{
    
    use Helpers;

    private $userId = '';

    public function AddComment(Request $request){
        $this->userId = $this->checkAuthUser($request);
        
        $v = $this->validate($request, [
            'forumid'=>'required|integer',
            'content'=>'required|string',
            'replaycommentid'=>'integer'
        ]);
        $v['replaycommentid'] = isset($v['replaycommentid']) ? $v['replaycommentid'] : -1;

        $this->checkForumIsExist($v['forumid']);
        if($v['replaycommentid'] != -1){
            $this->checkCommentIsExist($v['replaycommentid']);
        }

        $arr = array(
            'ReplayCommentId'=>$v['replaycommentid'],
            'ForumId'=>$v['forumid'],
            'UserId'=>$this->userId,
            'Content'=>$v['content'],
            'CreateTime'=>date('Y-m-d H:i:s'),
            'UpdateTime'=>date('Y-m-d H:i:s')
        );
        $ret = CommentModal::create($arr);

        
        $token = $this->getToken($request);
        $userurl = env('USER_URL');
        $userclient = new CLient(['base_uri' => $userurl]);
        $headers = ['Authorization'=>$token];
        $cuser = $this->getUserInfor($userclient, $headers, $ret->UserId);
        $tuser = null;
        if($ret->ReplayCommentId != -1){
            $com = CommentModal::find($ret->ReplayCommentId);
            $tuser = $this->getUserInfor($userclient, $headers, $com->UserId);
        }

        $ret->cuser = $cuser;
        $ret->tuser = $tuser;

        return $this->response->item($ret, new CommentTransformer);
    }

    public function DeleteComment(Request $request, $commentid){
        $this->userId = $this->checkAuthUser($request);
        
        $comment = $this->checkCommentIsMine($commentid, $this->userId);
        if($comment){
            $comment->delete();
            //删除回复的评论
            CommentModal::where('ReplayCommentId', $commentid)->delete();
        }

        return $this->response->noContent();
    }

    //////////////////////////////////////////////////////////////////////////////////
    private function checkForumIsExist($forumid){
        $obj = ForumModal::find($forumid);
        if(!$obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('帖子不存在');
        }
        return $obj;
    }
    private function checkCommentIsExist($commentId){
        $obj = CommentModal::find($commentId);
        if(!$obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('评论不存在');
        }
        return $obj;
    }
    private function checkCommentIsMine($commentid, $userId){
        $comment = $this->checkCommentIsExist($commentid);
        if($comment->UserId == $userId){
            return $comment;
        }
        return false;
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        if(!$userid){
            throw new UnauthorizedHttpException('no auth');
        }
        return $userid;
    }
    private function getToken($request){
        $token = $request->input('usertoken');
        if(!$token){
            throw new UnauthorizedHttpException('no auth');
        }
        return $token;
    }
    private function getUserInfor($client, $headers, $userid){
        $user = null;
        try{
            $response = $client->request('GET', 'manager/user/'.$userid, ['headers'=>$headers]);
            $code = $response->getStatusCode();
            if($code == 200){
                $content = $response->getBody()->getContents();
                $user = json_decode($content);
            }
        }catch(RequestException $e){
            $user = null;
        }
        return $user;
    }

}