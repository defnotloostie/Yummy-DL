<?php
namespace Loostie\YummyDL;

class YummyXvideos {
    public $videoUrl;

    private $regx_vid = '/html5player\.setVideoUrlHigh\(\'([^\']*)\'\);/s';
    private $regx_thumb = '/html5player\.setThumbUrl\(\'([^\']*)\'\);/s';
    private $regx_title = '/html5player\.setVideoTitle\(\'([^\']*)\'\);/s';
    private $regx_tags = '/"video_tags":\[([^\]]*)],/s';

    public function setUrl($videoUrl) 
    {
        $this->videoUrl = $videoUrl;
    }

    public function getVideoData(): object
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
        preg_match($this->regx_thumb, $page, $vid_thumb);
        preg_match($this->regx_title, $page, $vid_title);
        preg_match($this->regx_tags, $page, $vid_tags);

        if (!empty($vid_dl)) {
            $vid_found = true;
            $dl_link = $vid_dl[1];
            $thumb_link = $vid_thumb[1];
            $p_title = $vid_title[1];
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
                "title" => $p_title,
                "tags" => $p_tags
            );
        }

        return (object)$data;
    }

}