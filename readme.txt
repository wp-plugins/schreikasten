=== Schreikasten ===
Contributors: sebaxtian
Tags: shoutbox, ajax
Requires at least: 2.7
Tested up to: 2.8.6
Stable tag: 0.10.4.2

A shoutbox using ajax and akismet.

== Description ==

This plugin enables a shoutbox widget, integrated with Wordpress look n' feel,
using ajax to add comments and filtering comments with Akismet as spam filter. 

It also allows to define if the administrador has to approve every new comment.
You can mark a PC to be blocked for furter comments until a date, or just to mark 
any comment from this PC to wait for aprovation. Even if the user send it with a 
different name and e-mail if it comes from the same PC it will be blocked.

You can track the comments from a user and the comments from a PC.

Schreikasten is integrated with WP's users system. You can configure it to only allow
comments from registered users.

The anti Spam filter requires an Akismet API KEY. If you have one enabled in your 
site you can use it in this plugin too. If you don't have an API KEY, create one
in [this site](http://en.wordpress.com/api-keys/).

This plugin requires __[minimax](http://wordpress.org/extend/plugins/minimax/ "A minimal Ajax library")__ in order to work.

This plugin is near to a 1.0 release, any bug report would be appreciated.

Screenshots are in spanish because it's my native language. As you should know yet 
I __spe'k__ english, and the plugin use it by default.

== Installation ==

1. Install [minimax](http://wordpress.org/extend/plugins/minimax/).
2. Decompress schreikasten.zip and upload `/schreikasten/` to the `/wp-content/plugins/` directory.
3. Activate the plugin through the __Plugins__ menu in WordPress.
4. Activate Akismet API to prevent SPAM (if required).
5. Add the widget to your sidebar.

== Frequently Asked Questions ==

= Is this plugin bug free? =

It is near to a 1.0 release, any bug report would be appreciated.

= Why the strange name? =

It means shoutbox in german.

= Why another shoutbox? =

There are a lot of shoutbox in the Interwebz, but none of them fits and looks in my
template as I want. So I decide to create one using Akismet for Spam, gravatars, the 
default CSS from Wordpress and Ajax just to make it more fun.

= It say something about minimax. What's this? =

This plugin requires __[minimax](http://wordpress.org/extend/plugins/minimax/ "A minimal Ajax library")__ in order to work.

= Can I put a shoutbox inside a theme? =

Yes, use the function __sk\_shoutbox()__ to write the html code wherever you
need. Warning: You can not have more than one shoutbox in the same page, even if 
one is in a sidebar and the other in the conntents.

= Can I set my own CSS? =

Yes. Copy the file schreikasten.css to your theme folder. The plugin will check for it.


== Screenshots ==

1. The widget (left) for a non logged user (right) and the administrator.
2. Widget Options.
3. Page to set the API KEY to use Akismet.
4. Page to mannage comments. See the __Schreikasten__ option in the Comments item at the left menu.
5. Page to edit a comment.
6. Page to mannage blocked PCs. Read the messages sended from a specific PC even i they are from different users. Look the date at the right wich indicates when the user PC would be unblock, and the items to block it forever or enable it now.
7. Tracking system to read comments from one user.

== Changelog ==

= 0.10.4.2 =
* The code has been indented, documented and standardised.
* Solved a bug with the headers, now Schreikasten works with the plugin POD.
* Solved a bug where SK asked too many times to validate the key. Now it is quite faster checking spam.

= 0.10.4.1 =
* Solved a situation with the widget layout in IE.

= 0.10.4 =
* Added the function to put the Shoutbox in the theme code.
* Solved a situation with the widget layout in IE.

= 0.10.3 =
* Now you can set your own css file (see FAQ).

= 0.10.2 =
* Using the new semaphore system in minimax - Required in IE
* More values in the lists to configure the widget

= 0.10.1 =
* Solving some situations in the instalation.

= 0.10 =
* First version in SVN.
