# PHP Blog OpenClassRooms P5
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/7e03001e53ac4555ae3d45b355afc681)](https://app.codacy.com/manual/Sp4tz7/OC_P5?utm_source=github.com&utm_medium=referral&utm_content=Sp4tz7/OC_P5&utm_campaign=Badge_Grade_Dashboard) ![PHP](https://img.shields.io/badge/PHP-%3E%3D%207.2.5-green) [![Security Headers](https://img.shields.io/badge/SecurityHeaders-A%2B-green)](https://securityheaders.com/?q=https%3A%2F%2Fsiker.ch%2F)

This project is part of the 5th course of my OpenClassRooms course
-  Build a complete website blog from scratch
-  Create associated UML files
-  Use GitHub for version control

## Features

-  Homepage with personal information, blog abstracts and link to administration
-  Blog posts page with comments
-  Contact page
-  Sitemap page
-  Login/register area 
-  Administration page

You can also:
-  Add comments for each post or reply to an existing comment
-  See your pending/approved/rejected comments in the admin zone
-  Become an administrator if authorized

### Requirements

In order to use this framework, the following points must be respected

-  PHP version >=7.2.5
-  PHP [Imagick](http://pecl.php.net/package/imagick/3.4.4/windows) lib installed according to your PHP version

### Installation

This _PHP Blog_ project requires [PHP](https://php.net/) 7.2.5+ and [Composer](https://getcomposer.org/) to run.

Install the whole project from Github and run Composer vendors dependencies.

#### File
```sh
git clone https://github.com/Sp4tz7/OC_P5.git
cd OC_P5
composer install
```

#### Database

```
mysql> CREATE DATABASE IF NOT EXISTS php_blog DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
mysql> USE php_blog
mysql> SOURCE YOUR/ROOT/PATH/Database/Dump_php_blog.sql
```

### Configuration

Before running this framework, you have to setup the database and the email SMTP data.
1.  Import Database/Dump_php_blog.sql in your DBSM.
2.  Edit the Config/Config.php.dist file and enter the DB, SMTP and the other requested data.
3.  Rename Config/Config.php.**dist** to Config/Config.**php**.
4.  Point your virtual host to the **Public** directory.
5.  Login to your admin account using username **admin** and password **superadmin** and change your personal data.

[Link to the project web example](https://siker.ch)

**Free Software, Hell Yeah!**