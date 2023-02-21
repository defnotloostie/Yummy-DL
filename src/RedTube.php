<?php
namespace Loostie\YummyDL;

class RedTube {
    public $videoUrl;

    private $regx_vid = '/"videoUrl":"(?:[^"]|"")*"/s';
    private $regx_thumb = '/"thumbnailUrl":"(?:[^"]|"")*"/s';
    private $regx_title = '/<title>[A-Zs+\a-z]+<\/title>/s';
    private $regx_tags = '/<div class="tag_name">[A-Z + a-z]+<\/div>/s';
    private $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

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

        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST  =>"GET",
            CURLOPT_POST           =>false,
            CURLOPT_USERAGENT      => $this->user_agent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
        );
        
        try {
            $ch = curl_init($this->videoUrl);
            curl_setopt_array($ch, $curl_opt_array);
            $page = curl_exec($ch);
            curl_close($ch);

            preg_match_all($this->regx_vid, $page, $page_match);
            preg_match($this->regx_thumb, $page, $thumb_match);
            preg_match($this->regx_title, $page, $title_match);
            preg_match_all($this->regx_tags, $page, $tags_match);

            if(!$thumb_match[0]) {
                return (object)$data;
            }

            $title = str_replace(' - RedTube</title>', '', str_replace('<title>', '', $title_match[0]));

            $tags = array();
            
            foreach ($tags_match[0] as $tag) {
                $trimmed = rtrim(str_replace('<div class="tag_name">', '', $tag), '</div>');
                array_push($tags, $trimmed);
            }
            

            $thumb = rtrim(str_replace('"thumbnailUrl":"', '', $thumb_match[0]), '"');
            $stage_1 = rtrim(ltrim(end($page_match[0]), '"videoUrl":'), '"');

            $ch = curl_init(str_replace('\\', '', $stage_1));
            curl_setopt_array($ch, $curl_opt_array);
            $page = curl_exec($ch);

            preg_match_all($this->regx_vid, $page, $dl_match);

            $vid_dl = rtrim(ltrim($dl_match[0][0], '"videoUrl":'), '"');

            $data = array(
                "found" => true,
                "vidLink" => str_replace('\\', '', $vid_dl),
                "thumbnail" => str_replace('\\', '', $thumb),
                "title" => $title,
                "tags" => $tags
            );

            return (object)$data;

        } catch (\Exception) {
            return (object)$data;
        }
        
    }
}