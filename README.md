# UM Ads Free Cookies
Extension to Ultimate Member for creating ads free cookies supporting the Ezoic plugin.

## Settings 
UM Settings -> Access -> Other

1. Meta Key Start time - Name of the meta key with start time for ads free for each user.
2. Start time usage - Click checkbox for start time for ads free from next page display by the user if meta_key empty. Unchecked you must give the starttime as a Unix timestamp.
3. Cookie Name and Value - Comma separated
4. Role IDs and Number of Days - One Role per line and RoleID and number of days colon separated.

Default values if you leave fields empty:
1. ads_free_start_time
2. unchecked
3. um_ads_free_cookie, ads_free_cookie_value
4. Example: um_prospect: 30

## Reference
https://support.ezoic.com/kb/article/how-can-i-disable-ads-on-a-page  Section: Alternative Rule Types - To Exclude by Cookie
## Installation
Install by downloading the ZIP file and install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
Activate the plugin.
