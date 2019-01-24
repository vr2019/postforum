<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\ForumModal;
use App\Modal\V1\ThemeModal;
use App\Modal\V1\CommentModal;
use App\Modal\V1\RaiseModal;
use App\Modal\V1\AttentionModal;

use Storage;
use App\Transformers\V1\ForumTransformer;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ForumController extends BaseController{
    
    use Helpers;

    private $userId = '';

    public function AddForum(Request $request){
        $this->userId = $this->checkAuthUser($request);
        
        $v = $this->validate($request, [
            'themeid'=>'required',
            'content'=>'required|string',
            'imageids'=>'string'
        ]);

        $v['imageids'] = isset($v['imageids']) ? $v['imageids'] : '';

        $this->checkThemeExist($v['themeid']);
        $this->checkFileExist($v['imageids']);

        $arr = array(
            'UserId'=>$this->userId,
            'ThemeId'=>$v['themeid'],
            'Content'=>$v['content'],
            'ImageIds'=>$v['imageids'],
            'CreateTime'=>date('Y-m-d H:i:s'),
            'UpdateTime'=>date('Y-m-d H:i:s')
        );
        $ret = ForumModal::create($arr);

        return $this->response->item($ret, new ForumTransformer);
    }

    public function DeleteForum(Request $request, $forumid){
        $this->userId = $this->checkAuthUser($request);
        
        $forum = $this->checkIsMyForum($forumid, $this->userId);
        $forum->delete();
        //删除其他关联数据
        CommentModal::where('ForumId', $forumid)->delete();
        RaiseModal::where('ForumId', $forumid)->delete();
        AttentionModal::where('ForumId', $forumid)->delete();

        return $this->response->noContent();
    }

    public function GetMyReplay(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $token = $this->getToken($request);
        $files = array();
        $baseurl = env('NETSPAVE_URL');
        $userurl = env('USER_URL');

        $coms = CommentModal::where('UserId', $this->userId)->distinct('ForumId')->paginate(15);
        $arr = array();
        foreach($coms as $c){
            $arr[] = $c->ForumId;
        }
        $forums = ForumModal::whereIn('ForumId', $arr)->paginate(15);

        //get other information files
        $headers = ['Authorization'=>$token];
        $client = new Client(['base_uri' => $baseurl]);
        $userclient = new CLient(['base_uri' => $userurl]);
        foreach($forums as $f){
            $fileids = $f->ImageIds;
            $fileidArr = explode(';', $fileids);
            $arr = array();
            for($i=0;$i<count($fileidArr);$i++){
                $arr[] = $this->getFile($client, $headers, $fileidArr[$i]);
            }
            $f->files = $arr;
            //theme
            $f->theme = ThemeModal::find($f->ThemeId);
            //user
            $f->user = $this->getUserInfor($userclient, $headers, $f->UserId);
            //comments
            $f->comments = CommentModal::where('ForumId', $f->ForumId)->orderBy('CreateTime', 'asc')->get();
            foreach($f->comments as $c){
                $cuser = $this->getUserInfor($userclient, $headers, $c->UserId);
                $tuser = null;
                if($c->ReplayCommentId != -1){
                    $com = CommentModal::find($c->ReplayCommentId);
                    $tuser = $this->getUserInfor($userclient, $headers, $com->UserId);
                }

                $c->cuser = $cuser;
                $c->tuser = $tuser;
            }
            //israise
            $raisemd = RaiseModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($raisemd){
                $f->raise = $raisemd->RaiseId;
            }else{
                $f->raise = false;
            }
            //attention
            $attention = AttentionModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($attention){
                $f->attention = $attention->AttentionId;
            }else{
                $f->attention = false;
            }
        }

        return $this->response->array($forums);
    }

    public function GetMyAttention(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $token = $this->getToken($request);
        $files = array();
        $baseurl = env('NETSPAVE_URL');
        $userurl = env('USER_URL');

        $attens = AttentionModal::where('UserId', $this->userId)->paginate(15);
        $arr = array();
        foreach($attens as $at){
            $arr[] = $at->ForumId;
        }
        $forums = ForumModal::whereIn('ForumId', $arr)->paginate(15);

        //get other information files
        $headers = ['Authorization'=>$token];
        $client = new Client(['base_uri' => $baseurl]);
        $userclient = new CLient(['base_uri' => $userurl]);
        foreach($forums as $f){
            $fileids = $f->ImageIds;
            $fileidArr = explode(';', $fileids);
            $arr = array();
            for($i=0;$i<count($fileidArr);$i++){
                $arr[] = $this->getFile($client, $headers, $fileidArr[$i]);
            }
            $f->files = $arr;
            //theme
            $f->theme = ThemeModal::find($f->ThemeId);
            //user
            $f->user = $this->getUserInfor($userclient, $headers, $f->UserId);
            //comments
            $f->comments = CommentModal::where('ForumId', $f->ForumId)->orderBy('CreateTime', 'asc')->get();
            foreach($f->comments as $c){
                $cuser = $this->getUserInfor($userclient, $headers, $c->UserId);
                $tuser = null;
                if($c->ReplayCommentId != -1){
                    $com = CommentModal::find($c->ReplayCommentId);
                    $tuser = $this->getUserInfor($userclient, $headers, $com->UserId);
                }

                $c->cuser = $cuser;
                $c->tuser = $tuser;
            }
            //israise
            $raisemd = RaiseModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($raisemd){
                $f->raise = $raisemd->RaiseId;
            }else{
                $f->raise = false;
            }
            //attention
            $attention = AttentionModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($attention){
                $f->attention = $attention->AttentionId;
            }else{
                $f->attention = false;
            }
        }

        return $this->response->array($forums);
    }

    public function GetForums(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $token = $this->getToken($request);
        $files = array();
        $baseurl = env('NETSPAVE_URL');
        $userurl = env('USER_URL');

        $v = $this->validate($request, [
            'themeid'=>'string',
        ]);
        $v['themeid'] = isset($v['themeid']) ? $v['themeid'] : -1;
        if($v['themeid'] == -1 || trim($v['themeid']) == ''){
            $forums = ForumModal::paginate(15);
        }else{
            $arr = explode(',', $v['themeid']);

            if(count($arr) > 0){
                $forums = ForumModal::whereIn('themeid', $arr)->paginate(15);
            }else{
                $forums = ForumModal::paginate(15);
            }
        }

        //get other information files
        $headers = ['Authorization'=>$token];
        $client = new Client(['base_uri' => $baseurl]);
        $userclient = new CLient(['base_uri' => $userurl]);
        foreach($forums as $f){
            $fileids = $f->ImageIds;
            $fileidArr = explode(';', $fileids);
            $arr = array();
            for($i=0;$i<count($fileidArr);$i++){
                $arr[] = $this->getFile($client, $headers, $fileidArr[$i]);
            }
            $f->files = $arr;
            //theme
            $f->theme = ThemeModal::find($f->ThemeId);
            //user
            $f->user = $this->getUserInfor($userclient, $headers, $f->UserId);
            //comments
            $f->comments = CommentModal::where('ForumId', $f->ForumId)->orderBy('CreateTime', 'asc')->get();
            foreach($f->comments as $c){
                $cuser = $this->getUserInfor($userclient, $headers, $c->UserId);
                $tuser = null;
                if($c->ReplayCommentId != -1){
                    $com = CommentModal::find($c->ReplayCommentId);
                    $tuser = $this->getUserInfor($userclient, $headers, $com->UserId);
                }

                $c->cuser = $cuser;
                $c->tuser = $tuser;
            }
            //israise
            $raisemd = RaiseModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($raisemd){
                $f->raise = $raisemd->RaiseId;
            }else{
                $f->raise = false;
            }
            //attention
            $attention = AttentionModal::where([
                ['ForumId', '=', $f->ForumId],
                ['UserId', '=', $this->userId]
            ])->first();
            if($attention){
                $f->attention = $attention->AttentionId;
            }else{
                $f->attention = false;
            }
        }

        return $this->response->array($forums);
    }

    //////////////////////////////////////////////////////////////////////////////////
    private function checkThemeExist($id){
        $obj = ThemeModal::find($id);
        if(!$obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('主题不存在');
        }
        return $obj;
    }
    private function checkFileExist($fileids){
        $netspaceurl = env('NETSPAVE_URL');
        
        $arrFileIds = explode(';', $fileids);
        $ishave = true;

        //$client = new Client(['base_uri' => $netspaceurl]);
        foreach($arrFileIds as $fid){
            /* $response = $client->request('GET', 'file/'.$fid, ['headers'=>$headers]);
            $content = $response->getBody()->getContents();

            $user = json_decode($content); */
        }
        if(!$ishave){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('文件不存在');
        }
        return true;
    }
    private function checkIsMyForum($forumid, $userid){
        $forum = ForumModal::where([
            ['ForumId', '=', $forumid],
            ['UserId', '=', $userid]
        ])->first();
        if(!$forum){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('帖子不存在');
        }
        return $forum;
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
    private function getFile($client, $headers, $fileid){
        $file = null;
        try{
            if($fileid != ''){
                $response = $client->request('GET', 'file/'.$fileid, ['headers'=>$headers]);
                $code = $response->getStatusCode();
                if($code == 200){
                    $content = $response->getBody()->getContents();
                    $file = json_decode($content)->spacefile;
                }
            }
        }catch(RequestException $e){
            $file = null;
        }
        
        return $file;
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