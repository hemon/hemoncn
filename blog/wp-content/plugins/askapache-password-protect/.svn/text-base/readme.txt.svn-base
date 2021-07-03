=== AskApache Password Protect ===
Contributors: AskApache, cduke250
Donate link: http://www.askapache.com/donate/
Tags: password, secure, wp-admin, protect, spammer, security, admin, username, access, authorization, authentication, spam, hack, login, askapache, htaccess, rewrite, redirect, mod_security, htpasswd
Requires at least: 2.2
Tested up to: 2.5.1
Stable tag: 4.3.2
This plugin Adds Additional Password Protection and Security to your blog.


== Description ==

This plugin doesn't control WordPress or mess with your database, instead it utilizes fast, tried-and-true built-in Security features to add multiple layers of security to your blog.  This plugin is specifically designed and regularly updated specifically to stop automated and unskilled attackers attempts to exploit vulnerabilities on your blog resulting in a hacked site.  

The power of this plugin is that it creates a virtual wall around your blog allowing it to stop attacks before they even reach your blog to deliver a malicious payload.  In addition this plugin also has the capability to block spam with a resounding slap, saving CPU, Memory, and Database resources.   Choose a username and password to protect your entire /wp-admin/ folder and login page.  Forbid common exploits and attack patterns with Mod_Security, Mod_Rewrite, Mod_Alias and Apache's tried-and-true Core Security features.  This plugin requires the worlds #1 web server, Apache, and web host support for .htaccess files.

Has a user-contributed attack signature system modeled after the Snort Intrusion Detection and Prevention system, Nessus Vulnerability Scanner, and the Web Application Firewall ModSecurity.

1. http://www.modsecurity.org/
2. http://snort.org/
3. http://www.nessus.org/nessus/
4. http://httpd.apache.org/


== Installation ==

This section describes how to install the plugin and get it working.

1. Extract zip so wp-content/plugins/askapache-password-protect/askapache...php
2. Activate the Plugin.
3. Setup plugin options.


== Frequently Asked Questions ==

Do I have to type in my username and password every admin page?

No.  You just have to type it in once and it will keep you logged in until you close your browser.

How Secure Is It

In Basic HTTP Authentication, the password is passed over the network not encrypted but not as plain text -- it is "uuencoded." Anyone watching packet traffic on the network will not see the password in the clear, but the password will be easily decoded by anyone who happens to catch the right network packet.

So basically this method of authentication is roughly as safe as telnet-style username and password security -- if you trust your machine to be on the Internet, open to attempts to telnet in by anyone who wants to try, then you have no reason not to trust this method also.

In MD5 Message Digest Authentication, the password is not passed over the network at all. Instead, a series of numbers is generated based on the password and other information about the request, and these numbers are then hashed using MD5. The resulting "digest" is then sent over the network, and it is combined with other items on the server to test against the saved digest on the server. This method is more secure over the network, but it has a penalty. The comparison digest on the server must be stored in a fashion that it is retrievable. Basic Authentication stores the password using the one way crypt() function. When the password comes across, the server uudecodes it and then crypts it to check against the stored value. There is no way to get the password from the crypted value. In MD5, you need the information that is stored, so you can't use a one way hashing function to store it. This means that MD5 requires more rigorous security on the server machine. It is possible, but non-trivial, to implement this type of security under the UnixTM security model. 

See also http://www.askapache.com/htaccess/apache-htaccess.html


== Other Notes ==

Of course no plugin would ever be able to stop real hacker intent on taking over your blog, if you are connected to the net on a public line, of course you coun’t stop them. The people who are attacking the blogosphere are for the most part just playing. They "hack" code that "exploits" a "vulnerabiliity" in some open-source software like phpBB or WordPress. Those people actually help the community of open source software like WordPress by finding security issues and bringing them to light.. So who is this plugin built to stop? It’s built to stop the people who are trying all the time to maliciously crack into YOUR average blog. Why would someone want to hack an AVERAGE blog like mine or yours? Well the answer is that its not an actual group, entity, or person who is going to try hacking into your blog. Its an army of robots.. and they will never stop the attack.

So how do these robots attack us? What is their ammo? Their ammo is very specific knowledge of exploiting security holes in very specific software to "crack" your blog. Vulnerabilities are discovered all the time, mostly small ones, but those vulnerabiilties that are dangerous to those of us running WordPress 2.5 are LETHAL to those of us running 2.1.. just absolutely deadly. So These robots are programmed to do one thing and one thing only, try the exact same exploit that would work against 2.3 against every computer on the internet, as fast as they can and as anonymously as they can.. terrorizing the networks with these non-stop requests and slowing down the whole internet, which hopefully will start getting faster as more people use this plugin. Robots have no choice but to leave my servers alone. They understand what a 403 Forbidden means, to them it means take me off your list, the exploit I’m carrying is not compatible. But once again, this will not stop a hacker, this will stop 99.9% of the same bots that "hacked" 99.9% of the blogs.

http://www.askapache.com/htaccess/mod_security-htaccess-tricks.html


== Screenshots ==

1. Available .htaccess Security Modules
2. Installation Tests for htaccess Capabilities
3. Setting up Initial Username and Password
4. Multiple User Management
5. Shows Current .htaccess Files





