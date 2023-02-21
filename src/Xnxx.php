<?php
namespace Loostie\YummyDL;

class YummyXnxx {
    public $videoUrl;

    private $regx_vid = '/"contentUrl": "([^"]*)",/s';
    private $regx_thumb = '/"thumbnailUrl": \[[^\]]*],/s';
    private $regx_title = '/<title>[A-Zs+\a-z]+<\/title>/s';
    private $regx_tags = '/<meta name="keywords" content="([^"]*)"/s';

    public function setUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;
    }

    public function getVideoData()
    {
        if (!$this->videoUrl) {
            return false;
        }

        $data = array(
            "found" => false,
            "vidLink" => false,
            "thumbnail" => false,
            "title" => false,
            "tags" => false
        );

        $page = @file_get_contents($this->videoUrl);

        if (!$page) {
            return (object)$data;
        }

        preg_match($this->regx_vid, $page, $vid_dl);
        preg_match($this->regx_thumb, $page, $vid_thumb_s1);
        preg_match($this->regx_title, $page, $vid_title);
        preg_match($this->regx_tags, $page, $vid_tags);
        preg_match_all('/\\["(.*?)"\\]/', $vid_thumb_s1[0], $vid_thumb); 

        if (!empty($vid_dl)) {
            $vid_found = true;
            $title = htmlspecialchars_decode(str_replace(' - XNXX.COM</title>', '', str_replace('<title>', '', $vid_title[0])));
            $dl_link = $vid_dl[1];
            $thumb_link = $vid_thumb[1][0];
            $tags_raw = $vid_tags[1];

            $tags_stripped = str_replace('"', '', $tags_raw);
            $p_tags = explode(",", $tags_stripped);
        } else {
            $vid_found = false;
        }

        if ($vid_found) {
            $data = array(
                "found" => true,
                "vidLink" => $dl_link,
                "thumbnail" => $thumb_link,
                "title" => $title,
                "tags" => $p_tags
            );
        }

        return (object)$data;
    }
}