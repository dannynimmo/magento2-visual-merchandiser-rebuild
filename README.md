# Rebuild Visual Merchandiser Categories for Magento 2

Rebuild Magento 2 Visual Merchandiser categories using the CLI tool or a scheduled task.

> :warning: **NOTE:** This module is not fully tested, please make sure to test this on a non-production system first, and use at your own risk!

## Installation via .zip

 1. Download the zip file for the master branch on Github
 2. Extract the Dakzilla directory in the `app/code` directory of your Magento 2 EE project
 3. Run the `php bin/magento setup:upgrade` command to enable the module
 
## Installation via Composer

```
composer require dakzilla/magento2-visual-merchandiser-rebuild

php bin/magento setup:upgrade
```

## Usage

### Rebuild all smart categories from the command line

```bash
bin/magento catalog:visual-merchandiser:rebuild
```

### Show information about smart categories

```
bin/magento catalog:visual-merchandiser:show
```

The above command should display a nifty table like this one

![Information table](http://i.imgur.com/5yNW9Y8.png)

### Enable and set cron job in admin 

In Magento Admin, in the Visual Merchandiser options, you can set a cron expression to be used to rebuild the smart categories on schedule. The example below will execute the job every day at 1:45 AM. If you're not sure, use [Crontab Generator](https://crontab-generator.org/) to create a valid cron expression.
![Admin cron options](http://i.imgur.com/qjcJX01.png)

## Compatibility

This module was tested on Magento 2 Enterprise Edition versions 2.1.5 to 2.1.7. Compatibility with older versions is not guaranteed, but should not be an issue anyhow. 

## Changelog

### [0.2](https://github.com/dakzilla/magento2-visual-merchandiser-rebuild/releases/tag/0.2) - 2017-06-24
* Fixed an app state bug with the latest version of Magento 2
* Internal refactor
* Added the `catalog:visual-merchandiser:show` command to display a useful information table about your smart categories
* Added a configurable cron job in admin. From the Visual Merchandiser options, you can now set a cron job to rebuild smart categories automatically. Make sure your Magento cron in configured correctly!

### [0.1.1](https://github.com/dannynimmo/magento2-visual-merchandiser-rebuild/releases/tag/0.1.1) — 2017-03-17
* Fixed major issue where URL keys were missing from collection

### [0.1.0](https://github.com/dannynimmo/magento2-visual-merchandiser-rebuild/releases/tag/0.1.0) — 2017-02-28
* Released!
