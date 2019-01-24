<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function($api){

    $api->group(['middleware' => ['checkuserauth']], function($api){
        
        //主题
        $api->post('/addtheme', ['as'=>'vr.forum.addtheme', 'uses'=>'ThemeController@AddTheme']);
        $api->put('/updatetheme/{themeid}', ['as'=>'vr.forum.updatetheme', 'uses'=>'ThemeController@UpdateTheme']);
        $api->get('/themes', ['as'=>'vr.forum.gettheme', 'uses'=>'ThemeController@GetThemes']);

        //帖子
        $api->post('/forums', ['as'=>'vr.forum.addforum', 'uses'=>'ForumController@AddForum']);
        $api->delete('/forums/{forumid}', ['as'=>'vr.forum.deleteforum', 'uses'=>'ForumController@DeleteForum']);
        $api->get('/forums', ['as'=>'vr.forum.getforum', 'uses'=>'ForumController@GetForums']);
        $api->get('/forums/attention', ['as'=>'vr.forum.getattentionforum', 'uses'=>'ForumController@GetMyAttention']);
        $api->get('/forums/replay', ['as'=>'vr.forum.getreplayforum', 'uses'=>'ForumController@GetMyReplay']);

        //评论
        $api->post('/comments', ['as'=>'vr.forum.addcomment', 'uses'=>'CommentController@AddComment']);
        $api->delete('/comments/{commentid}', ['as'=>'vr.forum.deletecomment', 'uses'=>'CommentController@DeleteComment']);

        //赞
        $api->post('/raises', ['as'=>'vr.forum.addraise', 'uses'=>'RaiseController@AddRaise']);
        $api->delete('/raises/{raiseid}', ['as'=>'vr.forum.deleteraise', 'uses'=>'RaiseController@DeleteRaise']);
        
        //关注
        $api->post('/attentions', ['as'=>'vr.forum.addattention', 'uses'=>'AttentionController@AddAttention']);
        $api->delete('/attentions/{attentionid}', ['as'=>'vr.forum.deleteattention', 'uses'=>'AttentionController@DeleteAttention']);
        
    });


});