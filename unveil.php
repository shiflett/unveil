<?php

// Set default timezone if it's not set in php.ini.
if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/New_York');
}

// Create a descriptive page title.
if (isset($_GET['dir'])) {
    $title = $_GET['dir'];
} elseif (isset($_GET['file'])) {
    $title = $_GET['file'];
} else {
    $title = '/';
}

?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
<meta charset="utf-8" />
<title>Unveil: <?php echo $title; ?></title>
</head>
<body>
<h1>Unveil: <?php echo $title; ?></h1>
<hr />
<pre>
<?php

if (isset($_GET['dir'])) {
    ls($_GET['dir']);
} elseif (isset($_GET['file'])) {
    cat($_GET['file']);
} else {
    ls('/');
}

?>
</pre>
<hr />
<p>Powered by <a href="https://github.com/shiflett/unveil">Unveil</a>, a simple filesystem browser and security testing tool by <a href="http://shiflett.org/">Chris Shiflett</a>.</p>
</body>
</html>
<?php

function ls($dir) {
    $handle = dir($dir);
    while ($filename = $handle->read()) {
        $fullname = "{$dir}{$filename}";

        if (is_link($fullname)) {
            $link = lstat($fullname);
            $size = $link['size'];
            $perms = $link['mode'];
            $owner = posix_getpwuid($link['uid']);
            $owner = $owner['name'];
            $group = posix_getgrgid($link['guid']);
            $group = $group['name'];
            $modified = date('Y-m-d H:i', $link['mtime']);
        } else {
            $size = filesize($fullname);
            $perms = fileperms($fullname);
            $owner = posix_getpwuid(fileowner($fullname));
            $owner = $owner['name'];
            $group = posix_getgrgid(filegroup($fullname));
            $group = $group['name'];
            $modified = date('Y-m-d H:i', filemtime($fullname));
        }

        if (($perms & 0xC000) == 0xC000) {
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            $info = 'p';
        } else {
            $info = 'u';
        }

        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
        
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
        
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

        $line = str_pad($info, 15);
        $line .= str_pad($owner, 12);
        $line .= str_pad($group, 12);
        $line .= str_pad($size, 10);
        $line .= str_pad($modified, 17);

        if ($filename == '.') {
            if ($dir == '/') {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir=/\">./</a>";
            } else {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir={$dir}\">{$filename}/</a>";
            }
        } elseif ($filename == '..') {
            if ($dir == '/') {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir=/\">../</a>";
            } else {
                $parent = explode('/', $dir);
                $last = count($parent) - 2;
                unset($parent[$last]);
                $parent = implode('/', $parent);
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir={$parent}\">{$filename}/</a>";
            }
        } elseif (is_link($fullname)) {
            if (is_readable($fullname)) {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir={$fullname}/\">{$filename}@</a>";
            } else {
                $line .= "{$filename}@";
            }
        } elseif (is_dir($fullname)) {
            if (is_readable($fullname)) {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir={$fullname}/\">{$filename}/</a>";
            } else {
                $line .= "{$filename}/";
            }
        } else {
            if (is_readable($fullname)) {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?file={$fullname}\">{$filename}</a>";
            } else {
                $line .= $filename;
            }
        }

        echo "$line\n";
    }

    $handle->close();
}

function cat($file) {
    // This requires PHP 5.3.
    $info = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
    list($type, $subtype) = explode('/', $info);

    switch ($type) {
        case 'image':
            $data = base64_encode(file_get_contents($file));
            echo "<p><img src=\"data:{$info};base64,{$data}\" /></p>";
            break;
        case 'text':
            echo htmlentities(file_get_contents($file), ENT_QUOTES, 'UTF-8');
            break;
        default:
            echo "<p>Unsupported file type: $info</p>";
            break;
    } 
}

?>
