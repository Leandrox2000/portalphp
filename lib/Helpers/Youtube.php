<?php

namespace Helpers;

/**
 * Description of Youtube
 *
 * @autor Join-ti
 */
class Youtube
{

    /**
     *
     * @var String
     */
    private $key;

    /**
     *
     * @var Object
     */
    private $data;

    /**
     *
     * @var String
     */
    private $embed;

    /**
     *
     * @var Array
     */
    private $image = array('big' => NULL, 'small' => NULL);

    /**
     *
     * @param String $url
     */
    public function __construct($url = null)
    {
        if ($url !== NULL)
            $this->key = $url;
    }

    /**
     *
     * @param String $url
     * @return String
     */
    public function getImages($url = NULL)
    {
        $this->getKey($url);
        $this->image['big'] = '//img.youtube.com/vi/' . $this->key . '/0.jpg';
        $this->image['small'] = '//i1.ytimg.com/vi/' . $this->key . '/default.jpg';
        return $this->image;
    }

    /**
     *
     * @param String $url
     * @return String
     */
    public function getEmbed($url = NULL)
    {
        $this->getKey($url);
        return $this->embed = '//youtube.com/embed/' . $this->key . '?autoplay=1';
    }

    /**
     *
     * @param String $url
     * @return boolean|array
     */
    public function getInfor($url = NULL)
    {
        if ($url !== NULL)
            $this->key = $url;

        if ($this->gData()) {
            $this->getImages();
            $this->getEmbed();

            return array('image' => $this->image,
                'key' => $this->key,
//                'embed' => $this->embed,
//                'published' => $this->data->published,
//                'updated' => $this->data->updated,
                'title' => (string) $this->data['title'],
//                'description' => (string) $this->data->content,
                'description' => '',
                'author' => (string) $this->data['author_name'],
                'htmlEmbed' => (string) $this->data['html']
//                'htmlEmbed' => "<embed width='640' height='360' src='" . $this->key . "&showinfo=0' wmode='transparent' type='application/x-shockwave-flash' ></embed>"
            );
        } else {
            return false;
        }
    }

    /**
     *
     * @param String $url
     * @return String
     */
    public function getKey($url = NULL)
    {
        if ($url !== NULL) {
            preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
            $this->key = $matches[1];
        }
        return $this->key;
    }

    /**
     *
     * @param String $url
     * @return boolean
     */
    private function gData($url = NULL)
    {
        if ($url !== NULL)
            $this->key = $url;

        $curl = curl_init("http://www.youtube.com/oembed?url=". $this->key ."&format=json");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        if(strtolower($json) !== 'invalid id')
            return $this->data = json_decode($json, true);
        else
            return false;
    }

}