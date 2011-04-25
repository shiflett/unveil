Unveil, a simple filesystem browser and security testing tool
=============================================================

Requirements
------------

- [PHP](http://php.net/)

Usage
-----

Just put `unveil.php` anywhere under document root and visit it with any
browser.

BE SURE TO REMOVE THIS TOOL WHEN YOU'RE FINISHED TESTING.

The output is very similar to what `ls -l` looks like. For example, here's what
a listing of my web site's home directory looks like:

    drwxrwxr-x     www-data www-data 4096      2011-03-29 13:16 ../
    drwxr-xr-x     shiflett shiflett 4096      2011-04-20 11:35 app/
    drwxr-xr-x     shiflett shiflett 4096      2011-03-12 18:27 css/
    drwxr-xr-x     shiflett shiflett 4096      2011-04-20 10:54 pages/
    drwxr-xr-x     shiflett shiflett 4096      2011-04-20 10:48 fragments/
    drwxr-xr-x     shiflett shiflett 4096      2011-03-09 15:25 ./
    drwxr-xr-x     shiflett shiflett 4096      2010-08-04 14:12 js/
    drwxr-xr-x     shiflett shiflett 12288     2011-03-27 15:36 img/
    drwxr-xr-x     shiflett shiflett 4096      2011-03-31 11:54 inc/
    -rw-r--r--     shiflett shiflett 727       2010-07-13 18:50 VERSION

When you first visit `unveil.php`, you're shown a listing of `/`. The names of
files and directories that you can read are links that you can follow to explore
the filesystem.

On shared hosts, it is common for the web server to run as the same user for all
hosts. This typically means that all files the web server needs to be able to
read can also be read by anyone else on the server. Unveil is a tool to help
highlight these types of risks.

> Note: The `safe_mode` configuration directive can limit the usefulness of this
> tool, but it does not protect you on a shared host, because an attacker can
> simply use another language. Partly due to the false sense of security it
> provides, `safe_mode` is being deprecated.

For more information and background about Unveil, read my article on
[shared hosting](http://shiflett.org/articles/shared-hosting).