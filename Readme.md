# YummyDL

YummyDL provides a simple way to fetch video-data on adult websites

## Installation

The best way to obtain YummyDL is to download it using composer

```sh
composer require loostie/yummy-dl
```

## Usage

To keep everything simple all sites will return the same object, which will look like this:

```php
{
    ["found"] => bool,
    ["vidLink"] => string,
    ["thumbnail"] => string,
    ["title"] => string,
    ["tags"] => array

}
```

If the video data for some reason could not be retrieved, all of the above will be ```false```

### Example

```php
<?php
require 'vendor/autoload.php'; // Composer's autoloader

use Loostie\YummyDL\YummyRedTube;       // For RedTube
use Loostie\YummyDL\YummySpankBang;     // For SpankBang
use Loostie\YummyDL\YummyXnxx;          // For Xnxx
use Loostie\YummyDL\YummyXvideos;       // For Xvideos

// In this example we use Xvideos, but the procedure is the same for all of them
$video = new YummyXvideos();
$video->setUrl("https://xvideos.com/your_preferred_video");
$video_data = $video->getVideoData();

// This is just an example where we var_dump the response
// Then check if video data was found
var_dump($video_data);
if ($video_data->found) {
    echo $video_data->title     // The title of the video
    echo $video_data->thumbnail // Link to the video thumbnail (you could use this in <img src>)
    echo $video_data->vidLink   // Direct link to the video (not on the site), where it can be downloaded
    foreach ($video_data->tags as $tag) {
        echo $tag               // All tags for the video
    }
}
```

## Supported Websites (NSFW LINKS)

- [RedTube](https://redtube.com)
- [SpankBang](https://spankbang.com)
- [Xvideos](https://xvideos.com)
- [Xnxx](https://xnxx.com)