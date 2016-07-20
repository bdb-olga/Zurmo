# Zurmo Open Source CRM

<img height="309" src="http://zurmo.org/wp-content/uploads/2011/08/Tablet480x309.png" alt="zurmo-screenshot" title="zurmo-screenshot">

**Zurmo is an open source CRM  application written in PHP utilizing jQuery, Yii Framework, and RedBeanPHP.**

Our goal with Zurmo is to provide  an easy-to-use, easy-to-customize CRM application that can be adapted
to any  business use case. 
We have taken special care to think through many different use cases and have
designed a system that we believe provides a high degree of flexibility, covering a wide variety of use
cases out of the box.

We don't have a million features. We can never beat out existing players in a feature war.

But considering companies wind up only using a handful of features, we don't think it really  matters.

What we have so far is the beginning of a high-quality sales force  automation tool.
Stay tuned as we continue to make improvements.

From a technical perspective, we  are very excited. We have decided to build Zurmo on three awesome
development  frameworks, Yii, RedBeanPHP, and jQuery.
With almost a religious zeal for  testing,
you will find that our obsession with test driven development means a  more stable application.

Gone are the days of 'upgrade and pray'. Now it is  'upgrade and test'.

We have installation walkthroughs based on different development  platforms.

[Windows  Installation Instructions for Development using Apache] [wi]
[wi]: http://zurmo.org/wiki/windows-installation-instructions-for-development

[Linux  Installation Instructions for Development] [li]
[li]: http://zurmo.org/wiki/linux-installation-instructions-for-development

Production
Hardware
RAM: 256(Minimum), >= 512M (Recommended)
Disk: >= 500M
Software
Apache >= 2.2.1 Or IIS >= 5.0.0 (Zurmo should work on other webservers, IE cherokee, as we have not used any webserver specific modules. Be aware though, we have not tested Zurmo extensively on other webservers)
Apache mod_deflate (Optional, recommended for good traffic deployments)
Memcached (Optional, recommended for good traffic deployments)
MySQL Server >= 5.1
Either root access credentials shall be known or a new database and user credential pair should be created for zurmo
Settings:
character-set-server=utf8
collation-server=utf8_unicode_ci (Not utf8_general_ci)
default-storage-engine=INNODB
max_sp_recursion_depth=20 (Recommend value: 100)
max_allowed_packet = 20M (Optional)
thread_stack = 512K
optimizer-search-depth = 0
log_bin = 0 (this is the default setting, in most cases you will not need to modify it)
Database should not be running in strict mode
PHP >= 5.3.3
Settings
date.timezone must be set
memory_limit >= 128M (Minimum), >=256M (Recommended)
file_uploads should be on
upload_max_filesize >= 20M (Optional. You can adjust this to higher value depending on your needs)
post_max_size >= 20M (Optional. You can adjust this to higher value depending on your needs)
max_execution_time >= 300
set_include_path should be enabled
$_SERVER should be accessible
Extensions
pcre
spl
ctype
apc >= 1.0.0 (Optional, recommended for good traffic deployments)
soap
curl >= 6.0
mbstring
memcache >= 2.2.0 (php5-memcache, not php5-memcached)
imap (Optional)
pdo
pdo_mysql
zip (Optional)
gd (Optional)
Paths
The following paths should be writable by the webserver process:
app/protected/config/debug.php (may not exist)
app/version.php
app/protected/config/perInstance.php
app/assets/
app/protected/data/
app/protected/runtime/

 
For support please visit and register for our [forum] [fp] pages.
[fp]: http://zurmo.org/forums/

IMPORTANT NOTE: This is mirror repository from our Mercurial repository, which is hosted on BitBucket: https://bitbucket.org/zurmo/zurmo. We update this repository only when we release new stable releases, so it is strongly recomanded to clone Zurmo CRM from our master Mercurial repository.

