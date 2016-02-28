# LastFM Signature

Simple class for those who want to make their signature cool. 
Features:

1. Custom font, font size, color, offset 
2. Custom image, can be located on your directory or hosting via URL
3. Varity. You can make many cases of images with custom settings and return it randomly

### How to
1. Include class to new `.php` page or append code on this. 
2. Build image case using `BuildPicture()`

For example
```PHP
$creator = new LFMNowPlaying();
$creator->BuildPicture("DancingScript", 16, "#FF0000", "rin.png");
$creator->BuildPicture("Permanent Marker", 16, "008cf0", "http://i.imgur.com/VRUiYl7.png", 8, 130);
```
Make sure that `Fonts` dir contains those fonts and pictures located in `pics` directory.
Then just run class with your username
```PHP
$creator->Run("Mr7kill");
```

3. It will make script show your image. 
4. ???
5. PROFIT

[Live Demo](http://sig-lfmgen.rhcloud.com/json.php)
