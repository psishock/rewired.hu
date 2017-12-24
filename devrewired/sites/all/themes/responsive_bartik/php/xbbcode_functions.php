<?php

function addHttpIfMissing($input_url) {
	//$test = addScheme($url, $scheme = 'http://'); 
	$url = $input_url;
	$url = parse_url($url, PHP_URL_SCHEME) === null ? 'http://' . $url : $url;

	return $url;
}

function strposa($haystack, $needles) {
	if ( is_array($needles) ) {
		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = strposa($haystack, $str);
			} else {
				$pos = strpos($haystack, $str);
			}

			if ($pos !== FALSE) {
				return $pos;
			}
		}
	} 
	else {
		return strpos($haystack, $needles);
	}
}

function is_ani($filename) {
	//$filename = htmlspecialchars_decode($filename);	
	
    if(!($fh = @fopen($filename, 'rb'))) {

		/*
		echo "Error: ";
		foreach ($http_response_header as $value) {
			echo $value . PHP_EOL;
		} 
		*/
		
		return "cannot_read";
		//return 2;
	}
	
	$fh = fopen($filename, 'rb');
	
    $count = 0;
    //an animated gif contains multiple "frames", with each frame having a
    //header made up of:
    // * a static 4-byte sequence (\x00\x21\xF9\x04)
    // * 4 variable bytes
    // * a static 2-byte sequence (\x00\x2C)

    // We read through the file til we reach the end of the file, or we've found
    // at least 2 frame headers
    while(!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024 * 100); //read 100kb at a time
        $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
    }

    fclose($fh);

	if ($count > 1) 
		return "animated";
	else 
		return "non_animated";
}

function makeGifThumbnail($input_url) {
	$input_url = htmlspecialchars_decode($input_url);	
	
	$filename = preg_replace("/[^a-z0-9]+/i", "", $input_url);
	$pathname = './sites/default/files/gifthumbs/';
	$gifThumbnail = $pathname . $filename . '.jpg';

	$animationStatus = is_ani($input_url);
	
	if ($animationStatus == "animated") {
		if (!file_exists($gifThumbnail)) {
			$jpeg_memory = imagecreatefromgif($input_url);
			imagejpeg($jpeg_memory,$gifThumbnail, "25", FILE_EXISTS_REPLACE);
			imagedestroy($jpeg_memory);
		}
		
		$url = file_create_url($gifThumbnail);
		$url = parse_url($url);
		$background = $url['path'];

		return $background;
	}
	else if ($animationStatus == "cannot_read") {
		return "cannot_read";
	}
	else {
		return "non_animated";
	}
}

function getSize($headers) {
	preg_match('/Content-Length..([^"]+)/i', $headers, $matches);
	if (count($matches)) { 
		$size = $matches[1];
		$size = intval($size);
		$size = $size / 1048576;
		$size = round($size, 1);
		$size = " (" . $size . " MB)";
	}
	
	return $size;
}

function isImage($headers) {
	$content = $headers;
	
	//echo "<pre>";
	//var_dump($headers);
	//echo "</pre>";
	
	if(isset($content)){
		
		if (is_array($content)) {
			$string = json_encode($content);
			$string = stripslashes($string);
		}
		else {
			$string = $content;
		}

		$string=strtolower($string);
		$valid_image_array  = array(
			'image/png', 
			'image/jpg', 
			'image/jpeg', 
			'image/jpe',
			'image/gif',
			'image/tif',
			'image/tiff',
			'image/svg',
			'image/ico',
			'image/icon',
			'image/x-icon'
		);
		
		$match = strposa($string, $valid_image_array);
		
		//var_dump($match);

		if ($match !== NULL) {
			//echo "have a match";
			return true; //yes its an image
		}
		//else echo "not matched";
	}
	
	return false;
}

function isImage2($input_url) {
	$ntct = Array(
		"1" => "image/gif",
		"2" => "image/jpeg", #Thanks to "Swiss Mister" for noting that 'jpg' mime-type is jpeg.
		"3" => "image/png",
		"6" => "image/bmp",
		"17" => "image/ico"
		);

	if ($ntct[exif_imagetype($input_url)]) {
			return true;
	}
	
	return;
}

function isGif($headers) {
	$content = $headers;
	
	if(isset($content)){
		if (is_array($content)) {
			$string = json_encode($content);
			$string = stripslashes($string);
		}
		else {
			$string = $content;
		}

		$string=strtolower($string);
		$match = strposa($string, 'image/gif');
		
		if ($match && $match !== NULL) {
			//echo "yes its a GIF!!";
			return true;
		}
	}
	//echo "not a GIF";
	return false;
}

function isGif2($input_url) {
	if (exif_imagetype($input_url) == 1) {
		//echo "yes its a GIF2!!";
		return true;
	}
	//echo "not a GIF2";
	return;
}

function get_headers_c($input_url, $referer = false) { //custom header check ******************************************************
	$url = $input_url;

	$parsed_url = parse_url($url);
	
	if (!isset($parsed_url['query'])) {
		$parsed_url['query'] = '';
	}
	else {
		$parsed_url['query'] = "?" . $parsed_url['query'];
		$parsed_url['query'] = htmlspecialchars_decode($parsed_url['query']);
	}
	
	if (!isset($referer)) {
		$referer = '';
	}
	else {
		$referer = "Referer: ". $referer. "\r\n";
	}
	
	$package = array(
		'header'=>
			"HEAD " . $parsed_url['path'] . $parsed_url['query'] . " HTTP/1.1\r\n"
			. "Host: " . $parsed_url['host'] . "\r\n"
			. "User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0\r\n"
			. "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
			. "Accept-Language: en-US,en;q=0.5\r\n"
			. "Accept-Encoding: gzip,deflate\r\n"
			. $referer
			. "Connection: close\r\n"
			. "Pragma: no-cache\r\n"
			. "Cache-Control: no-cache\r\n"
			. "\r\n"
	);
	
	$fp = fsockopen($parsed_url['host'], 80);
	
	if (!$fp) {
		return "invalid";
	} else {

		fwrite($fp, $package['header']);
		$result = "";
	
		do { // while(!feof($fp)) loop unsafe, using custom end of file determinator
			$newline_fgets = fgets($fp);
			$resultArray[] = preg_replace('/\r\n$/', '', $newline_fgets);
		} while ( $newline_fgets != "\r\n" );
		
		fclose($fp);
	
		/*
		$testString = $resultArray;
		if (is_array($testString)) {
			$testString = json_encode($testString);
			$testString = stripslashes($testString);
		}
		$testString=strtolower($testString);
		var_dump($testString);
		*/
	
		if (is_array($resultArray)) {
			$result = json_encode($resultArray);
			$result = stripslashes($result);
		}

		//$result = strtolower($result);
		
		//echo "<pre>";
		//var_dump($result);
		//echo "</pre>";
	
		return $result;
	}
}

function get_headers_tester($input_url) { //for dev testing
	$url = $input_url;

	$parsed_url = parse_url($url);
	
	if (!isset($parsed_url['query'])) {
		$parsed_url['query'] = '';
	}
	else {
		$parsed_url['query'] = "?" . $parsed_url['query'];
		$parsed_url['query'] = htmlspecialchars_decode($parsed_url['query']);
	}
	
	$package = array(
		'header'=>
			//"HEAD " . $parsed_url['path'] . ( $parsed_url['query'] ? "?" . $parsed_url['query'] : "" ) . " HTTP/1.1\r\n"
			"HEAD " . $parsed_url['path'] . $parsed_url['query'] . " HTTP/1.1\r\n"
			. "Host: " . $parsed_url['host'] . "\r\n"
			. "User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0\r\n"
			//. "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
			. "Accept: image/png,image/*;q=0.8,*/*;q=0.5\r\n"
			. "Accept-Language: en-US,en;q=0.5\r\n"
			. "Accept-Encoding: gzip,deflate\r\n"
			. "Referer: http://www.rewired.hu/\r\n"
			//. "Connection: keep-alive\r\n"
			. "Connection: close\r\n"
			. "Pragma: no-cache\r\n"
			. "Cache-Control: no-cache\r\n"
			//. "Keep-Alive: timeout=10, max=10"
			. "\r\n"
	);
	
	//echo "<pre>";
	//echo "original url: " . $url . "\r\n";
	//echo "scheme: " . $parsed_url['scheme'] . "\r\n";
	//echo "host: " . $parsed_url['host'] . "\r\n";
	//echo "path: " . $parsed_url['path'] . "\r\n";
	//echo "query: " . ( $parsed_url['query'] ? $parsed_url['query'] : "" ) . "\r\n";
	//echo "\r\n";
	//echo "rebuilt url: " . $parsed_url['scheme'] . "://" . $parsed_url['host'] . $parsed_url['path'] . ( $parsed_url['query'] ? "?" . $parsed_url['query'] : "" ) . "\r\n";  
	//echo "\r\n";
	
	echo "<pre>";
	echo "client request...<br>";
	var_dump($package['header']);
  
	$fp = fsockopen($parsed_url['host'], 80);
	fwrite($fp, $package['header']);
	$result = "";
	
	while(!feof($fp)){
		$resultArray[] = preg_replace('/\r\n$/', '', fgets($fp));
	}

	fclose($fp);
	
	echo "<pre>";
	echo "server response...<br>";
	echo "<b>";
	var_dump($resultArray[0]);
	echo "</b>";
	
	return;
}

function get_headers_curlversion($input_url) {
	$url = $input_url;
	
	//$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)'; //user agent 

	//$agent= 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10';
	
	$agent= 'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19';
	
	$ch = curl_init();

	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: ";
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //header spoof
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);  //agent spoof
	curl_setopt($ch, CURLOPT_REFERER, "http://www.rewired.hu"); //referer spoof
	curl_setopt($ch, CURLOPT_COOKIE, "foo=bar"); //cookie spoof
	
	curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, true); //we want header
	curl_setopt($ch, CURLOPT_NOBODY, true); //we dont want body
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); //dont follow redirects 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //returns everything as a string on exec instead displaying
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //maximum timeout
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD"); 
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "CONNECT"); 

	curl_setopt($ch, CURLOPT_URL,$url); //url
	$result=curl_exec($ch);

	curl_close($ch);
	
	//echo "<pre>";
	//var_dump($result);
	//echo "</pre>";
  
	return $result;
}

function get_data_curl($input_url, $referer = false) {
	$url = $input_url;
	$ch = curl_init();
	
	if (!isset($referer)) {
		$referer = '';
	}
	else {
		curl_setopt($ch, CURLOPT_REFERER, $referer); //referer spoof
	}

	$agent = 'Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0';
	
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);  //agent spoof
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //returns everything as a string on exec instead displaying
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //maximum timeout
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

	curl_setopt($ch, CURLOPT_URL,$url); //url
	$result=curl_exec($ch);

	curl_close($ch);
 
	return $result;
}

function imageCreateFromAny($filepath) {
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
        break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
        break;
        case 3 :
            $im = imageCreateFromPng($filepath);
        break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
        break;
    }   
    return $im; 
}

function setGifStatus($orig_url, $videotype) {
	$get_background = makeGifThumbnail($orig_url);

	if ($get_background == "non_animated") {}
	else if ($get_background == "cannot_read") 
		$videotype = "rawgif";
	else {
		$videotype = "rawgif";
		$background = $get_background;
	}
	
	$returnArray = array($background, $videotype);
	
	return $returnArray;
}

function rwUrlValidator ($orig_url) {
	$orig_url = trim(preg_replace('/\s+/', '', $orig_url));
	$orig_url = addHttpIfMissing($orig_url);
	$orig_url = htmlspecialchars_decode($orig_url);
	$orig_headers = get_headers_c($orig_url);
	
	$writeValue = $orig_headers;
	$file = fopen('./sites/default/files/newsimages/test.txt',"w");
	fwrite($file,$writeValue);
	fclose($file);
	
	if(preg_match('/400 Bad Request/i', $orig_headers)) {
		return "invalid";
	}
	
	return $orig_headers;
}

/* URL checker --------------------------------------------------------------------------------------------------------------------------------------------------------------- */
function checkUrlType ($orig_url, $orig_headers, $reference_url) {
	
//check if url = Youtube
if (preg_match('/(youtu\.be)|(youtube\.com\/watch)|(youtube\.com\/embed)/i', $orig_url)) {
	$videotype = "youtube";
	$apikey = "AIzaSyDWolV5C_5dmNvWe7LigsWLJAPzk774DBc";

	//get Youtube ID
	$pattern = '/(?<=\d\/|\.be\/|v[=\/])([\w\-]{11,})|^([\w\-]{11})|\/embed\/([\w\-]{11})/i';
	preg_match($pattern, $orig_url, $matches);
    if (count($matches)) {
		$matches = end($matches);
		//if (strlen($matches) == 11)
		$id = $matches;
    }
	else $id = 'dQw4w9WgXcQ';
	
    //get video start time
    $pattern = '/t=((\d+h)?(\d+m)?(\d+[s]?)?)/'; //get every type of youtube timecode
    preg_match($pattern, $orig_url, $matches);
    if (count($matches)) { 
       $videostarttime = $matches[1];
    }
    else $videostarttime = 0;
	
	preg_match('/start=([0-9]+)/i', $videostarttime, $matches); //if typecode = seconds only in digits
	if (count($matches)) { 
       $videostarttime = $matches[1];
    }
	else {//we need to convert it to seconds only in digits
		preg_match('/(\d+)h/i', $videostarttime, $matches); //get hours
		if (count($matches)) {
			$hours = $matches[1];
			$hours = intval($hours);
			$hours = $hours * 60 * 60;
			$totaltime = $hours;
		}
	
		preg_match('/(\d+)m/i', $videostarttime, $matches); //get minutes
		if (count($matches)) { 
			$minutes = $matches[1];
			$minutes = intval($minutes);
			$minutes = $minutes * 60;
			$totaltime = $totaltime + $minutes;
		}
		
		preg_match('/(\d+)s/i', $videostarttime, $matches); //get seconds
		if (count($matches)) { 
			$seconds = $matches[1];
			$seconds = intval($seconds);
			$totaltime = $totaltime + $seconds;
		}
		
		if(isset($totaltime)) {
			$videostarttime = $totaltime;
		}
		
	}

    // returns a single line of JSON that contains the video title. Not a giant request.
    $videoTitle1 = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=" . $id . "&key=$apikey&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
    if ($videoTitle1) {
      $json = json_decode($videoTitle1, true);
      $videotitle = $json['items'][0]['snippet']['title'];
    }

	
	
    // get Youtube duration
    $dur = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$id&key=$apikey");
    $VidDuration =json_decode($dur, true);
    foreach ($VidDuration['items'] as $vidTime)
    {
    $videoduration = $vidTime['contentDetails']['duration'];
        //duration is on ISO 8601 format. Lets convert it to human readable hh:mm:ss
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($videoduration));
        $videoduration = $start->format('H:i:s');
    }

    //set Youtube background image
    $background = "https://i.ytimg.com/vi/" . $id . "/hqdefault.jpg";
	
	$data_url = url("https://www.youtube.com/embed/" .$id, $options = array(
        'query' => array(
          'autoplay' => 1,
          'autohide' => 1,
          'border' => 0,
          'wmode' => 'opaque',
          'enablejsapi' => 1,
          'start' => $videostarttime,
          'fs' => 1,
          'allowFullScreen' => 'true',
        )
    ));
}
//check if url = Vimeo
else if (preg_match('/vimeo\.com/i', $orig_url)) {
	$videotype = "vimeo";
	//get Vimeo ID
	//$pattern = '/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/';
	$pattern = '/vimeo.com\/(video\/)?(\d+)/i';
	
	preg_match($pattern, $orig_url, $matches);
	if (count($matches)) {
		$id = $matches[2];
	}

	// get background image, title, and duration from Vimeo
	$data = file_get_contents("https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/$id");
	$data = json_decode($data);
	$videotitle = $data->title;
	$videoduration = $data->duration;
	// duration is displayed on seconds. Lets convert it to hh:mm:ss
		$hms = "";
		$hours = intval(intval($videoduration) / 3600);
		$hms .= ($hours)
			? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':'
			: $hours . ':';
		$minutes = intval(($videoduration / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
		$seconds = intval($videoduration % 60);
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
	$videoduration = $hms;

	$background = $data->thumbnail_url;
	
	$data_url = url("//player.vimeo.com/video/" . $id, $options = array(
        'query' => array(
          'portrait' => 0,
          'color' => 333,
          'autoplay' => 1,
        )
    ));
}
//check if url = vid.me
else if (preg_match('/vid\.me/i', $orig_url)) {
	$videotype = "vidme";
	//get Vidme ID
	$pattern = '/vid.me\/(.*?)$/i';
    preg_match($pattern, $orig_url, $matches);
    if (count($matches)) {
      $id = $matches[1];
    }

    // get background image, title, and duration from Vimeo
    $data = file_get_contents("https://api.vid.me/videoByUrl?url=https%3A%2F%2Fvid.me%2F$id");
    $data = json_decode($data);

	//echo "<pre>";
	//var_dump($data);
	//echo "</pre>";
	
    $videotitle = $data->video->title;
    $videoduration = $data->video->duration;
    // duration is displayed on seconds. Lets convert it to hh:mm:ss
       $hms = "";
       $hours = intval(intval($videoduration) / 3600);
       $hms .= ($hours)
          ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':'
          : $hours . ':';
       $minutes = intval(($videoduration / 60) % 60);
       $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
       $seconds = intval($videoduration % 60);
       $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    $videoduration = $hms;

    $background = $data->video->thumbnail_url;
	
	$data_url = url("https://vid.me/e/" . $id, $options = array(
        'query' => array(
          'autoplay' => 1,
        )
    ));
}
//check if url = SoundCloud
else if (preg_match('/soundcloud.com/i', $orig_url)) {
    $videotype = "soundcloud";
    //Get the JSON data of song details with embed code from SoundCloud oEmbed
    $url = $orig_url;
    
	//$data = file_get_contents("http://soundcloud.com/oembed?format=js&url=$url&iframe=true");
    //$data = substr($data, 1, -2);
    //$data = json_decode($data);
	
	//get Image
    //$background = $data->thumbnail_url;
	
	//dsm($data);
	//207961075

    $unparsed_json = file_get_contents('https://api.soundcloud.com/resolve.json?url='.$url.'&client_id=ab294333cd285593ae359bd75745387b');
    $json_object = json_decode($unparsed_json);

	// get Soundcloud ID
    $id = $json_object->{'id'};

	//get Title
    $videotitle = $json_object->{'title'};
	
	//get Image
	$background = $json_object->{'artwork_url'};
	//https://i1.sndcdn.com/artworks-000118567770-k1955k-large.jpg
	
	$pattern = '/(.*)........./i';
    preg_match($pattern, $background, $matches);
    if (count($matches)) {
      $background = $matches[1]."t500x500.jpg";
    }

    //get Duration (no duration object in previous json string, we have to use diferent API call)
	$videoduration = $json_object->{'duration'};
       //duration is displayed on miliseconds. Lets convert it to hh:mm:ss
       $videoduration = intval(intval($videoduration) / 1000);
       $hms = "";
       $hours = intval(intval($videoduration) / 3600);
       $hms .= ($hours)
          ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':'
          : $hours . ':';
       $minutes = intval(($videoduration / 60) % 60);
       $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
       $seconds = intval($videoduration % 60);
       $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
       $videoduration = $hms;
	   
	   $data_url = url("https://w.soundcloud.com/player/", $options = array(
			'query' => array(
			'url' => "https://api.soundcloud.com/tracks/" . $id,
			'auto_play' => 'true',
			)
       ));
}
//check if url = Imgur.com
else if (preg_match('/imgur.com/i', $orig_url)) {
	if (preg_match('/.com\/(\w+)/i', $orig_url, $matches)) { //converts back to original imgur link, get ID
		$id = $matches[1];
		/*
		$orig_url = 'https://imgur.com/'.$id;

		$url = $orig_url;

		$options = array(
			'http'=>array(
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n" .
				"Cookie: foo=bar\r\n" .
				"User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36\r\n" 
			)
		);

		$context = stream_context_create($options);
		$data = file_get_contents($url, false, $context);
		
		if (preg_match('/<sourc.*?src="(\/\/i.imgur.com\/(\w+).(webm|mp4))"/i', $data, $matches)) { //webm video or mp4 video
			$videotype = "rawVideoClick";
			$data_url_webm = 'http://i.imgur.com/'.$matches[2].'.webm';
			$data_url_mp4 = 'http://i.imgur.com/'.$matches[2].'.mp4';

			if (preg_match('/twitt.*?content="(htt.*?)"/i', $data, $matches)) {
				$background = $matches[1];
			}
		}
		else if (preg_match('/<img.*?src="(\/\/i.imgur.*?)"/i', $data, $matches)) { //image
			$videotype = "embedImage";
			$data_url = 'http:' . $matches[1];
		}
		else $videotype = "none";
		*/
		$videotype = "none";

		$client_id="5a6b7c37e5e9010";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image/'.$id);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . $client_id ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$reply = curl_exec($ch);
		curl_close($ch);
		
		$reply = json_decode($reply);
		
		$orig_url = 'https://imgur.com/'.$reply->data->id;
		
		if (($reply->data->type == "video/mp4" || $reply->data->type == "image/gif") && $reply->data->animated) {
				$videotype = "rawVideoClick";
				
				if (preg_match('/.com\/(\w+)/i', $reply->data->gifv, $matches)) { 
					$id = $matches[1];
				}
				
				$data_url_webm = 'http://i.imgur.com/'.$id.'.webm';
				$data_url_mp4 = 'http://i.imgur.com/'.$id.'.mp4';
				$background =  'http://i.imgur.com/'.$id.'l.jpg';
		}
		else {
				$videotype = "embedImage";
				$data_url = $reply->data->link;
		}
	}
	else $videotype = "none";
}
//check if url = giphy.com
else if (preg_match('/giphy.com/i', $orig_url)) {
	if (preg_match('/giphy.com.*?([a-zA-Z0-9]+)(\/giphy)?(.gif)?$/i', $orig_url, $matches)) {
		$videotype = "rawVideoClick";
		$id = $matches[1];
		
		$data_url_mp4 = 'https://media.giphy.com/media/' . $id . '/giphy.mp4';
		$data_url_webm = 'https://media.giphy.com/media/' . $id . '/giphy.mp4';
		$background = 'https://media.giphy.com/media/' . $id . '/giphy_s.gif';
		$orig_url = 'https://giphy.com/gifs/' . $id;
		
		$headers = get_headers_c($data_url_webm);
		$size = getSize($headers);
	}
	else {
		$videotype = "none";
	}
}

//check if url = tinypic.com
else if (preg_match('/tinypic.com/i', $orig_url)) {
	$videotype = "none";
	
	if (preg_match('/tinypic.com.*?(view.php.pic=)?([a-zA-Z0-9]+)/i', $orig_url, $matches)) {
		$id = $matches[2]; //but we arent using it just yet

		$data_url = htmlspecialchars_decode($orig_url);
		$img = file_get_contents($data_url);
	
		$client_id="5a6b7c37e5e9010";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . $client_id ));
		curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($img) ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$reply = curl_exec($ch);
		curl_close($ch);
		$reply = json_decode($reply);

		$data_url = $reply->data->link;
		
		if (preg_match('/.com\/(\w+)/i', $data_url, $matches)) { //converts back to original imgur link, get ID
			$id = $matches[1];
			$data_url = 'https://imgur.com/'.$id;

			$url = $data_url;

			$options = array(
				'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n" .
					"User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36\r\n" 
				)
			);

			$context = stream_context_create($options);
			$data = file_get_contents($url, false, $context);
		
			if (preg_match('/<sourc.*?src="(\/\/i.imgur.com\/(\w+).(webm|mp4))"/i', $data, $matches)) { //webm video or mp4 video
				$videotype = "rawVideoClick";
				$data_url_webm = 'http://i.imgur.com/'.$matches[2].'.webm';
				$data_url_mp4 = 'http://i.imgur.com/'.$matches[2].'.mp4';

				if (preg_match('/twitt.*?content="(htt.*?)"/i', $data, $matches)) {
					$background = $matches[1];
				}
			}
			else if (preg_match('/<img.*?src="(\/\/i.imgur.*?)"/i', $data, $matches)) { //image
				$videotype = "embedImage";
				$data_url = 'http:' . $matches[1];
			}
		}
	}
}

//check if url = raw image,
//or google user content image
//or steam usercontent
else if ( isImage($orig_headers) || isImage2($orig_url) || preg_match('/^.*\.(jpg|jpeg|gif|png|bmp)$/i', $orig_url) || preg_match('/http.*?googleusercontent.*?=w.*/i', $orig_url) || preg_match('/http.*?steamusercontent.com.*?/i', $orig_url)) {
	$data_url = $orig_url;
	$headers = get_headers_c($orig_url, 'http://www.rewired.hu/');
	
	$size = getSize($orig_headers);

	//handle 200
	if (preg_match('/(200 OK)/i', $headers)) {
		$videotype = "rawImage";
		//echo "200 ok uzenet";

		if ( isGif($orig_headers) || isGif2($data_url) ) {
			$getValue = setGifStatus($orig_url, $videotype);
			$background = $getValue[0];
			$videotype = $getValue[1];
		}
	}
	//handle 301
	else if (preg_match('/(301 Moved Permanently)/i', $headers)) {
		//echo "301 uzenet";

		preg_match('/Location..([^"]+)/i', $headers, $matches);
		if (count($matches)) { 
			//var_dump($matches);
			$data_url = $matches[1];
			$videotype = "rawImage";
		}
		
		if ( isGif($orig_headers) || isGif2($data_url) ) {
			$getValue = setGifStatus($orig_url, $videotype);
			$background = $getValue[0];
			$videotype = $getValue[1];
		}
	}
	//handle 302
	else if (preg_match('/(302 Moved Temporarily)|(302 Found)/i', $headers)) {
		//echo "*302*";
		$videotype = "rawImageHotlinkNOTOK";
		
		preg_match('/Location..([^"]+)/i', $headers, $matches);
		if (count($matches)) { //found location string
			//var_dump($matches);
			if (preg_match('/imgur.com/i', $matches[1])) { //if imgur 302
				$videotype = "rawImage";

				if ( isGif($orig_headers) || isGif2($data_url) ) {
					$getValue = setGifStatus($orig_url, $videotype);
					$background = $getValue[0];
					$videotype = $getValue[1];
				}
			}
		}
	}
	//handle anything else
	else if (!preg_match('/(200 OK)/i', $headers)) {
		//echo "nemOK uzenet";
		
		$videotype = "rawImageHotlinkOK";
		$image_info = getimagesize($orig_url);
		$img_width = $image_info[0];
		$img_height = $image_info[1];
		
		$anonymizer = 'https://href.li/?';
		
		// https://href.li/?
		// https://anon.click/
		// https://www.anonymizer.info/?
		// https://referer.us/?
		
		$fixed_url = $anonymizer.$orig_url;

		if ( isGif($orig_headers) || isGif2($data_url) ) {
			$getValue = setGifStatus($orig_url, $videotype);
			$background = $getValue[0];
			$videotype = $getValue[1];
		}
		
		if ( $image_info == false || !$image_info) {
			$videotype = "rawImageHotlinkNOTOK"; //failsafe
		}
	} 
}
//check if url = 9gag
else if (preg_match('/9gag.com|9gag-fun/i', $orig_url)) {
	
	preg_match('/(gag|photo)\/(\w{7})/i', $orig_url, $matches);
	if (count($matches)) {
		$id = $matches[2];
	}
	
	$found = false;
	
	if (!$found) {
		$headers = get_headers_c("https://img-9gag-fun.9cache.com/photo/" . $id . "_460sv.mp4");
		if (preg_match('/HTTP\/1.1 200 OK/', $headers))
		{
			$videotype = "rawVideoClick";
			$data_url_mp4 = "https://img-9gag-fun.9cache.com/photo/" . $id . "_460sv.mp4";
			$data_url_webm = "https://img-9gag-fun.9cache.com/photo/" . $id . "_460svwm.webm";
			$background = "https://img-9gag-fun.9cache.com/photo/" . $id . "_700b.jpg";
			$size = getSize($headers);
			$found = true;
		}
	}
	
	if (!$found) {
		$headers = get_headers_c("https://img-9gag-fun.9cache.com/photo/" . $id . "_700b_v2.jpg");
		if (preg_match('/HTTP\/1.1 200 OK/', $headers)) {
			$videotype = "embedImage";
			$data_url = "https://img-9gag-fun.9cache.com/photo/" . $id . "_700b_v2.jpg";
			$found = true;
		}
	}
	
	if (!$found) {
		$headers = get_headers_c("https://img-9gag-fun.9cache.com/photo/" . $id . "_700b.jpg");
		if (preg_match('/HTTP\/1.1 200 OK/', $headers)) {
			$videotype = "embedImage";
			$data_url = "https://img-9gag-fun.9cache.com/photo/" . $id . "_700b.jpg";
			$found = true;
		}
	}
	
	if (!$found) {
		$videotype = "none";
	}
	
	/*
	preg_match('/9gag-fun.*\/(.*)_.*mp4/i', $orig_url, $matches);
	if (count($matches)) {
		$url = "https://9gag.com/gag/" . $matches[1];
		$orig_url = $url;
	}
	else {
		$url = $orig_url;
	}
	
	$options = array(
		'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en\r\n" .
			"Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
			"User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36\r\n" // i.e. An iPad 
		)
	);

	$context = stream_context_create($options);
	$data = file_get_contents($url, false, $context);
	
	//$test = htmlentities($data);
	//var_dump($test);
	//return;
	
	$pattern = '/data-mp4="(http.*?)"/i';
	preg_match($pattern, $data, $matches);
	if (count($matches)) {  //mp4 video
		$videotype = "rawVideoClick";
		$data_url_mp4 = $matches[1];
		
		$headers = get_headers_c($matches[1]);
		$size = getSize($headers);
		
		if (preg_match('/twitt.*?content="(htt.*?)"/i', $data, $matches)) {
			$background = $matches[1];
		}
	}
	else $videotype = "none";
	
	$pattern = '/data-webm="(http.*?)"/i';
	preg_match($pattern, $data, $matches);
	if (count($matches)) {  //webm video
		$videotype = "rawVideoClick";
		$data_url_webm = $matches[1];
		
		$headers = get_headers_c($matches[1]);
		$size = getSize($headers);
		
		if (preg_match('/twitt.*?content="(htt.*?)"/i', $data, $matches)) {
			$background = $matches[1];
		}
	else $videotype = "none";
	}
	
	if ($videotype != "rawVideoClick") {
		$pattern = '/<img.*?badge-item-img.*?src="(http.*?)"/i';
		preg_match($pattern, $data, $matches);
		if (count($matches)) {  //image
			$videotype = "embedImage";
			$data_url = $matches[1];
		}
		else $videotype = "none";
	}
	*/
}
//check if url = Tumblr.com
else if (preg_match('/tumblr.com/i', $orig_url)) {
	$orig_url = $orig_url;
	$url = $orig_url;

	$options = array(
		'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en\r\n" .
			"Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
			"User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
		)
	);
	
	$context = stream_context_create($options);
	$data = file_get_contents($url, false, $context);
	
	//$test = htmlentities($data);
	//var_dump($test);
	//return;

	$pattern = '/<ifram.*?(htt.*?)..style/i';
	preg_match($pattern, $data, $matches);
	if (count($matches)) { //if mp4 video
			$videotype = "rawVideoClick";
			$data2 = file_get_contents($matches[1], false, $context);
			$pattern = '/<sourc.*?(http.*?)"/i'; 
			preg_match($pattern, $data2, $matches);
			if (count($matches)) { 
				$data_url_mp4 = $matches[1].'.mp4';
				
				$headers = get_headers_c($data_url_mp4);
				$size = getSize($headers);

				if (preg_match('/og:image".content="(http.*?frame1.jpg)"/i', $data, $matches)) {
					$background = $matches[1];
				}
			}
			else $videotype = "none";
	}
	else { //picture
		$videotype = "embedImage";

		if ( preg_match('/<img.*?data-src=".*(http.*?media.tumblr.*?tumblr_.*?)"/i', $data, $matches) || preg_match('/<img.*?src=".*(http.*?media.tumblr.*?tumblr_.*?)"/i', $data, $matches) ) {
			$data_url = $matches[1];
			
			if ( isGif(get_headers($data_url, 1)) || isGif2($data_url)) {
				$getValue = setGifStatus($orig_url, $videotype);
				$background = $getValue[0];
				$videotype = $getValue[1];
			}
		}
		else $videotype = "none";
	}
}
//check if url = instagram.com
else if (preg_match('/instagram.com\/p/i', $orig_url)) {
	$pattern = '/(instagram.com\/p\/.*)\//i';
    preg_match($pattern, $orig_url, $matches);
    if (count($matches)) { //get json data from the link
       $data = file_get_contents('https://api.instagram.com/oembed/?url=http://'.$matches[1]);
    }
	
	if (preg_match('/video.posted.by/i', $data)) { //check if json data contains video string
		$videotype = "rawVideoClick";
		$data2 = file_get_contents('https://www.'.$matches[1].'/embed/');
		
		$pattern = '/(http*.*mp4)/i';
		preg_match($pattern, $data2, $matches);
		
		if (count($matches)) { //get json data from the link
		    $data_url_mp4 = stripslashes($matches[1]);
			
			$headers = get_headers_c($data_url_mp4);
			$size = getSize($headers);
			
			if (preg_match('/background-ima.*?url..(htt.*?)\'/i', $data2, $matches)) {
				$background = $matches[1];
			}
		}
		else $videotype = "none";
	}
	else {
		$videotype = "embedImage";
		$data = json_decode($data);
		$data_url = $data->thumbnail_url;
		
		if ( isGif(get_headers($data_url, 1)) || isGif2($data_url) ) {
			$getValue = setGifStatus($orig_url, $videotype);
			$background = $getValue[0];
			$videotype = $getValue[1];
		}
	}
}
//check if url = Vine.co
else if (preg_match('/vine.co/i', $orig_url)) {

	$pattern = '/(vine.co\/v\/.{11})/i';
    preg_match($pattern, $orig_url, $matches);
    if (count($matches)) {
       $data = file_get_contents('https://'.$matches[1].'/embed/simple');
    }
	else $videotype = "none";

	preg_match('/"post":.(.*)/i', $data, $matches);
	if (count($matches)) { 
		$videotype = "rawVideoClick";

		$data = stripslashes($matches[1]);
		$data = substr($data, 0, -1);
		$data = json_decode($data);
		$data_url_webm = $data->videoUrls[0]->videoUrl;
		$data_url_mp4 = $data->videoUrls[0]->videoUrl;
		$background = $data->thumbnailUrl;
	
		$headers = get_headers_c($data_url_mp4);
		$size = getSize($headers);
	}
	else $videotype = "none";
}
//check if url = Twitter.com
else if (preg_match('/twitter.com/i', $orig_url)) {

/*	
	$data = file_get_contents($orig_url);
	
	$test = htmlspecialchars_decode($data);
	$test = htmlentities($data);
	var_dump($test);

	$pattern = '/status\/(\d+)/i';
    preg_match($pattern, $orig_url, $matches);

    if (count($matches)) {
       $data = file_get_contents('https://twitter.com/i/videos/tweet/'.$matches[1].'?embed_source=clientlib&player_id=0&rpc_init=1');
    }
	else $videotype = "none";

	preg_match('/data-config="(.*)/i', $data, $matches); //check if video
	if (count($matches)) {
		$videotype = "rawVideoClick";
		
		$data = $matches[1];
		$data = substr($data, 0, -3);
		$data = stripslashes($data);
		$data = htmlspecialchars_decode($data); //get rid of &quot
		$data = str_replace(':null', ':"0"', $data);
		$data = str_replace(':false', ':"false"', $data);
		$data = str_replace(':true', ':"true"', $data);
		$data = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $data); //remove more than 1 character whitespace

		preg_match('/"source":"(.*?).."in_reply_to_status_id"/i', $data, $matches); //json validation bugfix
		if (count($matches)) { 
			$data = str_replace($matches[1], '0', $data);
		}
		
		preg_match('/"text":"(.*?).."entities"/i', $data, $matches); //json validation bugfix
		if (count($matches)) { 
			$data = str_replace($matches[1], '0', $data);
		}
		
		$data = json_decode($data);

		//echo "<pre>";
		//var_dump($data); 
		//echo "</pre>";
		
		$data_url_webm = $data->video_url;
		$data_url_mp4 = $data->video_url;
		$background = $data->image_src;
		
		$headers = get_headers_c($data_url_mp4);
		$size = getSize($headers);
	}
	else {
		$videotype = "embedImage";
		
		$data = file_get_contents($orig_url, NULL, NULL, -1, 110000);
		$data = htmlentities($data);
		$data = htmlspecialchars_decode($data); //get rid of &quot
		$data = strstr($data, 'AdaptiveMedia-singlePhoto'); //delete everything before important parts 
		$data = substr($data, 0, strpos($data, "</div>")); //delete everything after important parts 
		
		//$data = htmlentities($data);
		//echo "<pre>";
		//var_dump($data); 
		//echo "</pre>";
		//return;
	
		$data = preg_replace(array('/\s{1,}/', '/[\t\n]/'), '', $data); //remove any whitespace
		preg_match('/imgdata-aria-label-partsrc="(.*).alt/i', $data, $matches); 
	
		if (count($matches)) {
			$data_url = $matches[1];
	
			if ( isGif(get_headers($data_url, 1)) || isGif2($data_url)) {
				$background = makeGifThumbnail($data_url);
				if ($background) $videotype = "rawgif";
			}
		}
		else $videotype = "none";
	}
*/
	preg_match('/https?:\/\/(mobile)?.?((twitter.com.*)\?|twitter.com.*)/i',  $orig_url, $matches);
	$matches = end($matches);
	$orig_url = 'https://' . $matches;
	
	$data = get_data_curl('https://mobile.' . $matches . '/video/1', 'https://mobile.' . $matches ); //test if video
	
	if(strlen($data) != 0) { //its a video
		$videotype = "rawVideoClick";
		preg_match('/<source.*?src="(.*\.mp4)".type="video/i',  $data, $matches); 
		
		if (count($matches)) {
			$data_url_webm = $matches[1];
			$data_url_mp4 = $matches[1];

			preg_match('/poster="(.*?)".src/i',  $data, $matches); //get background
			$background = $matches[1];
		
			$headers = get_headers_c($data_url_mp4);
			$size = getSize($headers);
		}
		else $videotype = "none"; //failsafe
	} 
	else { 
		$data = get_data_curl('https://mobile.' . $matches . '/photo/1', 'https://mobile.' . $matches ); //test if picture
		
		if(strlen($data) != 0) { //its a picture
			$videotype = "embedImage";
			preg_match('/<img.*?src="(.*?)".>/i',  $data, $matches); 
			
			if (count($matches)) {
				$data_url = $matches[1];

				if (isGif(get_headers($data_url, 1)) || isGif2($data_url)) {
					$getValue = setGifStatus($orig_url, $videotype);
					$background = $getValue[0];
					$videotype = $getValue[1];
				}
			}
			else $videotype = "none"; //failsafe

		}
		else { //neither a video or picture
			 $videotype = "none";
		}
	}	
}
//check if url = Facebook.com
else if (preg_match('/facebook.com/i', $orig_url)) {
	$url = $orig_url;

	set_time_limit(120); //in seconds, max time before throwing an error
	
	$options = array(
		'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en\r\n" .
			"Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
			"User-Agent: Mozilla/5.0 (Linux; Android 4.4.4; Nexus 7 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.111 Safari/537.36\r\n"
		)
	);
	
	$context = stream_context_create($options);

	$data = file_get_contents($url, false, $context);
	
	//$test = htmlspecialchars_decode($data);
	//$test = htmlentities($data);
	//var_dump($test);
	//return;
	
	if (preg_match('/www.facebook.com.*?photo/i', $orig_url)) { //facebook picture
		$videotype = "embedImage";
		$pattern = '/href="(https?:\/\/scontent.*?)">view full size/i';

		//(?<=LOOKBEHIND)(LOOKFORWHAT(?=LOOKAFTER))
		preg_match($pattern, $data, $matches);
		if (count($matches)) { 
			$url = $matches[1];
		    $url = htmlspecialchars_decode($url);
		}
		else $videotype = "none";
		
		$img = file_get_contents($url);
	
		$client_id="5a6b7c37e5e9010";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . $client_id ));
		curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($img) ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$reply = curl_exec($ch);
		curl_close($ch);
		$reply = json_decode($reply);

		$data_url = $reply->data->link;
		
		if ( isGif(get_headers($data_url, 1)) || isGif2($data_url)) {
			$getValue = setGifStatus($orig_url, $videotype);
			$background = $getValue[0];
			$videotype = $getValue[1];
		}
		
		//var_dump($reply);
	}
	else { //facebook video
	
		//echo "<i>//facebook video beagyazast kikapcsoltam, mivel a legjobb esetben is szivbajosan mukodott csak.</i>". PHP_EOL;
		//echo "-> ". $orig_url;
		//return;
		
		/*
		$videotype = "rawVideoClick";

		$orig_url = 'https://video-sea1-1.xx.fbcdn.net/v/t42.1790-2/13634532_1820868668133431_817126651_n.mp4?efg=eyJybHIiOjU4MywicmxhIjo1MTIsInZlbmNvZGVfdGFnIjoidjNfNDI2X2NyZl8yM19tYWluXzMuMF9zZCJ9&rl=583&vabr=324&oh=4345acc3aa639046d55a8d619d518cd0&oe=577FDE27';
		
		$encoded_url = rawurlencode($orig_url);
		$data = file_get_contents("https://api.streamable.com/import?url=$encoded_url");
		$data = json_decode($data);
		$id = $data->shortcode;	
		
		if(isset($id)) {
			$videotype = "rawVideoClick";
			$data_url_mp4 = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
			$data_url_webm = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
			$background = 'https://cdn.streamable.com/image/' . $id . '_first.jpg';

			$headers = get_headers_c($data_url_webm);
			$size = getSize($headers);
		}
		else {
			$videotype = "none";	
		}
		*/
		
		/*
		$videotype = "rawVideoClick";

		$encoded_url = rawurlencode($orig_url);
		$data = file_get_contents("https://upload.gfycat.com/transcode?fetchUrl=$encoded_url");
		$data = json_decode($data);
		$id = $data->gfyname;	
		
		if(isset($id)) {
			$videotype = "rawVideoClick";
			$data_url_mp4 = $data->mp4Url;		
			$data_url_webm = $data->webmUrl;	
			$background = 'https://thumbs.gfycat.com/' . $id . '-poster.jpg';

			$headers = get_headers_c($data_url_webm);
			$size = getSize($headers);
		}
		else {
			$videotype = "none";	
		}
		*/
		//----------------------------------------------------------------------
		//echo htmlspecialchars($data);
		//var_dump($data);
		
		
		//$test = htmlspecialchars_decode($data);
		//$test = htmlentities($data);
		//var_dump($test);
		//return;
		
		$videotype = "none"; //failsafe
		
		if (preg_match('/&quot;hd_src&quot;:&quot;(https.*?)&quot;|&quot;sd_src&quot;:&quot;(https.*?)&quot;|playOnClick.*?(https.*?oe=[^\&]{8})|&quot;video&quot;,&quot;src&quot;:&quot;(https.*?oe=[^\&]{8})/i', $data, $matches)) {

			/*
			if (preg_match('/&quot;hd_src&quot;:&quot;https..(.*?)&quot;|"sd_src":"https..(.*?)"|
						playOnClick.*?https..(.*?oe=[^\&]{8})|
						(video-sea.*?oe=[^\&]{8})|
						&quot;video&quot;,&quot;src&quot;:&quot;(https.*?oe=[^\&]{8})(https:\/\/.*?oe=[^\&]{8})|
						(video.xx.fbcdn.net.*?oe=[^\&]{8})/i', $data, $matches)) {
			*/
			//echo "one";

			$matches = end($matches);
			$url = $matches;
			$url = html_entity_decode($url); //remove "\u00253D" from url
			$url = stripslashes($url); //remove slashes from url
			//$url = 'https://' . $url;
			
			$headers = get_headers_c($url);
			$size = getSize($headers);

			$encoded_url = rawurlencode($url);
			$data = file_get_contents("https://api.streamable.com/import?url=$encoded_url");
			$data = json_decode($data);
			$id = $data->shortcode;	
			
			//$id = "aukc";
		
			if(isset($id)) {
				//echo "two";
			
				$start = microtime(true);
				for ($i = 0; $i < 30; ++$i) { //in seconds

					$data = file_get_contents("https://api.streamable.com/videos/$id");
					$data = json_decode($data);
					if ($data->status == 2) {
						$videotype = "rawVideoClick";
		
						$data_url_mp4 = "https:" . $data->files->mp4->url;
						$data_url_webm = "https:" . $data->files->mp4->url;
						$background = "https:" . $data->thumbnail_url;
				
						/*
						$data_url_mp4 = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
						$data_url_webm = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
						$background = 'https://cdn.streamable.com/image/' . $id . '_first.jpg';
						*/

						break;
					}

					time_sleep_until($start + $i + 1);
				}
			}
		}

		/*
		if (preg_match('/("hd_src":"https.*?")/i', $data, $matches)) {
			$data_url = $matches[1];
			$data_url = '{'.$data_url.'}';
			$data_url = json_decode($data_url);
			$data_url_mp4 = $data_url->hd_src; 
			
			if (preg_match('/background..url.......http.*?url=(.*?)......\)/i', $data, $matches)) {
				$background = $matches[1];
				$background = rawurldecode($background);
			}
			echo "one";
		}
		else if (preg_match('/("sd_src":"https.*?")/i', $data, $matches)) {
			$data_url = $matches[1];
			$data_url = '{'.$data_url.'}';
			$data_url = json_decode($data_url);
			$data_url_mp4 = $data_url->sd_src; 
			
			if ($background != $orig_background && preg_match('/background..url.......http.*?url=(.*?)......\)/i', $data, $matches)) {
				$background = $matches[1];
				$background = rawurldecode($background);
			}
			
			echo "two";
		}
		else if (preg_match('/playOnClick.*?(http.*?oe=[^\&]{8})/i', $data, $matches)) {
			$data_url = $matches[1];
			$data_url = '{'.'"nd_src":"'.$data_url.'"}';
			$data_url = json_decode($data_url);
			$data_url_mp4 = $data_url->nd_src; 
		
			if ($background != $orig_background && preg_match('/background..url.......http.*?url=(.*?)......\)/i', $data, $matches)) {
				$background = $matches[1];
				$background = rawurldecode($background);
			}
			
			$data = htmlspecialchars_decode($data);
			var_dump($data);
			
			echo "three";
		}
		else $videotype = "none";
		*/
	}
}
//check if url = gfycat.com
else if (preg_match('/gfycat.com/i', $orig_url)) {
	preg_match('/gfycat.com\/(search\/[^\/]+\/(detail\/(\w+)|\w+)|detail\/(\w+)|\w+)/i', $orig_url, $matches);
	if (count($matches)) {
		$videotype = "rawVideoClick";
		
		$matches = end($matches);
		$id = $matches;
		//$orig_url = "https://gfycat.com/$id";

		$data = file_get_contents("https://gfycat.com/cajax/get/$id");
		$data = json_decode($data);
		
		$data_url_mp4 = $data->gfyItem->mp4Url;		
		$data_url_webm = $data->gfyItem->webmUrl;	
		$background = $data->gfyItem->posterUrl;
		//$background = 'https://thumbs.gfycat.com/' . $id . '-poster.jpg';
		
		$headers = get_headers_c($data_url_webm);
		$size = getSize($headers);
	}
	else {
		$videotype = "none";
	}
}
else if (preg_match('/webm.land/i', $orig_url)) {
	preg_match('/(w\/|media\/)(.{4})/i', $orig_url, $matches);
	if (count($matches)) {
		$videotype = "rawVideoClick";
		
		$matches = end($matches);
		$id = $matches;

		$data_url_webm = "http://webm.land/media/$id.webm";
		$data_url_mp4 = "http://webm.land/media/$id.webm";
		$background = "http://webm.land/media/thumbnails/$id.jpeg";
		$orig_url = "http://webm.land/w/$id/";
		
		$headers = get_headers_c($data_url_webm);
		$size = getSize($headers);
	}
	else {
		$videotype = "none";
	}
}
else if (preg_match('/streamable.com/i', $orig_url)) {
	preg_match('/.com\/(\w*)($|.mp4)/i', $orig_url, $matches);
	if (count($matches)) {
		$videotype = "rawVideoClick";
		
		//reset($matches);
		//$id = current($matches);
		$id = $matches[1];
		
		$data = file_get_contents("https://api.streamable.com/videos/$id");
		$data = json_decode($data);
		
		$data_url_mp4 = "https:" . $data->files->mp4->url;
		$data_url_webm = "https:" . $data->files->mp4->url;
		$background = "https:" . $data->thumbnail_url;

	
		/*
		$data_url_mp4 = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
		$data_url_webm = 'https://cdn.streamable.com/video/mp4/' . $id . '.mp4';
		$background = 'https://cdn.streamable.com/image/' . $id . '.jpg'; //. '_first.jpg';
		*/
		
		$orig_url = 'https://streamable.com/' . $id;
		
		
		$headers = get_headers_c($data_url_webm);
		$size = getSize($headers);
	}
	else {
		$videotype = "none";
	}
}
//check if url = coub.com
else if (preg_match('/coub.com/i', $orig_url)) {
	
    if (preg_match('/coub.com\/view\/(\w+)\/?/i', $orig_url, $matches)) {
		$videotype = "rawVideoClick";
	
		$data = file_get_contents('http://coub.com/api/v2/coubs/'.$matches[1]);
		$data = json_decode($data);
		$data_url_webm = $data->file;
		$data_url_mp4 = $data->file;
		$background = $data->picture;
    }
	else $videotype = "none";
}

//check if url = .webm/.mp4 extension
else if (preg_match('/(^.*\.webm|^.*\.mp4)/i', $orig_url)) { 

	$size = getSize($orig_headers);
	$data_url_webm = $orig_url;
	$data_url_mp4 = $orig_url;
	$videotype = "rawVideoClick";
	
	$headers = get_headers_c($orig_url, 'http://www.rewired.hu/');

	//handle 200
	if (preg_match('/(200 OK)/i', $headers)) {
		$data_url_webm = $orig_url;
		$data_url_mp4 = $orig_url;
		$videotype = "rawVideoClick";
	}
	//handle 301
	else if (preg_match('/(301 Moved Permanently)/i', $headers)) {
		preg_match('/Location..([^"]+)/i', $headers, $matches);
		if (count($matches)) { 
			$data_url_webm = $matches[1];
			$data_url_mp4 = $matches[1];
			$videotype = "rawVideoClick";
		}
	}
	//handle 302
	else if (preg_match('/(302 Moved Temporarily)|(302 Found)/i', $headers)) {
		$videotype = "rawImageHotlinkNOTOK";
	}
	//handle anything else
	else if (!preg_match('/(200 OK)/i', $headers)) {
		$videotype = "rawVideoClickHotlinkOK";
		
		//$image_info = getimagesize($orig_url);
		//$img_width = $image_info[0];
		//$img_height = $image_info[1];
		
		$anonymizer = 'https://href.li/?';
		
		// https://href.li/?
		// https://anon.click/
		// https://www.anonymizer.info/?
		// https://referer.us/?
		
		$fixed_url = $anonymizer.$orig_url;
	} 
}
//format not recognised.
else {
	$videotype = "none";
}

if (isset($reference_url)) {
	$orig_url = $reference_url;
}

$valuesArray = array(
	'videotype' => isset($videotype) ? $videotype : '',
	'data_url' => isset($data_url) ? $data_url : $orig_url,
	'data_url_webm' => isset($data_url_webm) ? $data_url_webm : 'about:blank',
	'data_url_mp4' => isset($data_url_mp4) ? $data_url_mp4 : 'about:blank',
	'background' => isset($background) ? $background : '',
	'videotitle' => isset($videotitle) ? $videotitle : '',
	'videoduration' => isset($videoduration) ? $videoduration : '',
	'orig_url' => isset($orig_url) ? $orig_url : '',
	'fixed_url' => isset($fixed_url) ? $fixed_url : '',
	'size' => isset($size) ? $size : '',
	'img_width' => isset($img_width) ? $img_width : '',
	'img_height' => isset($img_height) ? $img_height : '',
);


return $valuesArray;
}

function rwMakeMetaContent($node, $nid) {
	
	//dsm($node);

	global $base_url; //our url
	$default_background =  $base_url."/sites/all/files/rewired_og.jpg";
	
	$body = field_get_items('node', $node, 'body');  //load content, Forum Topic body only
	
	if ($body == null) {
		$body1 = field_get_items('node', $node, 'field_pollbody3');  //Load Voting top field
		$body2 = field_get_items('node', $node, 'field_pollbody4');  //Load Voting bottom field
		
		$field_language1 = field_language('node', $node, 'field_pollbody3'); //Voting top body field's language, if any
		$field_language2 = field_language('node', $node, 'field_pollbody4'); //Voting bottom body field's language, if any
	
		
		if ($body1 != null && isset($node->field_pollbody3[$field_language1][0]['value'])) {
			$string1 = $node->field_pollbody3[$field_language1][0]['value']; //load Voting top field's body content
		} else $string1 = "";
		
		if ($body2 != null && isset($node->field_pollbody4[$field_language2][0]['value'])) {
			$string2 = $node->field_pollbody4[$field_language2][0]['value']; //load Voting bottom field's body content
		} else $string2 = "";
		
		$string = $string1 . $string2;
		
		//dsm($string);
	}
	else {
		$field_language = field_language('node', $node, 'body'); //load content, body field's language, if any
		$string = $node->body[$field_language][0]['value'];  //load raw value
	}
	
	$orig_url = $string;
	
	$summaryString = strip_tags($string); //strip html tags
	//to escape reserved square brackets in drupal
	//$pattern = "/&#91;a-zA-Z&#93;*&#91;:\/\/&#93;*&#91;A-Za-z0-9\-_&#93;+\.+&#91;A-Za-z0-9\.\/%&=\?\-_&#93;+/i";
	//$pattern = html_entity_decode($pattern); 
	$pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
	$replacement = "";
	$summaryString = preg_replace($pattern, $replacement, $summaryString); //strip links
	$summaryString = str_replace(array("\r","\n"), "", $summaryString); //strip linebreaks
	$summaryString = preg_replace('#\[[^\]]+\]#', '', $summaryString); //strip bbcodes
	$summaryString = html_entity_decode($summaryString);
	//$summaryString = substr($summaryString, 0, 240) . "..."; //strip to 240 characters
	$summaryString = mb_substr($summaryString, 0, 240, 'UTF-8') . "..."; //strip to 240 characters UTF-8 fix
	//utf8_decode($summaryString); 
	
	//find all url, store in array
	$pattern = "/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)&#91;-A-Z0-9+&@#\/%=~_|$?!:,.&#93;*&#91;A-Z0-9+&@#\/%=~_|$&#93;/i";
	$pattern = html_entity_decode($pattern); //to escape reserved square brackets in drupal
	preg_match_all($pattern, $orig_url, $result, PREG_PATTERN_ORDER);
	$result = $result[0];

	for ($i=0; $i < count($result);$i++) {
		$orig_url = $result[$i];
	
		$orig_headers = rwUrlValidator($orig_url);
	
		if ($orig_headers != "invalid") {
			$valuesArray = checkUrlType($orig_url, $orig_headers);
		
			switch($valuesArray['videotype']) {
				case 'youtube':
				case 'vimeo':
				case 'vidme':
				case 'soundcloud':
				case 'rawgif':	
				case 'rawgifHotlinkOK':
					$output_url = $valuesArray['background'];
					break 2;
				case 'rawVideoClick':
				case 'rawVideoClickHotlinkOK':
				case 'none':
					//$output_url = $default_background;
					$output_url = null;
					break;
				case 'rawImage':
				case 'embedImage':
					$output_url = $valuesArray['data_url'];
					break 2;
				case 'rawImageHotlinkOK':
				case 'rawImageHotlinkNOTOK':
					$output_url = $orig_url;
					break 2;
			}
		}
	}
	
	/*
	$filename = "testtesttest";
	$pathname = './sites/default/files/newsimages/';
	$filename = $pathname . $filename . '.txt';
	
	$file = fopen($filename,"w");
	fwrite($file,$writeValue);
	fclose($file);
	*/
	
	/*
	$writeValue = "hahahah";
	$file = fopen('./sites/default/files/newsimages/test.txt',"w");
	fwrite($file,$writeValue);
	fclose($file);
	*/
	
	//dsm($pager_total);
	
	//rwUpdateMetaFields($nid, $summaryString, $output_url);
	//rwUpdateMetaFields($node, $summaryString, $output_url);
	
	//dsm($summaryString);
	//dsm($output_url);
	
	$valuesArray = array(
	'ogDescription' => isset($summaryString) ? $summaryString : null,
	'ogImage' => isset($output_url) ? $output_url : null,
	);
	
	$valuesObject = (object) $valuesArray;
	return $valuesObject;
}

function rwGetImageFromUrl($orig_url) {
	
	//dsm($node);
	
	global $base_url; //our url
	$default_background =  $base_url."/sites/all/files/rewired_og.jpg";
	
	$orig_headers = rwUrlValidator($orig_url);
	
	if ($orig_headers != "invalid") {
		$valuesArray = checkUrlType($orig_url, $orig_headers);
		
		switch($valuesArray['videotype']) {
			case 'youtube':
			case 'vimeo':
			case 'vidme':
			case 'soundcloud':
			case 'rawgif':	
			case 'rawgifHotlinkOK':
				$output_url = $valuesArray['background'];
			case 'rawVideoClick':
			case 'rawVideoClickHotlinkOK':
			case 'none':
				//$output_url = $default_background;
				$output_url = null;
			case 'rawImage':
			case 'embedImage':
				$output_url = $valuesArray['data_url'];
			case 'rawImageHotlinkOK':
			case 'rawImageHotlinkNOTOK':
				$output_url = $orig_url;
		}
	}
	else $output_url = "invalid";
	
	return $output_url;
}


function rwUpdateMetaFields ($node, $ogDescription, $ogImage) {
	//$node = node_load($nid);
	
	if (isset($ogDescription)) {
		$field_language = field_language('node', $node, 'field_og_description');
		$node->field_og_description[$field_language][0]['value'] = $ogDescription;
	}
	
	if (isset($ogImage)) {
		$field_language = field_language('node', $node, 'field_og_image');
		$node->field_og_image[$field_language][0]['value'] = $ogImage;
	}

	field_attach_update('node', $node);
}

function rwMakeMetaThumbs ($nid, $dateCreated, $pathname, $ogImage) {
	if (!is_null($ogImage)) {
		//delete first
		$filename = "node-$nid";
		$gifThumbnail = $pathname . $filename . '.*';
		array_map( "unlink", glob($gifThumbnail) );

		//create
		$filename = "node-$nid-$dateCreated";
		$gifThumbnail = $pathname . $filename . '.jpg';
		$jpeg_memory = imageCreateFromAny($ogImage);
		imagejpeg($jpeg_memory,$gifThumbnail, "75", FILE_EXISTS_REPLACE);
		imagedestroy($jpeg_memory);
	}
}

function rwLimitFilenumber ($pathname, $maxAllowedFiles) {
	//The glob() function returns an array of filenames or directories matching a specified pattern.
	foreach (glob("$pathname*.*") as $filePath) { 

		//read node created date from the picture filename
		if ( preg_match('/node-\d+-(\d+)/i', $filePath, $matches) ) {
			$nodeWasCreatedOn = $matches[1];
		}
		else $nodeWasCreatedOn = 0;
		
		//setting up the array Key as the "Node created date", and the array Value as the "./sites/default/files/newsimages/node-92-444.jpg".
		//we can sort and delete them later by "date" easily.
		$filesArray[$nodeWasCreatedOn] = $filePath;
	}
			
	//dsm($filesArray);

	if (count($filesArray) > ($maxAllowedFiles + 5) ) {
		//Sort an associative array in descending order, according to the key
		ksort($filesArray);
		
		for ($i=0; $i < (count($filesArray) - $maxAllowedFiles) ;$i++) {
			$filePath2 = array_values($filesArray)[$i];
			array_map( "unlink", glob($filePath2) );
		}
	}
	
	/*
	foreach (glob("$pathname*.*") as $file) {
		$files[filesize($file)] = $file;
	}
				
	if (count($files) > ($maxAllowedFiles + 5) ) {
		krsort($files);
		
		//dsm($files);
		for ($i=0; $i < (count($files) - $maxAllowedFiles) ;$i++) {
			$thumbnail = array_values($files)[$i];
			array_map( "unlink", glob($thumbnail) );
		}
	}
	*/
}

?>