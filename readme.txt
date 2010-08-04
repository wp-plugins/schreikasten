=== Schreikasten ===
Contributors: sebaxtian
Tags: shoutbox, ajax
Requires at least: 2.7
Tested up to: 3.0
Stable tag: 0.13.104

A shoutbox using ajax and akismet.

== Description ==

This plugin enables a shoutbox widget, integrated with Wordpress look n' feel,
using ajax to add comments and filtering comments with Akismet as spam filter.

There are 4 layouts to select from:

* Guest Book: Just to leave messages. No replies.
* Black Board: Anyone can leave replies to any comment, but there wouldn't be 
threads. A reply is listed in the order they come like another comment.
* Chat Box: The space to write comments comes at the bottom, and the messages 
list goes like in a chat room. Anyone can leave replies to any comment, but there 
wouldn't be threads. A reply is listed in the order they come like another comment.
* Questions and Answers: Only the administrator can leave a reply, and it will be 
shown right after the parent message.

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

To add a shoutbox into a page or post, use the tag __[shoutbox:title,items,rssicon]__, 
where __title__ should be the text to display as header, __items__ is the number of
items to show in every single page, and __rssicon__ (true or false) sets if you want to 
display the rss icon.

If you want to enable the rss capability, your shoutbox is in a single page or 
post and you don't use a Shoutbox widget that would be displayed everywhere in your 
site, you have to add the next line at the end of your wp-config.php file.

define('SK_CHAT', 'http://url-to-your-chat-room');

This plugin is near to a 1.0 release, any bug report would be appreciated.

Schreikasten has been translated to german by __[Andreas](http://f.indetonation.de/ "Nordic Talking")__, azerbaijani by __[Turkel](http://vsayt.com/ "Pulsuz sayt")__ and italian by  __[Marco Gargani](http://www.digitalangel.it/ "Tecnologie, Passioni, Giochi, Applicazioni, Hardware & Vita!")__. Thanks for your time guys!

Screenshots are in spanish because it's my native language. As you should know yet 
I __spe'k__ english, and the plugin use it by default.

== Installation ==

1. Decompress schreikasten.zip and upload `/schreikasten/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the __Plugins__ menu in WordPress.
3. Activate Akismet API to prevent SPAM (if required).
4. Add the widget to your sidebar.

== Frequently Asked Questions ==

= Is this plugin bug free? =

It is near to a 1.0 release, any bug report would be appreciated.

= Why the strange name? =

It means shoutbox in German.

= Why another shoutbox? =

There are a lot of shoutbox in the Interwebz, but none of them fits and looks in my
template as I want. So I decided to create one using Akismet for Spam, gravatars, the 
default CSS from Wordpress and Ajax just to make it more fun.

= I want to use a web feed management provider =

Easy. Activate the widget, enable the RSS feed, and copy the URL from the Icon near the Widget title. Create
the new feed into your feed management provider. Add this line to your __wp-congig.php__ file

define('SK_RSS', 'http://new-feed-url');

= Can I put a shoutbox inside a theme? =

Yes, use the function __sk\_shoutbox(size)__ to write the html code wherever you need, or get the code with __sk\_codeShoutbox(size)__. The argukment size would be a number and sets the number of items to show per page, or false to use the default number (5).

= Can I set my own CSS? =

Yes. Copy the file schreikasten.css to your theme folder. The plugin will check for it.

= Can I put the button over the text? =

Yes. Copy the file schreikasten.css to your theme folder, comment the section called 
__Button at the right side of the text__ and uncomment the section called __Button 
over the text__.

= Can I reduce the font size of the text near the button? =

Yes. Copy the file schreikasten.css to your theme folder, search for the class 
__sk-little__ and change the font size.

= Can I change the style for each type of user? =

Yes. Copy the file schreikasten.css to your theme folder, search for the classes 
sk-user-admin, sk-user-editor, sk-user-author and sk-user-user, and change them as you want.


== Screenshots ==

1. Left: non logged user and guest book layout (without replies). Right: administrator (edit, delete, spam) and black board (with replies).
1. Left: non logged user and questions and answers layout (only administrator can reply and any reply is shown right after the question). Right: administrator (edit, delete, spam) and chat layout (with replies, text area at bottom and messages in chat order).
3. Widget Options.
4. Page to set the API KEY to use Akismet.
5. Page to mannage comments. See the __Schreikasten__ option in the Comments item at the left menu.
6. Page to edit a comment.
7. Page to mannage blocked PCs. Read the messages sended from a specific PC even if they are from different users. Look the date at the right wich indicates when the user PC would be unlock, and the items to lock it forever or enable it now.
8. Tracking system to read comments from one user.

== Changelog ==

= 0.13.104 =
* Solved bug with hidden values.

= 0.13.103 =
* Added layout configuration. Guest Book, Black Board, Chat Room and Q&A.

= 0.13.102 =
* Update internal count when a comment has been deleted.

= 0.13.101 =
* Solved bug with sound embeded object
* Javascript modified.

= 0.13.100 =
* When using timer and there is a new message, the window would ring a sound and the window title would change if it doesn't have the focus.

= 0.13.99 =
* First release with italian translation. Thanks __[Marco Gargani](http://www.digitalangel.it/ "Tecnologie, Passioni, Giochi, Applicazioni, Hardware & Vita!")__.

= 0.13.98 =
* Bug with duplicated div id.
* Solved bug with blacklist.

= 0.13.97 =
* Admin widget now with AJAX capabilities to delete, mark as spam or reject a comment.

= 0.13.96 =
* Solved bug with rss feed capabilitie.

= 0.13.95 =
* Changed GUI to help users with API Key.
* Added uninstall capabilities.

= 0.13.94 =
* Solved bug with default size.

= 0.13.93 =
* Modified add_comment function to solve strange characters in alias entry.

= 0.13.92.1 =
* Internal theme function bug.

= 0.13.92 =
* Theme function bug.

= 0.13.91 =
* Javascript bug.

= 0.13.90 =
* First multiwidget release.
* New tag system.

= 0.13.0.1 =
* Data checking inside the PHP script (solving a detected spam atack).

= 0.13 =
* Stable release.

= 0.12.3.96 =
* Solved some HTML errors (Thanks Frank).
* Solved a bug with new line characters.

= 0.12.3.95 =
* Don't show title aspects when there is no title
* Solved a bug with timer in slow conections.

= 0.12.3.3 =
* Solved a bug with masqued domains and subdomains.

= 0.12.3.2 =
* Erasing not used code.

= 0.12.3.1 =
* Solved a Bug in the configuration form that asked for Minimax.
* Solved a Bug in the configuration form that asked for an API key even if there is a valid one.

= 0.12.3 =
* Semaphore technic applied to the new AJAX system.
* RSS with UTF-8 header

= 0.12.2 =
* Solved a timer situation
* Using universal.js

= 0.12.1 =
* Solved a bug in the timer with the new AJAX system.

= 0.12 =
* First release that doesn't require Minimax.
* New CSS style.

= 0.11.24 =
* Added configuration to set moderation (required, not required, as general configuration) 

= 0.11.23.1 =
* Solved an issue with external CSS.
* Modified number of pages to show.

= 0.11.23 =
* Updated nonce system to enhace security. Using feed url as seed.
* New CSS for pagination system.

= 0.11.22 =
* Solved a Bug with 'show comments' when in list version (no avatar). 

= 0.11.21 =
* Solved a bug with Quotation marks and Apostrophe
* Wouldn't require confirmation if the comment was send by the administrator when he was loged.

= 0.11.20 =
* New cache system fixed.

= 0.11.19 =
* Added tags for feed and feed icon.
* Solved a bug with GMT 0 in RSS feeds. 

= 0.11.18.2 =
* Solved a bug with timer when show the warning message about to login to write a comment.

= 0.11.18.1 =
* Solved a bug with the new content system.
* Reply system works again.

= 0.11.18 =
* Content system.

= 0.11.17 =
* Feed system enhaced.

= 0.11.16 =
* Added RSS feed.
* Widget item to show RSS feed.

= 0.11.15 =
* Solved a bug in activation system.

= 0.11.14 =
* Solved a bug with date format.

= 0.11.13 =
* Time formated to looks like the general configuration.
* Modified the CSS to fit better in general themes.
* Solved a bug with the charset in database.
* First release with Arzeibajan translation.


= 0.11.12 = 
* Solved a bug in the plugin configuration form.

= 0.11.11 = 
* Solved errors in german translation. Es war mein Fehler, nicht von Andreas.

= 0.11.10 =
* Solved a bug with german characters.

= 0.11.9 =
* Solved a bug with the notification system when a confirmation is required.

= 0.11.8 =
* First release with German translation.
* Modified the CSS to allow the button to be placed at the right of the text or over it. 

= 0.11.7 =
* Solved a bug with the notification system when a confirmation is required.
* Solved a bug with the allowed size in messages.

= 0.11.6 =
* UI updated to work in IE6.

= 0.11.5 =
* Noy you can configure the number of characters allowed per comment.

= 0.11.4 = 
* UI updated to simplify the 'require email' configuration.

= 0.11.3.1 =
* Solved a bug with the UI.

= 0.11.3 =
* UI updated to simplify the 'send email' configuration.
* Fixed a bug with the & character.
* User interface modified to set more items per page.

= 0.11.2 = 
* Using nonce to not show data when someone call the ajax script outside the plugin.
* Silence is gold.

= 0.11.1 =
* Now you can define if the plugin will send a mail (always, never, or use general configuration) to inform there is a new comment.

= 0.11 =
* Using minimax 0.3

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
