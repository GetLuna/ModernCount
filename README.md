ModernCount Readme
================

ModernCount (previously SHTracker) is a PHP download counter. You can use it to track the number of times a link has been clicked or the number of times a file has been downloaded. ModernCount can also be used to hide affiliate links. ModernCount is integrated in MocernBB.org, I release the script now for everyone to download. Enjoy it.

The script uses SQL databases. It comes with an admin panel where you can view how many times a download has been accessed. You can also easily add, edit or remove downloads using the panel. ModernCount can also display the current count for a download on any web page.

#### Current Version: 3.0.0

##Features

* Password protect downloads
* Can be used to track links
* Count unique visitors to avoid multiple counts from same user
* Supports displaying of ads before user is redirected to download
* Full admin panel
* Display download counts to users
* Themed by Twitter Bootstrap, offers a choice of themes
* Sort and search downloads through the use of DataTables
* Works well on mobile devices due to a responsive layout

##Installation

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Download and unzip ModernCount-xxxx.zip
3. Upload the ModernCount folder to your server via FTP or your hosts control panel
4. Open up http://yoursite.com/ModernCount/installer in your browser and enter your database/user details
5. Delete the "installer" folder from your server
6. Login to the admin panel using the username and password you set during the install process
7. Add your downloads
8. ModernCount should now be set up

##Usage

The main script is called like this: /get.php?id=mydownload1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/ModernCount/get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/ModernCount/get.php?id=mydownload1

To find this URL select the download and click the "Show Tracking Link" (the Search-icon) button

This will log the count of the download and redirect users to the file or web page

This script can also be called via $_POST just set the name of the form to ID and the value to the ID you wish to download

To show the download count for one ID only, call http://yoursite.com/ModernCount/display.php?id=mydownload1. This could be done by linking directly to display.php, using an iframe or by using a PHP include

**Examples**

```php
<?php
$_GET["id"] = "download1";
include("ModernCount/display.php");
?>
```

```html
<iframe src="ModernCount/display.php?id=download1"></iframe>
```

##Administration

Open up ModernCount/admin to add new downloads, view statistics, update existing downloads or delete downloads. The admin panel can also be used to password protect downloads or to show you the tracking link for a download.

##Removal

To remove ModernCount, simply delete the ModernCount folder from your server and delete the "Data" table from your database

##Contributing

Feel free to fork and make any changes you want to ModernCount. If you want them to be added to master then send a pull request.

##Thanks to

Joshfs Indication software.