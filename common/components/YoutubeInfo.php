<?php
namespace common\components;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class YoutubeInfo extends Component
{
    public $id;
    protected $itags = [
        5 => ['type' => 'FLV', 'resolution' => '240p', 'flag' => ''],
        17 => ['type' => '3GP', 'resolution' => '144p', 'flag' => ''],
        18 => ['type' => 'MP4', 'resolution' => '360p', 'flag' => ''],
        22 => ['type' => 'MP4', 'resolution' => '720p', 'flag' => ''],
        34 => ['type' => 'FLV', 'resolution' => '360p', 'flag' => ''],
        35 => ['type' => 'FLV', 'resolution' => '480p', 'flag' => ''],
        36 => ['type' => '3GP', 'resolution' => '240p', 'flag' => ''],
        37 => ['type' => 'MP4', 'resolution' => '1080p', 'flag' => ''],
        38 => ['type' => 'MP4', 'resolution' => '1080p', 'flag' => ''],
        43 => ['type' => 'WEB', 'resolution' => '360p', 'flag' => ''],
        44 => ['type' => 'WEB', 'resolution' => '480p', 'flag' => ''],
        45 => ['type' => 'WEB', 'resolution' => '720p', 'flag' => ''],
        46 => ['type' => 'WEB', 'resolution' => '1080p', 'flag' => ''],
        82 => ['type' => 'MP4', 'resolution' => '360p', 'flag' => '3D'],
        83 => ['type' => 'MP4', 'resolution' => '480p', 'flag' => '3D'],
        84 => ['type' => 'MP4', 'resolution' => '720p', 'flag' => '3D'],
        85 => ['type' => 'MP4', 'resolution' => '1080p', 'flag' => '3D'],
        100 => ['type' => 'WEB', 'resolution' => '360p', 'flag' => '3D'],
        101 => ['type' => 'WEB', 'resolution' => '480p', 'flag' => '3D'],
        102 => ['type' => 'WEB', 'resolution' => '720p', 'flag' => '3D'],
        133 => ['type' => 'MP4', 'resolution' => '240p', 'flag' => 'VO'],
        134 => ['type' => 'MP4', 'resolution' => '360p', 'flag' => 'VO'],
        135 => ['type' => 'MP4', 'resolution' => '480p', 'flag' => 'VO'],
        136 => ['type' => 'MP4', 'resolution' => '720p', 'flag' => 'VO'],
        137 => ['type' => 'MP4', 'resolution' => '1080p', 'flag' => 'VO'],
        138 => ['type' => 'MP4', 'resolution' => '2160p', 'flag' => 'VO'],
        139 => ['type' => 'MP4', 'resolution' => 'Low bitrate', 'flag' => 'AO'],
        140 => ['type' => 'MP4', 'resolution' => 'Med bitrate', 'flag' => 'AO'],
        141 => ['type' => 'MP4', 'resolution' => 'Hi bitrate', 'flag' => 'AO'],
        160 => ['type' => 'MP4', 'resolution' => '144p', 'flag' => 'VO'],
        171 => ['type' => 'WEB', 'resolution' => 'Med bitrate', 'flag' => 'AO'],
        172 => ['type' => 'WEB', 'resolution' => 'Hi bitrate', 'flag' => 'AO'],
        242 => ['type' => 'WEB', 'resolution' => '240p', 'flag' => 'VOX'],
        243 => ['type' => 'WEB', 'resolution' => '360p', 'flag' => 'VOX'],
        244 => ['type' => 'WEB', 'resolution' => '480p', 'flag' => 'VOX'],
        245 => ['type' => 'WEB', 'resolution' => '480p', 'flag' => 'VOX'],
        246 => ['type' => 'WEB', 'resolution' => '480p', 'flag' => 'VOX'],
        247 => ['type' => 'WEB', 'resolution' => '720p', 'flag' => 'VOX'],
        248 => ['type' => 'WEB', 'resolution' => '1080p', 'flag' => 'VOX'],
        264 => ['type' => 'MP4', 'resolution' => '1080p', 'flag' => 'VO'],
        271 => ['type' => 'WEBM', 'resolution' => '1440p', 'flag' => 'VO'],
        272 => ['type' => 'WEBM', 'resolution' => '2160p', 'flag' => 'VO']
    ];

    private $_attributes;

    public function init()
    {
        if (!$this->id) {
            throw new InvalidConfigException('Please provider video id');
        }
    }

    public function getItag($itag, $key = false)
    {
        if ($this->hasItag($itag)) {
            if ($key) {
                return ArrayHelper::getValue($this->itags, $itag . '.' . $key);
            } else {
                return ArrayHelper::getValue($this->itags, $itag);
            }
        }
        return false;
    }

    protected function hasItag($itag)
    {
        return ArrayHelper::keyExists($itag, $this->itags);
    }

    public function getFetch()
    {
        return [
            'title' => $this->title,
            'image' => $this->thumbnail,
            'sources' => $this->formats
        ];
    }

    public function getSources()
    {
        return [

        ];
    }

    public function getTitle()
    {
        return ArrayHelper::getValue($this->attributes, 'title');
    }

    public function getSlug()
    {
        return Inflector::slug($this->title);
    }

    public function getThumbnail($type = 'iurlhq')
    {
        return ArrayHelper::getValue($this->attributes, $type);
    }

    public function getFormats()
    {
        $res = [];
        if (ArrayHelper::keyExists('adaptive_fmts', $this->attributes)) {
            //$availableFormats = ArrayHelper::getValue($this->attributes, 'adaptive_fmts');
        }
        if (ArrayHelper::keyExists('url_encoded_fmt_stream_map', $this->attributes)) {
            $availableFormats = isset($availableFormats) ? $availableFormats . ',' : '' . ArrayHelper::getValue($this->attributes, 'url_encoded_fmt_stream_map');
        }
        if (isset($availableFormats)) {
            foreach (explode(",", $availableFormats) as $availableFormat) {
                $format['itag'] = $format['sig'] = '';
                parse_str($availableFormat, $format);
                $itag = ArrayHelper::getValue($format, 'itag');
                $url = ArrayHelper::getValue($format, 'url');
                if ($this->hasItag($itag)) {
                    if (!ArrayHelper::keyExists($this->getItag($itag, 'resolution'), $res)) {
                        $res[$this->getItag($itag, 'resolution')] = [
                            'file' => urldecode($url) . '&title=' . $this->slug,
                            'label' => $this->getItag($itag, 'resolution')
                            //'type' => strtolower($this->getItag($itag, 'type')),
                        ];
                    }
                }
            }
        }
        return array_values($res);
    }


    protected function getAttributes()
    {
        if (!$this->_attributes) {
            $this->getVideoInfo();
        }
        return $this->_attributes;
    }

    protected function getVideoInfo()
    {
        $url = "http://www.youtube.com/get_video_info?&video_id={$this->id}&asv=3&el=detailpage&hl=en_US";
        $h = curl_init($url);
        curl_setopt($h, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 3);
        $source = curl_exec($h);
        curl_close($h);
        parse_str($source, $this->_attributes);
    }
}