<?php
$html = true;
if( isset( $_GET['list'] ) )
    $html = false;
$versionOnly = false;
if( isset( $_GET['version'] ) )
    $versionOnly = true;
if( !$html ) header( "Content-type: text\plain;" );
define( "SCRIPT_DIR", "http://" . $_SERVER['SERVER_NAME'] . pathinfo( $_SERVER['REQUEST_URI'], PATHINFO_DIRNAME ) . "/" );
define( "CRC_DELIMITER", "|" );
define( "CRC_NEXTFILE", ";" );
define( "NM_VERSION", "2.3" );
define( "NM_REVISION", "" );
define( "NM_PATCH", 0 );

if( $versionOnly )
    die( "NM:" . NM_VERSION . ":" . NM_REVISION . ":" . NM_PATCH );

if( $html )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
    <title>NetMatch updater files</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<h1>NetMatch updater files</h1>
<?php
echo "<h2>Current version: v" . NM_VERSION . NM_REVISION;
if( NM_PATCH > 0 )
    echo ( NM_PATCH <= 9 ? "_0" : "_" ) . NM_PATCH;
echo "</h2>";
echo "\n<ol>";
}

gohash();

// Bufferoidaan output ettei CB jäädy.
if( !$html ) ob_start();
foreach( $crcArray as $dirArray )
{
    uksort($dirArray, "isort");
    foreach( $dirArray as $file => $fileArray )
    {
        if( !$html )
        {
            echo $file . CRC_DELIMITER . $fileArray['hash'] . CRC_DELIMITER . $fileArray['link'] . CRC_NEXTFILE;
        } else {
            echo "\n<li>$file<ul>";
            echo "\n\t<li>Hash: $fileArray[hash]</li>";
            echo "\n\t<li>Link: <a href=\"$fileArray[link]\">$fileArray[link]</a></li>";
            echo "\n</ul></li>";
        }
    }
}

// Tulostetaan kaikki nyt vasta.
if( !$html )
{
    ob_end_flush();
} else {
?>
</ol>

</body>
</html>
<?php
}
    

function gohash( $dir = "." )
{
    global $crcArray;
    
    $fileArray = scandir( $dir );
    foreach( $fileArray as $file )
    {
        if( $file != "." && $file != ".." && $file != basename( __FILE__ ) )
        {
            if( $dir != "." )
                $file = $dir . "/" . $file;
            if( is_dir( $file ) )
            {
                gohash( $file );
                continue;
            }
            $hash = strtoupper(hash_file('crc32b', $file));
            $link = SCRIPT_DIR . $file;
            $crcArray[$dir][$file] = array( "hash" => $hash, "link" => $link );
        }
    }
}

function isort($a,$b) {
    return strtolower($a)>strtolower($b);
}
?>