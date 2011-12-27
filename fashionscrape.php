#!/usr/bin/php
<?php
	/*
	** fashion blog image scraper. 
	** (C) mindwar - mindwar.ro 2010
	*/

	$sitedata = array(
		'fashiongonerogue' => array(
			'titlexpath' => '//div[@id="post-title"]/h2/a',
			'photoxpath' => '//div[@class="copy  clearfix"]//img',
			'url' => 'http://fashiongonerogue.com/',
			'folder' => 'fashiongonerogue'
		),
		'touchpuppet' => array(
			'titlexpath' => '//div[@class="single-post post sfbox"]//h1',
			'photoxpath' => '//div[@id="full-post"]//img',
			'url' => 'http://www.touchpuppet.com/',
			'folder' => 'touchpuppet'
		),
		'fashiontography' => array(
			'titlexpath' => '//h3[@class="post-title entry-title"]/a',
			'photoxpath' => '//div[@class="post-body entry-content"]//img',
			'url' => 'http://fashiontography.blogspot.com/',
			'folder' => 'fashiontography'
		),
		'calikartel' => array(
			'titlexpath' => '//h1[@class="title"]/a',
			'photoxpath' => '//div[@class="content"]//img',
			'url' => 'http://calikartel.com/',
			'folder' => 'calikartel'
		),
		'ZACfashion' => array(
			'titlexpath' => '//h1[@class="entry-title"]',
			'photoxpath' => '//div[@class="entry-content"]//img',
			'url' => 'http://zac-fashion.com/',
			'folder' => 'zac'
		),
		'thephotographylink' => array(
			'titlexpath' => '//div[@id="entries"]//h1//a',
			'photoxpath' => '//div[@class="post"]//img',
			'url' => 'http://www.thephotographylink.com/',
			'folder' => 'thephotolink'
		),
		'fashioneditorials' => array(
			'titlexpath' => '//h1[@class="entry-title"]',
			'photoxpath' => '//div[@id="content"]//img',
			'url' => 'http://www.fashioneditorials.com/',
			'folder' => 'fashioneditorials'
		),
		'noirfacade' => array(
			'titlexpath' => '//div[@style="margin-left: 30px"]/p//b',
			'photoxpath' => '//center//img',
			'url' => 'http://noirfacade.livejournal.com/',
			'folder' => 'noirfacade'
		)		
	);

	echo "fashion blog photo downloader - (c) 2010 mindwar\n\n";
	echo "supported:\n";
	foreach ($sitedata as $urls) {
		echo "- {$urls['url']}\n";
	}
	echo "\n\n";
	
	if ($argc != 2) {
	    echo "USAGE: $argv[0] [url]\n";
	    echo "[url] = direct link to post\n";
	    exit;
	} else {
	    $target_url = $argv[1];
	}

	$thesite = "";
	foreach ($sitedata as $site) {
		if (strlen(strstr($argv[1], $site['url'])) > 0) {
			$thesite = $site;
		}
	}

	$html = new DOMDocument();

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
	curl_setopt($curl, CURLOPT_URL, $target_url);
	$thehtml = curl_exec($curl);

	@$html->loadHTML($thehtml);
	$xml = simplexml_import_dom($html);

	// pt images
	$title = $xml->xpath($thesite['titlexpath']);

	// pt photos
	$photos = $xml->xpath($thesite['photoxpath']);

	$folder_name = $title[0];

	$chars = array('!','@','#','$','%','^','&','*','`','~',',','.',':',';','|','/');

	foreach ($chars as $c) {
		$folder_name = str_replace($c, '', $folder_name);
	}
	$folder_name = trim(str_replace(' ','_', $folder_name));

	$folder = "photos/" . $thesite['folder'] . "/" . $folder_name;

	if(!is_dir($folder)) {
		echo "FOLDER: $folder\n";
		mkdir($folder, 0777, true);
	} else {
		echo "FOLDER: $folder - already exists\n";
	}

	foreach ($photos as $photo) {
		$url  = parse_url($photo['src']);
		$path = explode("/", $url['path']);
		$file = $path[count($path)-1];
		echo "IMG: $folder/$file - ";
		file_put_contents($folder . "/" . $file, file_get_contents($photo['src']));
		echo "DONE\n";
	}
?>
