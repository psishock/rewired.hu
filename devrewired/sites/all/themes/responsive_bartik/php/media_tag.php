<?php

function media_tag($tag) {
	global $base_url;
	
	require_once("./sites/all/themes/responsive_bartik/php/xbbcode_functions.php"); //custom functions

	$default_background =  $base_url."/sites/all/files/rewired_og.jpg";
	$orig_url = $tag->content;

	$orig_headers = rwUrlValidator($orig_url);
	if ($orig_headers == "invalid") {
		echo "<i>//Ervé Ágnes: beágyazásra ez nekem túl gyanúsan formázott URL cím. Ennyi ami biztonságosnak fest a megrágása után:". PHP_EOL;
		echo "-> ". $orig_url . "</i>";
		return;
	}
	
	$foundUrl = checkUrlTypeContainer($orig_url, $orig_headers);
	if ($foundUrl != '') {
		$valuesArray = checkUrlType($foundUrl, $orig_headers, $orig_url);
	} 
	else {
		$valuesArray = checkUrlType($orig_url, $orig_headers);
	}
	
	if ($valuesArray['background'] == '') $valuesArray['background'] = $default_background;
	
	media_output($valuesArray);
}

function checkUrlTypeContainer($orig_url) {
	if (preg_match('/www\.reddit\.com/i', $orig_url)) {
		$json = file_get_contents($orig_url . "/.json");	
		$json = json_decode($json, true);
		$jsonUrl = $json[0]['data']['children'][0]['data']['url'];
		
		return $jsonUrl;
	}
}


/* Output ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
  //$playarrow = file_create_url(file_default_scheme()."://") . "playicon.png";

function media_output($valuesArray) {
	
  $playarrow = "/sites/all/files/" . "playicon.png";
  $valuesArray['playarrow'] = $playarrow;

   switch($valuesArray['videotype']) {
    case 'youtube':
	case 'vimeo':
	case 'vidme':
    case 'soundcloud':
	return <<<MEDIA
   <div class="media-wrapper {$valuesArray['videotype']}">
		<div class="media {$valuesArray['videotype']}">
        <img class="mediabackground" src="{$valuesArray['background']}" alt="{$valuesArray['videotitle']}">
		<img class="play-icon {$valuesArray['videotype']}" src="{$valuesArray['playarrow']}" alt="Inditás gomb">
        <h3 class="mediatitle">{$valuesArray['videotitle']} ({$valuesArray['videoduration']})</h3>
        <iframe class="element-invisible" src="about:blank" frameborder="0" allowfullscreen="1" data-url="{$valuesArray['data_url']}"></iframe>
      </div>
    </div>
MEDIA;
    break;
	case 'rawVideoClick':
	echo <<<HTML
	<div class=mediatitle2-wrapper><h3 class="mediatitle2"><a href="{$valuesArray['orig_url']}">{$valuesArray['orig_url']}{$valuesArray['size']}</a></h3></div>
    <div class="media-wrapper {$valuesArray['videotype']}">
      <div class="media {$valuesArray['videotype']}">
        <img class="mediabackground" src="{$valuesArray['background']}" alt="{$valuesArray['orig_url']}"/>
        <img class="play-icon {$valuesArray['videotype']}" src="{$valuesArray['playarrow']}" alt="Inditás gomb"/>
		<video class="element-invisible" controls loop autoplay preload="none" id="webmvideo">
			<source class="mp4source" type="video/mp4" src="about:blank" data-url="{$valuesArray['data_url_mp4']}"></source>
			<source class="webmsource" type="video/webm" src="about:blank" data-url="{$valuesArray['data_url_webm']}"></source>
		</video>
      </div>
    </div>
HTML;
	break;
	case 'rawVideoClickHotlinkOK':
	echo <<<HTML
	<div class=mediatitle2-wrapper><h3 class="mediatitle2"><a href="{$valuesArray['orig_url']}">{$valuesArray['orig_url']}{$valuesArray['size']}</a></h3></div>
    <div class="media-wrapper rawVideoClick">
      <div class="media rawVideoClick">
		
		<img class="mediabackground" src="{$valuesArray['background']}" alt="{$valuesArray['orig_url']}"/>
        <img class="play-icon rawVideoClick" src="{$valuesArray['playarrow']}" alt="Inditás gomb"/>
		
		<video class="element-invisible" controls loop autoplay preload="none" id="webmvideo">
			<source class="mp4source" type="video/mp4" src="about:blank" data-url="{$valuesArray['data_url_mp4']}"></source>
			<source class="webmsource" type="video/webm" src="about:blank" data-url="{$valuesArray['data_url_webm']}"></source>
		</video>
		
		 <iframe class="hotlinkedVideoIframe element-invisible" src="{$valuesArray['fixed_url']}" data_url="{$valuesArray['fixed_url']}" width={$valuesArray['img_width']} height={$valuesArray['img_height']} frameBorder='0' scrolling='no'></iframe>
		
      </div>
    </div>
HTML;
	break;	
	case 'rawImage':
	echo <<<HTML
		<img alt="kép:{$valuesArray['data_url']}" src="{$valuesArray['data_url']}" />
HTML;
    break;
	case 'rawgif':
	echo <<<HTML
		<div class=mediatitle2-wrapper><h3 class="mediatitle2"><a href="{$valuesArray['orig_url']}">{$valuesArray['orig_url']}{$valuesArray['size']}</a></h3></div>
		<div class="media-wrapper {$valuesArray['videotype']}">
			<div class="media {$valuesArray['videotype']}">
				<img class="gifImageContainer" src="{$valuesArray['background']}" alt="{$valuesArray['orig_url']}" data_url="{$valuesArray['data_url']}"/>
				<img class="play-icon {$valuesArray['videotype']}" src="{$valuesArray['playarrow']}" alt="Inditás gomb"/>
			</div>
		</div>
HTML;
	break;
	case 'rawgifHotlinkOK':
	echo <<<HTML
		<div class=mediatitle2-wrapper><h3 class="mediatitle2"><a href="{$valuesArray['orig_url']}">{$valuesArray['orig_url']}{$valuesArray['size']}</a></h3></div>
		<div class="media-wrapper {$valuesArray['videotype']}">
			<div class="media {$valuesArray['videotype']}">
				<img class="gifImageContainer" src="{$valuesArray['background']}" alt="{$valuesArray['orig_url']}" data_url="{$valuesArray['fixed_url']}"/>
				<img class="play-icon {$valuesArray['videotype']}" src="{$valuesArray['playarrow']}" alt="Inditás gomb"/>
				<div class="hotlinkedImageWrapper element-invisible">
					<iframe class="hotlinkedImageIframe element-invisible" src="about:blank" data_url="{$valuesArray['fixed_url']}" width={$valuesArray['img_width']} height={$valuesArray['img_height']} frameBorder='0' scrolling='no'></iframe>
				</div>
				<div class="iframeblocker"></div>
			</div>
		</div>
HTML;
	break;	
	case 'rawImageHotlinkNOTOK':
	echo <<<HTML
		<div><i>// Ervé Ágnes: Nem tudom kijátszani a beágyazás-védelmet vagy az átirányításokat a megadott URL-en! Használd az <a href='http://imgur.com'>imgur.com</a>-ot a kép feltöltéséhez, aztán újra próbálkozhatsz.</i>
		-> <a href={$valuesArray['orig_url']} rel=noreferrer>{$valuesArray['orig_url']}</a>//</i>
		</div>
HTML;
	break;	
	case 'rawImageHotlinkOK':
	echo <<<HTML
		<div class="hotlinkedImageWrapper">
			<iframe class="hotlinkedImageIframe" src={$valuesArray['fixed_url']} width={$valuesArray['img_width']} height={$valuesArray['img_height']} frameBorder='0' scrolling='no'></iframe>
		</div>			
HTML;
    break;	
	case 'embedImage':
	echo <<<HTML
		<div class=mediatitle2-wrapper><h3 class="mediatitle2"><a href="{$valuesArray['orig_url']}">{$valuesArray['orig_url']}</a></h3></div>
		<img alt="kép:{$valuesArray['data_url']}" src="{$valuesArray['data_url']}"/>
HTML;
    break;
	case 'none':
	echo <<<HTML
		<div><i>// Ervé Ágnes: hallod Pityu, ezt a beágyazni valót nem tudom sehogyan sem értelmezni.
		-> {$valuesArray['orig_url']} //</i>
		</div>
HTML;
	break;
   }

}
?>