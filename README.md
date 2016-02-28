# LastFM Signature

Simple class for those who want to make their signature cool. 
Features:

1. Custom font, font size, color, offset 
2. Custom image, local file or URL 
3. Varity. You can make many cases of images with custom settings and return them randomly

### How to
1. Include class to new `.php` page or append code on this. 
2. Build image case using `BuildPicture()`.
3. Then just run script via `Run()` with your username.

For example
```PHP
$creator = new LFMNowPlaying();
//For local image with standart offsets
$creator->BuildPicture("DancingScript", 16, "#FF0000", "rin.png"); 
//For hosted image with custom offset
$creator->BuildPicture("Permanent Marker", 16, "008cf0", "http://i.imgur.com/VRUiYl7.png", 8, 130); 

$creator->Run("mr7kill");
```
Make sure that `Fonts` dir contains used fonts and pictures located in `pics` directory. 
If you need custom prefixes read script documentation for additional information. 

###[Live Demo](http://sig-lfmgen.rhcloud.com/json.php)
