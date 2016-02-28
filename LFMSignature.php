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

    static public $_api = "59c75ce54be869532e03f89b19edd849"; //Your LastFM API Key
    static public $_nowplaying = "Now Playing: ";             //For currently playing song
    static public $_lastplayed = "Last Played: ";             //For the last song
    static public $_divider = " - ";                          //Char between artist and song
    static public $_case = 0;                                 //Array random range


    //Image Params
    /**
     * @var array   Font path. Fonts have to be placed into 'Fonts/' dir
     * @var array   Font size
     */
    static public $_font = array();
    static public $_size = array();

    /**
     * @var array   $_r ,$_g, $_b RGB params took from HEX.
     */
    static public $_r  = array();
    static public $_g  = array();
    static public $_b  = array();

    /**
     * @var array  Image path. Pics have to be placed into 'pics' dir or can be URL format
     */
    static public $_img = array();

    /**
     * @var array $_x $_y X and Y offset default bottom(X:10, Y:135)
     */
    static public $_x = array();
    static public $_y = array();


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

        /**
         * @author hafees
         * @url http://php.net/manual/ru/function.hexdec.php#99478
         * Just simple modification for current script
         */
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
$creator->BuildPicture("Permanent Marker", 16, "E000D9", "eriri.png");
$creator->Run("Mr7kill");
?>
