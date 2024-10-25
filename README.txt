=== Edge Caching and Firewall with BunnyCDN - Faster TTFB, DDoS protection with Reverse-Proxy ===
Contributors: thegulshankumar
Donate link: https://www.buymeacoffee.com/gulshan
Tags: performance, pagespeed, optimization, security, caching, ddos, cache, reverse-proxy
Requires at least: 4.5
Requires PHP: 7.2
Tested up to: 5.7
Stable tag: 1.0.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Make your site globally faster and secure like never before.

== Description ==

Reverse-Proxy Edge Caching & DDoS protection in 90 seconds setup 😎

> The use of bunny.net API key is subject to its applicable 'Pricing', 'Terms' and 'Acceptable Usage Policy'.

= 🎁 Your Benefits  =
* Get TTFB ~35ms globally with Reverse-Proxy Edge caching.
* Hide the Origin IP to prevent DDoS attack.
* Prevent bypassing bunny Proxy with Origin Access Token
* Automatically bypass the HTML cache for logged in users.
* Fix latency issues for anonymous visitors far from your server.
* Edge Caching at BunnyCDN Edge with auto purge made easier! 
* Not just Static files, cache everything at one hostname.
* Cache Post, Pages, RSS Feed with automatic purge by URL.

== 🥉 Salient Features ==
* Get your beloved admin toolbar while logged in. (Never in logged out as expected)
* Automated purge for best possible Cache HIT ratio in the Industry
* Purge relevant URLs for Post status change, new comment
* Automatically purge all cache for site-level changes
* Always edge and browser caching for static files even if logged in.
* Automatically bypass the HTML cache for dynamic request
* Automatically Ignores tracking-system query strings for better Cache-HIT ratio.
* Allow you to purge cache for a page or whole site via admin toolbar
* GDPR compliant

== Intelligent cookies-session handling 👏 ==

> Crafted carefully 😎

* Displays comment moderation status to the comment author.
* Bypasses caching for 'Remember Me' checked the login
* In other event, can serve cached response instantly after 'log out'.
* Bypasses caching for cart of 'Easy Digital Downloads' & 'WooCommerce'

== Want to serve nex-gen Image? It's compatible! ✌️ ==
* Free: Vary Cache, to extend support for [WebP Express](https://www.gulshankumar.net/how-to-serve-webp-format-images-in-wordpress/#nginx), ShortPixel
* Paid: Optimizer ($9.5/mo), for automated CSS/JS minify, WebP.

== 💕 Compatibility 💕 ==
* AMP (official plugin only)
* Autoptimize, if AO clear cache we will do purge everything automatically.
* bunny.net, the official plugin is still helpful for static files with perma-cache.
* [Forget Spam Comment](https://wordpress.org/plugins/forget-spam-comment/)
* WebP Express
* Fluent Form, Contact Form 7
* RankMath

== Things completely bypassed from Caching ==
* XML Sitemap
* WordPress Search
* REST API Route
* Pagination (example.com/blog/page/n/)

= Requirements =
* bunny.net API key
* Cloudflare DNS

== How to use ==

* Enter bunny.net API
* Click on Setup Pull Zone
* Update CNAME records at Cloudflare DNS
* Click on Install SSL button
* Done.
* For more information, please check 'Help' sections inside plugin UI.

== Screenshots ==
1. Edge Caching Setup in just two steps
2. Bunnyfied message shows setup completed
3. Restrict Access to Origin to the Authorized Zone
4. Enjoy faster TTFB globally

== Disclaimer ==
* You should 'delete' Pull Zone self after plugin deactivation.
* This plugin doesn't collect any personal information. 
* In no shall events plugin will be liable for any loss or misconfiguration.
* This is an **unofficial** plugin for bunny fans. Except for account and billing queries, please do not bother team bunny.net by asking support for this plugin instead consider using below official support channel.

== Official Support Channels ==

- Create a topic at [WordPress Support Forum](https://wordpress.org/support/plugin/edge-caching-firewall-bunnycdn/)
- Or, ask at [GulshanForum](https://help.gulshankumar.net)
- Or, tweet [@TheGulshanKumar](https://twitter.com/TheGulshanKumar) 

Share with your friends! Thank you. 🙂


== Installation ==

To install this plugin from WordPress Dashboard

1. Go to Plugins menu > Add new
1. Search for Edge Caching and Firewall with BunnyCDN
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enter bunny.net API Key and setup the plugin
1. Update the DNS records at Cloudflare as suggested by plugin
1. Restart the browser.


To install this plugin manually

1. Download the 'Edge Caching and Firewall with BunnyCDN'
1. Upload `Edge Caching and Firewall with BunnyCDN` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enter bunny.net API Key and setup the plugin
1. Update the DNS records at Cloudflare as suggested by plugin
1. Restart the browser.

== Frequently Asked Questions ==

= Will it work on my site? =
bunny offers 14 days free trial. Why not give a try yourself?

= Why www or subdomain is optionally recommended? =
An actual CNAME can be much faster and accurate in routing compared to ANAME or CNAME Flattening . See the [case-study](https://www.gulshankumar.net/using-cloudflare-dns-without-cdn-or-waf/#Should-you-use-Cloudflare-DNS). Or, if you are on other subdomain then it's perfectly fine. The recommended DNS is Cloudflare.

= How to clean uninstall this plugin? =
1. First, Deactivate the plugin. 
2. Point back to original Hosting IP in your DNS.
3. Login to bunny.net account and delete created pull zone. Close account.
3. Deactivation/re-activation will drop saved info, except the static 'Origin Access Token' hash to allow graceful re-installation. Optionally, you may run `delete_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token' );` to fully clean.

== Changelog ==

= 1.0.2 =
* [BUG FIX] Prevent droping settings after update.

= 1.0.1 =
* [IMPROVEMENT] Updated instructions for cert installations

= 1.0.0 =
* Initial release