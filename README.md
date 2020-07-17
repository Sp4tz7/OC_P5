# PHP Blog OpenClassRooms P5
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/7e03001e53ac4555ae3d45b355afc681)](https://app.codacy.com/manual/Sp4tz7/OC_P5?utm_source=github.com&utm_medium=referral&utm_content=Sp4tz7/OC_P5&utm_campaign=Badge_Grade_Dashboard)

This project is part of the 5th course of my OpenClassRooms course
  - Build a complete website blog from scratch
  - Create associated UML files
  - Use GitHub for version control

# Features

  - Homepage with personal information, blog abstracts and link to administration
  - Blog posts page with comments
  - Contact page
  - Sitemap page
  - Login/register area 
  - Administration page

You can also:
  - Add comments for each post or reply to an existing comment
  - See your pending/approved/rejected comments in the admin zone
  - Become an administrator if authorized

### Installation

This _PHP Blog_ project requires [PHP](https://php.net/) 7.0+ and [Composer](https://getcomposer.org/) to run.

Install the whole project from Github and run Composer vendors dependencies.

```sh
$ git clone https://github.com/Sp4tz7/OC_P5.git
$ cd OC_P5
$ composer install
```

### Configuration

Before running this framework, you have to setup the database and the email SMTP data.
 - Import Database/Dump_php_blog.sql in your DBSM
 - Edit the Config/Config.php.dist file and enter the DB, SMTP and the other requested data
 - Rename Config/Config.php.**dist** to Config/Config.**php**
 - Point your virtual host to the **Public** directory

**Free Software, Hell Yeah!**
