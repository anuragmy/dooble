<?php 
require('classes/DomDocumentParser.php');
require('config.php');

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImage = array();

function linkExists($url) {
    global $connection;
    $sql = "SELECT * from sites WHERE url = '$url'";
    $result = mysqli_query($connection,$sql);
    if($result && mysqli_num_rows($result) >= 1) return true;
    else return false;
    
}

function insertLink($url,$title,$description,$keywords) {
    global $connection;
    $sql = ("insert into sites(url,description,keywords,title) values ('$url','$description','$keywords','$title') ");
    $result = mysqli_query($connection,$sql);
    if(!$result && !mysqli_affected_rows($connection) == 1) {
        echo mysqli_error($connection);
    }
}

function insertImage($siteUrl,$imageUrl,$alt,$title) {
    global $connection;
    $sql = ("insert into images (siteUrl,imageUrl,alt,title) values ('$siteUrl','$imageUrl','$alt','$title') ");
    $result = mysqli_query($connection,$sql);
    if($result && mysqli_affected_rows($connection) == 1) {
        echo "$siteUrl added successfully";
    }
    else {
        echo mysqli_error($connection);
    }
}

function getDetails($url) {
    global $alreadyFoundImage;
    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];
    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitle();

    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) return; 

    $title = $titleArray->item(0)->nodeValue;

    $title = str_replace("\n","",$title);
    
    if($title == "") return;

    $description = "";
    $keywords = "";

    $metaArray = $parser->getMeta();

    foreach($metaArray as $meta) {
        if($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }

        if($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n","",$description);
    $keywords = str_replace("\n","",$keywords);




    echo "URL: $url.title:$title"."\n";
    if(!linkExists($url)) 
    insertLink($url,$title,$description,$keywords);

    $imageArray = $parser->getImages();
    foreach($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {
            continue;
        }

        /*if($src[0] == '/') {
            $src = $scheme."://".$host.$src;
        }*/

        if(!in_array($src,$alreadyFoundImage)) {
            $alreadyFoundImage[] = $src;
            
            insertImage($url,$src,$alt,$title);
        }
    }
}


function followLinks($url) {

    global $crawling;
    global $alreadyCrawled;
    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    $parser = new DomDocumentParser($url);
    $linkedList = $parser->getLinks();
    
    foreach($linkedList as $links) {
       $href = $links->getAttribute("href");
       if(strpos($href,"#") !== false) 
        continue;

        if (substr($href,0,1) == '/') {
            $href = $scheme . "://" . $host . $href;
        }
        else if(substr($href,0,2) == '//') { 
            $href = $scheme . ":" . $host . $href;
        }
        else if (substr($href,0,2) == './') {
            $href = $scheme . "://" . $host . dirname(parse_url($url)) . substr($href,1);
        }
        else if((substr($href,0,3) == '../') || (substr($href,0,5) != 'https' && substr($href,0,4) != 'http')) {
            $href = $scheme . "://" . $host . "/" . $href;
        }
        

        /*if(!in_array($href,$alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            getDetails($href);
        }*/
        // /echo $href."<br>";
        getDetails($href);
    }
   /* echo "this is crawled array"."\n";
    echo "<pre>";
    print_r($crawling);
    echo "</pre>";
    echo "\n"."this is alreadycrawled array"."\n";
    echo "<pre>";
    print_r($crawling);
    echo "</pre>";
    */
    //followLinks($sites);
    
}

/*function createLink($url,$src) {
    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    if (substr($src,0,1) == '/') {
        $src = $scheme . "://" . $host . $src;
    }
    else if(substr($src,0,2) == '//') { 
        $src = $scheme . ":" . $host . $src;
    }
    else if (substr($src,0,2) == './') {
        $src = $scheme . "://" . $host . dirname(parse_url($url)) . substr($src,1);
    }
    else if((substr($src,0,3) == '../') || (substr($src,0,5) != 'https' && substr($src,0,4) != 'http')) {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    
    return $src;
}*/

$url = "https://www.reddit.com";
followLinks($url);

//insertLink('https://www.apple.com','apple','this is a very good product','apple,ios.mac');
//linkExists('https://www.apple.com');
//insertImage('https://www.apple.com','image.png','cat','this is a cat');