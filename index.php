<?php

abstract class Api {

    abstract public static function getUsers(string $url): array;
    abstract public static function getPosts(string $url, int $userId): array;
    abstract public static function getTodos(string $url, int $userId): array;

    abstract public static function storePost(string $url, int $userId, string $title, string $body): array;
    abstract public static function updatePost(string $url, int $userId, string $title, string $body, int $postId): array;
    abstract public static function deletePost(string $url, int $postId): array;
}

class JsonApi extends Api {

    public static function getUsers($url) : array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $res = curl_exec($curl);
        curl_close($curl);
        return (array) json_decode($res);
    }
    public static function getPosts($url, int $userId): array
    {
        if(!isset($userId)) {
            return ['msg' => 'User ID must be set'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $res = curl_exec($curl);
        curl_close($curl);
        return (array) json_decode($res);
    }
    public static function getTodos($url, int $userId): array
    {
        if(!isset($userId)) {
            return ['msg' => 'User ID must be set'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?userId=' . $userId);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $res = curl_exec($curl);
        curl_close($curl);
        return (array) json_decode($res);
    }
    public static function storePost($url, $userId = 0, $title = '', $body = ''): array
    {
        if($userId === 0) {
            return ['msg' => 'User ID must be set'];
        }
        $post = [
            "userId" => $userId,
            "title" => $title,
            "body" => $body
        ];
        $data_string = json_encode($post);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=UTF-8',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $res = curl_exec($curl);
        curl_close($curl);
        return (array) json_decode($res);
    }
    public static function updatePost($url, $userId = 0, $title = '', $body = '', $postId = null): array
    {
        if($userId === 0 || !isset($postId)) {
            return ['msg' => 'User or post ID must be set'];
        }
        $post = [
            "id" => $postId,
            "userId" => $userId,
            "title" => $title,
            "body" => $body
        ];
        $data_string = json_encode($post);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . "/$postId");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=UTF-8',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $res = curl_exec($curl);
        curl_close($curl);
        return (array) json_decode($res);
    }
    public static function deletePost($url, $postId): array
    {
        if(!isset($postId)) {
            return ['msg' => 'Post ID must be set'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . "/$postId");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $res = curl_exec($curl);
        curl_close($curl);
        return ['msg' => 'Delete success'];
    }
}

// Example of executing class methods

$domain = 'https://jsonplaceholder.typicode.com';
$userId = 1;

$users = JsonApi::getUsers($domain.'/users');
$userPosts = JsonApi::getPosts($domain.'/posts', $userId);
$userTodos = JsonApi::getTodos($domain.'/todos', $userId);

$title = 'Title';
$body = 'Some body text';
$newPost = JsonApi::storePost($domain.'/posts', $userId, $title, $body);

$newTitle = 'New Title';
$newBody = 'New another wonderful text';
$postId = 1;
$updatePost = JsonApi::updatePost($domain.'/posts', $userId, $newTitle, $newBody, $postId);
$deletePost = JsonApi::deletePost($domain.'/posts', $postId);



