# Edge Caching and Firewall with BunnyCDN

### Make your site globally faster and secure like never before

**Reverse-Proxy Edge Caching & DDoS protection in 90 seconds setup.** 😎

> The use of Bunny.net API key is subject to its applicable 'Pricing', 'Terms', and 'Acceptable Usage Policy'.

### 🎁 Your Benefits
- Achieve TTFB ~35ms globally with Reverse-Proxy Edge caching.
- Hide the Origin IP to prevent DDoS attacks.
- Prevent bypassing Bunny Proxy with Origin Access Token.
- Automatically bypass HTML cache for logged-in users.
- Fix latency issues for anonymous visitors far from your server.
- Easy edge caching at BunnyCDN Edge with auto-purge.
- Cache all content at one hostname, including posts, pages, and RSS feeds with automatic URL purging.

## 🥉 Salient Features
- Retain the admin toolbar while logged in (hidden for logged-out users).
- Automated purging for optimal Cache HIT ratio.
- Purge relevant URLs on post status changes and new comments.
- Cache purging for all site-level changes.
- Ensure edge and browser caching for static files, even when logged in.
- Automatically bypass HTML cache for dynamic requests.
- Ignore tracking-system query strings for better Cache HIT ratios.
- Purge cache for a single page or the entire site via the admin toolbar.
- GDPR compliant.

## Intelligent Cookies-Session Handling 👏
> Crafted with care. 😎
- Displays comment moderation status to the comment author.
- Bypasses caching for logged-in users with 'Remember Me' checked.
- Serves cached response instantly after logout.
- Bypasses caching for 'Easy Digital Downloads' & 'WooCommerce' carts.

## Want to Serve Next-Gen Images? It's Compatible! ✌️
- **Free**: Vary Cache, to extend support for [WebP Express](https://www.gulshankumar.net/how-to-serve-webp-format-images-in-wordpress/#nginx), ShortPixel.
- **Paid**: Optimizer ($9.5/mo), for automated CSS/JS minification and WebP.

## 💕 Compatibility 💕
- AMP (official plugin only)
- Autoptimize (automatically purges cache on clear)
- Bunny.net (official plugin useful for static files)
- [Forget Spam Comment](https://wordpress.org/plugins/forget-spam-comment/)
- WebP Express
- Fluent Form, Contact Form 7
- RankMath

## Things Completely Bypassed from Caching
- XML Sitemap
- WordPress Search
- REST API Route
- Pagination (e.g., example.com/blog/page/n/)

## Requirements
- Bunny.net API key
- Cloudflare DNS

## How to Use
1. Enter Bunny.net API key.
2. Click on "Setup Pull Zone".
3. Update CNAME records at Cloudflare DNS.
4. Click on "Install SSL" button.
5. Done! For more information, please check the 'Help' section in the plugin UI.

## Screenshots
1. Edge Caching Setup in just two steps.
2. Bunnyfied message shows setup completion.
3. Restrict Access to Origin to the Authorized Zone.
4. Enjoy faster TTFB globally.

## Disclaimer
- You should 'delete' Pull Zone after plugin deactivation.
- This plugin does not collect any personal information.
- The plugin will not be liable for any loss or misconfiguration.
- This is an **unofficial** plugin for Bunny fans. For account and billing queries, please do not contact Bunny.net support.

## Installation

### From WordPress Dashboard
1. Go to Plugins menu > Add New.
2. Search for "Edge Caching and Firewall with BunnyCDN".
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Enter Bunny.net API Key and set up the plugin.
5. Update the DNS records at Cloudflare as suggested by the plugin.
6. Restart your browser.

### Manual Installation
1. Download the "Edge Caching and Firewall with BunnyCDN".
2. Upload the folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Enter Bunny.net API Key and set up the plugin.
5. Update the DNS records at Cloudflare as suggested by the plugin.
6. Restart your browser.

## Frequently Asked Questions

### Will it work on my site?
Bunny offers a 14-day free trial. Why not give it a try yourself?

### Why is www or subdomain optionally recommended?
An actual CNAME can be much faster and more accurate in routing compared to ANAME or CNAME flattening. See the [case study](https://www.gulshankumar.net/using-cloudflare-dns-without-cdn-or-waf/#Should-you-use-Cloudflare-DNS). If you are on a different subdomain, that's perfectly fine. The recommended DNS is Cloudflare.

### How to clean uninstall this plugin?
1. Deactivate the plugin.
2. Point back to the original Hosting IP in your DNS.
3. Log in to your Bunny.net account and delete the created pull zone. Close your account.
4. Deactivation/re-activation will drop saved info, except for the static 'Origin Access Token' hash for graceful re-installation. Optionally, you may run `delete_option( 'edge_caching_and_firewall_with_bunnycdn_origin_access_token' );` to fully clean.

Share with your friends! Thank you. 🙂 

## Changelog

### 1.0.2
- [BUG FIX] Prevent dropping settings after update.

### 1.0.1
- [IMPROVEMENT] Updated instructions for certificate installations.

### 1.0.0
- Initial release.
