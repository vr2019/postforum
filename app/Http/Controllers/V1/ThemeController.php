<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\ThemeModal;

use Storage;
use App\Transformers\V1\ThemeTransformer;
use Dingo\Api\Routing\Helpers;

class ThemeController extends BaseController{

    use Helpers;

    private $userId = '';

    public function AddTheme(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $v = $this->validate($request, [
            'name'=>'required|string'
        ]);
        $this->checkNameExist($v['name']);

        $arr = array(
            'ThemeName'=> $v['name'],
            'CreateTime'=>date('Y-m-d H:i:s'),
            'UpdateTime'=>date('Y-m-d H:i:s')
        );
        $ret = ThemeModal::create($arr);

        return $this->response->item($ret, new ThemeTransformer);
    }

    public function UpdateTheme(Request $request, $themeid){
        $this->userId = $this->checkAuthUser($request);

        $v = $this->validate($request, [
            'name'=>'required|string'
        ]);
        $theme = $this->checkIdExist($themeid);
        $this->checkNameExistNotSelf($v['name'], $theme->ThemeId);

        $theme->ThemeName = $v['name'];
        $theme->save();

        return $this->response->item($theme, new ThemeTransformer);
    }

    public function GetThemes(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $themes = ThemeModal::get();
        return $this->response->array($themes);
    }


    private function checkNameExist($name){
        $isHave = ThemeModal::where('ThemeName', $name)->first();
        if($isHave){
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('主题名称已存在');
        }
    }
    private function checkIdExist($id){
        $obj = ThemeModal::find($id);
        if(!$obj){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('主题不存在');
        }
        return $obj;
    }
    private function checkNameExistNotSelf($name, $themeid){
        $isHave = ThemeModal::where([
            ['ThemeName', '=', $name],
            ['ThemeId', '!=', $themeid]
        ])->first();
        if($isHave){
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('主题名称已存在');
        }
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        if(!$userid){
            throw new UnauthorizedHttpException('no auth');
        }
        return $userid;
    }

}