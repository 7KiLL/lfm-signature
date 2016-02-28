<?php
/**
 * Created by PhpStorm.
 * User: 7KiLL
 * Date: 27/02/16
 * Time: 23:38
 */

/**
 * Class LFMNowPlaying
 * Has arrays of images params. Then take random index and show it for user with LastFM song.
 */
class LFMNowPlaying {

    static private $_api = "59c75ce54be869532e03f89b19edd849"; //Your LastFM API Key
    static private $_nowplaying = "Now Playing: ";             //For currently playing song
    static private $_lastplayed = "Last Played: ";             //For the last song
    static private $_divider = " - ";                          //Char between artist and song
    static private $_case = 0;                                 //Array random range


    //Image Params
    /**
     * @var array   Font path. Fonts have to be placed into 'Fonts/' dir
     * @var array   Font size
     */
    static private $_font = array();
    static private $_size = array();

    /**
     * @var array   $_r ,$_g, $_b RGB params took from HEX.
     */
    static private $_r  = array();
    static private $_g  = array();
    static private $_b  = array();

    /**
     * @var array  Image path. Pics have to be placed into 'pics' dir or can be URL format
     */
    static private $_img = array();

    /**
     * @var array $_x $_y X and Y offset default bottom(X:10, Y:135)
     */
    static private $_x = array();
    static private $_y = array();

    /**
     * @author hafees
     * @url http://php.net/manual/ru/function.hexdec.php#99478
     * @param string $hex Hex code
     * @return array $RGB
     * @throws Exception Bad HEX given
     * Just simple modification for current script
     */
    function HEX2RGB($hex) {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hex); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) {
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) {
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            throw new Exception('Wrong HEX');
        }
        return $rgbArray;
    }

    /**
     * @param $font         string Fonts/
     * @param $size         int Normal 14-16, but it depends of font
     * @param $hex          string HEX code of color. With # or without.
     * @param $img          string pics/ or URL
     * @param int $x        X offset
     * @param int $y        Y offset
     * @throws Exception    Bad HEX given
     */
    function BuildPicture($font, $size, $hex, $img, $x = 10, $y = 135) {
        self::$_font[self::$_case] = "Fonts/".$font.".ttf";
        self::$_size[self::$_case] = $size;

        //Hex
        $rgbArray = $this->HEX2RGB($hex);
        self::$_r[self::$_case] = $rgbArray['red'];
        self::$_g[self::$_case] = $rgbArray['green'];
        self::$_b[self::$_case] = $rgbArray['blue'];

        //Check string for URL or local file path
        if(preg_match('/http:/', $img))
            self::$_img[self::$_case] = $img;
        else {
            self::$_img[self::$_case] = 'pics/'.$img;
        }

        //Offsets
        self::$_x[self::$_case] = $x;
        self::$_y[self::$_case] = $y;

        self::$_case++;
    }

    /**
     * @param $user string LastFM username. Required field.
     * @param null $np   Now playing prefix
     * @param null $lp   Prefix for past songs
     * @param null $divider Middle char
     */
    function Run($user, $np = null, $lp = null, $divider = null)
    {
        //Building URL
        $user = trim($user);
        $url =  'http://ws.audioscrobbler.com/2.0/?method=user.getRecentTracks&format=json&user=' . $user . '&api_key=' .self::$_api . '&limit=1';
        $json = file_get_contents($url); //Parse API callback
        $songs = json_decode($json, TRUE); //Serialize JSON as assoc array
        $artist = $songs["recenttracks"]["track"][0]["artist"]["#text"];
        $song = $songs["recenttracks"]["track"][0]["name"];

        //Text style. Choose default values or take user's
        if($np == null)
            $np = self::$_nowplaying;
        if($lp == null)
            $lp = self::$_lastplayed;
        if($divider == null)
            $divider = self::$_divider;
        $pre = $lp;
        //Get "nowplaying" attribute from JSON. Returns true if exists.
        if($songs["recenttracks"]["track"][0]["@attr"]["nowplaying"] == "true")
            $pre = $np;

        $text = $pre.$artist.$divider.$song;

        //$_case increment higher than images count, so we make it right.
        $i = rand(0, self::$_case-1);
        /**
         * @url http://php.net/manual/ru/function.imagecreatefrompng.php
         * @url http://php.net/manual/ru/function.imagecolorallocate.php
         * @url http://php.net/manual/ru/function.imagettftext.php
         */
        $img = imagecreatefrompng(self::$_img[$i]);
        $color = imagecolorallocate($img, self::$_r[$i], self::$_g[$i], self::$_b[$i]);
        imagettftext(
            $img,
            self::$_size[$i],
            0,
            self::$_x[$i],
            self::$_y[$i],
            $color,
            self::$_font[$i],
            $text);
        header("Content-type: image/png");  //Header tweaks. Jpeg should have different content-type
        imagepng($img);  //Show pic
        imageDestroy($img);  //Destroy pic from memory
    }

}

$creator = new LFMNowPlaying();
$creator->BuildPicture("DancingScript", 16, "#FF0000", "rin.png");
$creator->BuildPicture("Permanent Marker", 16, "008cf0", "http://i.imgur.com/VRUiYl7.png", 8, 130);
$creator->Run("mr7kill");
?>
