Unveil, a simple filesystem browser
===================================

Requirements
------------

- [PHP](http://php.net/)

Usage
-----

Just put `unveil.php` anywhere under document root and visit it with any
browser.

BE SURE TO REMOVE THIS TOOL WHEN YOU'RE FINISHED TESTING.

The output is very similar to what `ls -l` looks like. For example, here's what
a listing of my web site looks like:

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

Unveil is meant to highlight some of the security risks of shared hosting. If
your copy of `unveil.php` is able to show you the contents of other people's
files, then they can also see yours, so hiding passwords in those files is not
sufficient.

I'll provide more information later. For now, I just want to release this for
those who might find it useful.