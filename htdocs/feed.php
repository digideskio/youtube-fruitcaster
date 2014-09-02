<?php

include('Spyc.php');

$yaml = Spyc::YAMLLoad('../config.yaml');
$config = $yaml["feed"];

function compare_items($a, $b) {
	return strnatcmp($b["publishedAt_raw"], $a["publishedAt_raw"]); // Order switched to have reverse sorting
}

function xmlentities($str) {
	$str = str_replace("&", "&amp;", $str);
	$search = array("<", ">", "'", "\"");
	$replace = array("&lt;", "&gt;", "&apos;", "&quot;");
	$str = str_replace($search, $replace, $str);
	return $str;
}

if($handle = opendir('data/meta')){
	while (false !== ($file = readdir($handle))) {
        if(strstr($file, 'json')){
        	$metafiles[] = json_decode(file_get_contents('data/meta/'.$file), 1);
        }
    }
}

$items = array();

foreach ($metafiles as $metafile) {
	if (!file_exists('data/videos/'.$metafile["id"].'.mp4')) {
		continue;
	}
	$item = array();
	$item["duration"] = str_replace(array("PT", "H", "M", "S"), array ("", ":", ":", ""), $metafile["duration"]);
	$item["description"] = $metafile["description"];
	$item["summary"] = substr($metafile["description"], 0, 255);
	$item["id"] = $metafile["id"];
	$item["publishedAt"] = date(DATE_RFC822, strtotime($metafile["publishedAt"]));
	$item["title"] = xmlentities($metafile["title"]);
	$item["publishedAt_raw"] = strtotime($metafile["publishedAt"]);
	$item["filesize"] = filesize('data/videos/'.$item["id"].'.mp4');
	
	$item["flattr_url"] = "https://flattr.com/submit/auto?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D{$item["id"]}&amp;user_id={$config["flattr"]["user_id"]}&amp;title=".urlencode($item["title"])."&amp;tags={$config["flattr"]["tags"]}&amp;category=video";
	
	$item["description"] .= "<p><a href=\"{$item["flattr_url"]}\" title=\"Flattr\"><img src=\"https://api.flattr.com/button/flattr-badge-large.png\"/></a></p>";	
	$items[] = $item;
}

usort($items, "compare_items");


header("Content-Type: application/rss+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
	<channel>
		<title><?= $config["title"]; ?></title>
		<link><?= $config["url"]; ?></link>
		<language><?= $config["language"]; ?></language>
		<copyright>&#x2117; <?= date("Y")." ".$config["author"];?></copyright>
		<itunes:subtitle><?= $config["subtitle"]; ?></itunes:subtitle>
		<itunes:author><?= $config["author"]; ?></itunes:author>
		<itunes:summary><?= $config["summary"]; ?></itunes:summary>
		<description><?= $config["description"]; ?></description>
		<itunes:owner>
			<itunes:name><?= $config["owner_name"]; ?></itunes:name>
			<itunes:email><?= $config["owner_mail"]; ?></itunes:email>
		</itunes:owner>
		<itunes:image href="http://podcast.raumzeitlabor.de/podcast1400.png" />
		<itunes:category text="<?= $config["category"]; ?>" />
		<itunes:explicit>clean</itunes:explicit>
		<itunes:new-feed-url>http://feeds.feedburner.com/RaumzeitlaborPodcast</itunes:new-feed-url>		

			<?php foreach ($items as $item) { ?>
			<item>
				<title><?=$item["title"];?></title>
				<description><![CDATA[<?=$item["description"];?>]]></description>
				<enclosure url="http://podcast.raumzeitlabor.de/data/videos/<?=$item["id"];?>.mp4" length="<?=$item["filesize"];?>" type="video/mp4" />
				<guid>http://podcast.raumzeitlabor.de/data/videos/<?=$item["id"];?>.mp4</guid>
				<pubDate><?=$item["publishedAt"];?></pubDate>
				<itunes:author><?=$author;?></itunes:author>
				<itunes:subtitle><![CDATA[<?=$item["description"];?>]]></itunes:subtitle>
				<itunes:summary><![CDATA[<?=$item["summary"];?>]]></itunes:summary>
				<itunes:image href="https://img.youtube.com/vi/<?=$item["id"];?>/hqdefault.jpg" />
				<itunes:duration><?=$item["duration"];?></itunes:duration>
				<atom:link rel="payment" href="<?=$item["flattr_url"];?>" type="text/html" />
			</item>
			<?}?>
			
	</channel>
</rss>
