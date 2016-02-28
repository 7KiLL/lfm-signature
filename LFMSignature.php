<?php
/**
 * Created by PhpStorm.
 * User: 7KiLL
 * Date: 27/02/16
 * Time: 23:38
 */
class LFMNowPlaying {
    static public $_api = "59c75ce54be869532e03f89b19edd849";
    static public $_nowplaying = "Now Playing: ";
    static public $_lastplayed = "Last Played: ";
    static public $_divider = " - ";
    static public $_case = 0;


    //Image Params
    static public $_font = array();
    static public $_size = array();

    static public $_r  = array();
    static public $_g  = array();
    static public $_b  = array();

    static public $_img = array();

    static public $_x = array();
    static public $_y = array();


    function BuildPicture($font, $size, $hex, $img, $x = 10, $y = 135) {
        self::$_font[self::$_case] = "Fonts/".$font.".ttf";
        self::$_size[self::$_case] = $size;

        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hex); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    $rgb = $rgbArray; // returns the rgb string or the associative array

    self::$_r[self::$_case] = $rgb['red'];
    self::$_g[self::$_case] = $rgb['green'];
    self::$_b[self::$_case] = $rgb['blue'];

        if(preg_match('/http:/', $img))
            self::$_img[self::$_case] = $img;
        else {
            self::$_img[self::$_case] = 'pics/'.$img;
        }



        self::$_x[self::$_case] = $x;
        self::$_y[self::$_case] = $y;

        self::$_case++;
    }

    function Run($user, $np = null, $lp = null, $divider = null)
    {
        $url =  'http://ws.audioscrobbler.com/2.0/?method=user.getRecentTracks&format=json&user=' . $user . '&api_key=' .self::$_api . '&limit=1';
        $json = file_get_contents($url);
        $songs = json_decode($json, TRUE);
        $artist = $songs["recenttracks"]["track"][0]["artist"]["#text"];
        $song = $songs["recenttracks"]["track"][0]["name"];
        if($np == null) 
            $np = self::$_nowplaying;
        if($lp == null)
            $lp = self::$_lastplayed;
        if($divider == null)
            $divider = self::$_divider;
        $pre = $lp;
        if($songs["recenttracks"]["track"][0]["@attr"]["nowplaying"] == "true")
            $pre = $np;

        $text = $pre.$artist.$divider.$song;

        $i = rand(0, self::$_case-1);
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