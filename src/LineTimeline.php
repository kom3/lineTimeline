<?php

namespace Line;

class LineTimeline {

    private $line_host = 'https://timeline.line.me/api/';
    public $timeline_order = 'TIME'; // RANKING
    public $post_limit = 10;
    public $user;
    public $gnb;
    public $home_id;
    public $session_id;

    private $is_true_last = false;
    private $last_response = [];

    public function __construct(){
        // To do...
    }
    /*
        @title Set Session Cookies
        @param $ids string
        @return
    */
    public function setSession($ids){
        $this->session_id = $ids;
        return $this;
    }
    /*
        @title Set Type Order Untuk Timeline
        @param $param string
        @return
    */
    public function order($param){
        if(!in_array($param, ['TIME', 'RANKING'])) throw new Exception("Invalid Order Type");
        $this->timeline_order = $param;
    }
    /*
        @return
    */
    public function userinfo(){
        $this->sessID();
        $http = $this->response($this->http('gnb/userInfo.json'));
        if($this->isOK()){
            $data = [];
            $this->user = $http['userInfo'];
            $this->gnb = $http['gnb'];
            $this->home_id = $this->gnb['homeInfo']['homeId'];
            return $this;
        } else {
            throw new Exception('Invalid sesssion!');
        }
    }
    /*
        @title Get Latest Response if One
        @param
        @return
    */
    public function get(){
        return $this->last_response;
    }
    /*
        @title Get Notification
        @param
        @return
    */
    public function notification(){
        $http = $this->response($this->http('gnb/noticenter.json'));
        if($this->isOK()){
            return $http['notifications'];
        } else {
            throw new Exception('Invalid sesssion!');
        }
    }
    /*
        @title Get Friend List
        @param $total integer
        @param $callback callable
        @return
    */
    public function friendList($total, callable $callback = NULL){
        if(!is_numeric($total) OR $total <= 0) throw new Exception("Invalid total!");
        $multiple = ($total <= 20) ? 1 : ($total - ($total % 20)) / 20;
        if($total % 20 != 0) $multiple++;
        $tmp_arr = [];
        for($i=0;$i<$multiple;$i++){
            $scrool_id = ($i * 20) + 1;
            $this->getFriend($scrool_id, function($data) use (&$tmp_arr){
                if(empty($data) OR count($data) == 0) break;
                $tmp_arr = array_merge($tmp_arr, $data);
            });
        }
        $this->last_response = array_slice($tmp_arr, 0, $total);
        return ($this->_end($this->isOK(), $callback) == true);
    }

    /*
        @title Post Status to Timeline
        @param $text string
        @param $type integer
        @param $large_text boolean
        @param $callback callable
        @return
    */
    public function postTimeline($text, $type = 1, $large_text = false, $sticker = [], callable $callback = NULL){
        $this->sessId();
        /* Sticker example
        $sticker[] = ["id"=>5,"packageId"=>1,"packageVersion"=>100];
        $sticker[] = ["id"=>1,"packageId"=>1,"packageVersion"=>100];
        */
        switch($type){
            case 1:
                $permission = 'ALL';
                break;
            case 2:
                $permission = 'FRIEND';
                break;
            case 3:
                $permission = 'NONE';
                break;
            default:
                throw new Exception("Invalid post type!");
        }
        $large_text = ($large_text == true) ? 'true' : 'false';
        $stickers = json_encode($sticker);
        $post = '{"postInfo":{"readPermission":{"type":"'.$permission.'","gids":null}},"contents":{"text":"'.$text.'","largeText":'.$large_text.',"stickers":'.$stickers.',"media":[]}}';
        $http = $this->response($this->http('post/create.json?sourceType=TIMELINE ', $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Delete Comment by ID and Comment Id
        @param $postid string
    */
    public function deleteComment($postid, $commentid, callable $callback = NULL){
        $this->sessID();
        $post = '{"postId":"'.$postid.'","actorId":"x","commentId":"'.$commentid.'"}';
        $http = $this->response($this->http('post/delete.json?sourceType=TIMELINE&homeId=' . $this->home_id, $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Add Friend
        @param $homeid string
    */
    public function addFriend($homeid, callable $callback = NULL){
        $this->sessID();
        $post = '{"mid":"' . $homeid . '"}';
        $http = $this->response($this->http('friend/add.json', $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Delete Post by ID 
        @param $postid string
    */
    public function deletePost($postid, callable $callback = NULL){
        $this->sessID();
        $post = '{"postId":"'.$postid.'"}';
        $http = $this->response($this->http('post/delete.json?sourceType=TIMELINE&homeId=' . $this->home_id, $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Get group list
        @param $limit integer
        $param $callback callable
    */
    public function groupList($limit = 100, callable $callback = NULL){
        $this->sessID();
        $http = $this->response($this->http('group/list.json?limit=' . $limit));
        $this->last_response = $http;
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Search Post by Hashtag 
        @param $hashtag string
        @param $limit integer 
        $param $callback callable
    */
    public function searchByHashtag($hashtag, $limit = 10, callable $callback = NULL){
        $this->sessID();
        $http = $this->response($this->http('hashtag/search.json?query='.$hashtag.'&postLimit='.$limit.'&commentLimit=1&likeLimit=1'));
        $this->last_response = $http;
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Comment a Post by Post ID
        @param $home_id string
        @param $post_id string
        @param $comment_text string
        @param $callback callable
        @return
    */
    public function commentPost($home_id, $post_id, $comment_text, callable $callback = NULL){
        $this->sessID();
        $post = '{"contentId":"'.$post_id.'","commentText":"'.$comment_text.'","contentsList":[],"actorId":"'.$this->home_id.'","recallInfos":[]}';
        $http = $this->response($this->http('comment/create.json?sourceType=MYHOME_END&homeId=' . $home_id, $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Like a Post by Post ID
        @param $post_id string
        @param $reaction integer
        @param $shareable boolean
        @param $callback callable
        @return
    */
    public function likePost($post_id, $reaction = 0, $shareable = false, callable $callback = NULL){
        $this->sessID();
        // 1 love 2 haha 3 ok 4 terharu 5 kaget 6 cry 0 random
        if(!in_array($reaction, [0,1,2,3,4,5,6])) throw new Exception('Invalid reaction id!');
        $shareable = ($shareable == true) ? 'true' : 'false';
        if($reaction == 0) $reaction = mt_rand(1, 6);
        $post = '{"contentId":"'.$post_id.'","actorId":"x","likeType":"100'.$reaction.'","sharable":'.$shareable.'}';
        $http = $this->response($this->http('like/create.json?sourceType=TIMELINE&homeId=x', $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Cancel Like a Post by Post ID
        @param $post_id string
        @param $callback callable
        @return
    */
    public function unlikePost($post_id, callable $callback = NULL){
        $this->sessID();
        $post = '{"contentId":"'.$post_id.'"}';
        $http = $this->response($this->http('like/cancel.json?sourceType=TIMELINE&homeId=x', $post));
        return ($this->_end($this->isOK(), $callback) == true);
    }
    /*
        @title Like Timeline (This Session)
        @param $max integer
        @param $reaction integer
        @param $mode integer
        @param $share_mode integer
        @param $callback callable
        @return 
    */
    public function likeTimeline($user = NULL, $max = 1, $reaction = 0, $mode = 1, $share_mode = 1, callable $callback = NULL){
        $this->sessID();
        if(!in_array($mode, [1,2,3])) throw new Exception('Invalid mode!');
        if(!in_array($share_mode, [1,2,3,4])) throw new Exception('Invalid share mode!');
        if(!in_array($reaction, [0,1,2,3,4,5,6])) throw new Exception('Invalid reaction id!');
        $this->getHomeList($user, function($timeline) use ($max, $reaction, $mode, $share_mode, $callback){
            $n = 0;
            if(is_array($timeline) && count($timeline) > 0){
                foreach($timeline as $tl){
                    if($n >= $max) break;
                    if($reaction == 0) $reaction = mt_rand(1, 6);
                    $post_d = $tl['post']['postInfo']['postId'];
                    $liked = $tl['post']['postInfo']['liked'];
                    if($liked == false && $this->isPassLikesMode($tl, $mode)){
                        $this->likePost($post_d, $reaction, $this->isShareableMode($tl, $share_mode), $callback);
                        $n++;
                    }
                }
                return $this;
            } else {
                throw new Exception("Error occured!");
            }
        });        
    }
    private function getFriend($scrool_id = 1, callable $callback = NULL){
        $http = $this->response($this->http('friend/list.json?scrollId='.$scrool_id.'&limit=20'));
        if($this->isOK()){
            if(empty($http['friends'])) throw new Exception('Invalid response data!');
            if(is_callable($callback) && $callback != NULL){
                $callback($http['friends']);
            } else {
                return $http['friends'];
            }
        } else {
            throw new Exception('Invalid session data!');
        }
    }
    private function getHomeList($user_id = NULL, callable $callback = NULL){
        // $user_id example : _dQXvILQLzuN5-jSNMrfUNcemoCbkLSmRijRjFrU
        $this->sessID();
        if(empty($this->user)) $this->userinfo();
        if(empty($this->user) or empty($this->home_id)) throw new Exception('Invalid user info!');
        if($user_id == NULL){
            $http = $this->response($this->http('feed/list.json?postLimit='.$this->post_limit.'&commentLimit=2&likeLimit=20&order='.$this->timeline_order.'&requestTime=' . time()));
        } else {
            $http = $this->response($this->http('post/list.json?homeId='.$user_id.'&postLimit='.$this->post_limit.'&commentLimit=2&likeLimit=20&requestTime=' . time()));
        }
        if($this->isOK()){
            if(count($http['feeds']) < 1) throw new Exception('Invalid response data!');
            if(is_callable($callback) && $callback != NULL){
                $callback($http['feeds']);
            } else {
                return $http['feeds'];
            }
        } else {
            throw new Exception('Invalid session data!');
        }
    }
    private function is_official($data){
        if(!empty($data['post']['postInfo']['officialHome']['homeType']) && $data['post']['postInfo']['officialHome']['homeType'] == 'OFFICIAL'){
            return true;
        } else {
            return false;
        }
    }
    private function isPassLikesMode($data, $mode){
        switch($mode){
            case 1:
                return true;
                break;
            case 2: // User only
                if($this->is_official($data) == false){
                    return true;
                } else {
                    return false;
                }
                break;
            case 3: // line @ only
                if($this->is_official($data) == true){
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                throw new Exception("Error occured!");
        }
    }
    private function isShareableMode($data, $mode){
        switch($mode){
            case 1: // dont share anything
                return false;
                break;
            case 2: // share anything
                return true;
                break;
            case 3: // share if user
                if($this->is_official($data) == false){
                    return true;
                } else {
                    return false;
                }
                break;
            case 4: // share if line @ account
                if($this->is_official($data) == true){
                    return true;
                } else {
                    return false;
                }
            default:
                throw new Exception("Error occured!");
        }
    }
    private function sessID(){
        if(empty($this->session_id)){
            throw new Exception("Please set session id first!");
        }
    }
    private function isOK(){
        return $this->is_true_last;
    }
    private function response($response, $return = false){
        $res = json_decode($response[1], true);
        if(!empty($res) && $res['message'] == 'success' && isset($res['result'])){
            $this->is_true_last = true;
        } else {
            $this->is_true_last = false;
        }
        if($this->is_true_last){
            return $res['result'];
        } else {
            return [];
        }
    }
    private function _end($isOK, $callback){
        if($isOK == true){
            if($callback != NULL && is_callable($callback)){
                $callback(false, $this);
            } else {
                return true;
            }
        } else {
            if($callback != NULL && is_callable($callback)){
                $callback(true);
            } else {
                return false;
            }
        }
    }
    private function http($path, $postdata = ''){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->line_host . $path);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaderList($postdata));
        if(!empty($postdata)){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($http, $response);
    }
    private function getHeaderList($p){
        $ht = 'Host: timeline.line.me
            Connection: keep-alive
            Accept: application/json, text/plain, */*
            X-Timeline-WebVersion: 1.4.2
            X-Line-AcceptLanguage: en
            User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36
            Origin: https://timeline.line.me
            Content-Type: application/json;charset=UTF-8
            Referer: https://timeline.line.me/
            Accept-Encoding: gzip, deflate, br
            Accept-Language: en,id;q=0.8,ja;q=0.6
            Cookie: tc="'.$this->session_id.'";';
        if(!empty($p) && strlen($p) > 0){
            $ht .= "\nContent-Length: " . strlen($p);
        }
        return array_map(function($val){
            return trim($val);
        }, explode("\n", $ht));
    }
}

class Exception extends \Exception {

}
