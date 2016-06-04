[![Latest Version on Packagist](http://img.shields.io/packagist/v/richardhj/contao-notification_center_member_selectable.svg)](https://packagist.org/packages/richardhj/contao-notification_center_member_selectable)
[![Dependency Status](https://www.versioneye.com/php/richardhj:contao-notification_center_member_selectable/badge.svg)](https://www.versioneye.com/php/richardhj:contao-notification_center_member_selectable)

# Member selectable messages for the notification_center extension for Contao Open Source CMS

Imagine, you provide nc messages (e.g. an order confirmation) with different gateways (like email or sms). This extension provides a front end module which lets the logged in member choose, which messages (e.g. only the sms message and/or only the email message) he wants to receive.

## Usage
Modify the messages of a particular notifiaction and tick the box "Is member selectable" (All messages without this box ticked will be sent anyway).
Create a new front end module "Manage messages" (can be found in the category "user"). Select the notifications, the member can manage with this module. Place the module in the front end (e.g. in the member area).

### Demonstration front end module
![Front end module](https://cloud.githubusercontent.com/assets/1284725/15799664/e29811a8-2a63-11e6-9bbf-e2be8569a09d.png)
