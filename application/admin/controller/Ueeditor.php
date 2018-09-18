<?php
namespace app\admin\controller;
use think\Controller;

class Ueeditor extends Controller{

    // ueditor 编辑器
    public function index()
    {
        if(request()->isPost()){
            $content = input('post.content');
            dump($content);
            die;
        }


    }

    public function config()
    {
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./static/ueditor/php/config.json")), true);
        $action = $_GET['action'];
//http://huahong.yongweisoft.cn/admin/ueeditor/config?action=config&action=uploadimage&encode=utf-8
        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = include("./static/ueditor/php/action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
                $result = include("./static/ueditor/php/action_list.php");
                break;
            /* 列出文件 */
            case 'listfile':
                $result = include("./static/ueditor/php/action_list.php");
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = include("./static/ueditor/php/action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }









}