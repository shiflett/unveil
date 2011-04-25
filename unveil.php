<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
<meta charset="utf-8" />
<title>Unveil, a simple filesystem browser and security testing tool</title>
</head>
<body>
<p>Powered by <a href="https://github.com/shiflett/unveil">Unveil</a>, a simple filesystem browser and security testing tool by <a href="http://shiflett.org/">Chris Shiflett</a>.</p>
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
<pre>
<?php $safe = ini_get('safe_mode'); ?>
[<code>safe_mode</code>] [<code><?php echo $safe; ?></code>]
<?php $base = ini_get('open_basedir'); ?>
[<code>open_basedir</code>] [<code><?php echo $base; ?></code>]
</pre>
<hr />
</body>
</html>
<?php

function ls($dir) {
    $handle = dir($dir);
    while ($filename = $handle->read()) {
        $size = filesize("{$dir}{$filename}");
        $perms = fileperms("{$dir}{$filename}");
        $owner = posix_getpwuid(fileowner("{$dir}{$filename}"));
        $owner = $owner['name'];
        $group = posix_getgrgid(filegroup("{$dir}{$filename}"));
        $group = $group['name'];
        $modified = date('Y-m-d H:i', filemtime("{$dir}{$filename}"));

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
        } elseif (is_dir("{$dir}{$filename}")) {
            if (is_readable("{$dir}{$filename}")) {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?dir={$dir}{$filename}/\">{$filename}/</a>";
            } else {
                $line .= "{$filename}/";
            }
        } else {
            if (is_readable("{$dir}{$filename}")) {
                $line .= "<a href=\"{$_SERVER['PHP_SELF']}?file={$dir}{$filename}\">{$filename}</a>";
            } else {
                $line .= $filename;
            }
        }

        echo "$line\n";
    }

    $handle->close();
}

function cat($file) {
    echo htmlentities(file_get_contents($file), ENT_QUOTES, 'UTF-8');
}

?>
