# Smart Post Sync Plugin
![banner-772x250 (1)](https://github.com/user-attachments/assets/0eb949af-566c-4294-9789-da2db2d3e6f6)

WordPress Plugin for [Multidots](https://www.multidots.com/).

Smart Post Sync streamlines the process of connecting external data sources to your WordPress posts. With seamless API integration, this plugin automatically imports data and creates or updates posts on your site with minimal effort. Simplify your workflow and keep your content up to date effortlessly. Whether you’re syncing product information, blog content, or any other data, Smart Post Sync ensures a smooth and efficient workflow.


## Features

1. **Seamless API Integration:**
   Easily configure and manage API connections to import data into WordPress posts without technical complexities.
   
2. **Automated Data Syncing:**
Schedule automatic syncs at predefined intervals to keep your content updated in real-time.

3. **Custom Post Mapping:**
Choose which product details to sync, such as titles, descriptions, images, prices, and inventory levels.

Streamline content management and enhance your WordPress site with Smart Post Sync!


### Why Choose the Smart Post Sync Plugin?

**Simplified Integration:**
* Smart Post Sync takes the complexity out of API integrations, offering an easy setup and user-friendly interface that lets you sync external data effortlessly.

**Save Time and Effort**
* Automate the process of creating and updating posts, eliminating the need for manual data entry and saving valuable time.

**Maintain Accuracy**
* With customizable post mapping, ensure that the right data is pulled into the correct post elements, keeping your content organized and accurate.

**Stay Updated**
* Set automated sync intervals, ensuring that your website’s content is always fresh and relevant, without manual intervention.

**Enhanced Flexibility**
* Whether you’re syncing product details, blog content, or other external data, Smart Post Sync adapts to your unique needs, providing a reliable solution for diverse use cases.

### Quick Start

Clone or download this repository, change its name to something else (like, say, `md-optima`), and then you'll need to do a nine-step find and replace on the name in all the templates. **Please make sure to on capslock before start search and replace.**

1. Search for `smart-post-sync` the text replace with: `md-optima` .
2. Search for `smart_post_sync` the text replace with: `md_optima` .
3. Search for `SMART-POST-SYNC` the text replace with: `MD-OPTIMA` .
4. Search for `SMART_POST_SYNC` the text replace with: `MD_OPTIMA` .
5. Search for `Wp_Post_Sync` the text replace with: `Md_Optima` .
6. Search for `Smart Post Sync` the text replace with: `MD Optima` .
7. Delete `phpcbf.xml`, `phpcs.xml` and `composer.json` file from theme root directory.
8. Rename class file `smart-post-sync-plugin/inc/classes/class-smart-post-sync.php` to `smart-post-sync-theme/inc/classes/class-md-optima.php` .
9. Rename plugin folder `smart-post-sync-plugin` to `md-optima` .


### External services
This plugin connects to the Salsify API to sync product data. It sends product information and API keys when syncing data.
Service: Salsify API
Terms of Service: https://www.salsify.com/legal/terms-of-service
Privacy Policy: https://www.salsify.com/privacy-policy


### Changelog

1.0
* Initial release of the Smart Sync Post plugin.

### Upgrade Notice

1.0
Initial release. No upgrades available yet.


### Credits
Smart Post Sync is developed by Multidots. We appreciate the contributions from the open-source community.

### Support
For support or questions, please [open an issue](https://github.com/multidots/smart-post-sync/issues) or contact us via our website at [multidots.com](http://multidots.com/).

### License
This plugin is licensed under the GPLv2 or later license.

### See potential here?
<a href="https://www.multidots.com/contact-us/" rel="nofollow"><img width="1692" height="296" alt="01-GitHub Footer" src="https://github.com/user-attachments/assets/6b9d63e7-3990-472d-acb9-5e4e51b446fc" /></a>
