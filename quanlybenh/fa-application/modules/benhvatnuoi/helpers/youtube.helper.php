<?php
if (!function_exists('youtube_id'))
{
    function youtube_id($url)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        if ($matches[0])
        {
            return $matches[0];
        }
        return false;
    }
}

if (!function_exists('valid_youtube_url'))
{
    function valid_youtube_url($url) {
        if (!preg_match('/^http:\/\/(?:www\.)?(?:youtube.com|youtu.be)\/(?:watch\?(?=.*v=([\w\-]+))(?:\S+)?|([\w\-]+))$/i', $url))
        {
            return false;
        }
        return true;
    }
}