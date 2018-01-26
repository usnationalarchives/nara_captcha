# NARA Captcha

## Goal
This module plugin adds a captcha element that can be added to any form using the [Drupal Captcha Module](https://www.drupal.org/project/captcha). In addition to adding captcha, the module also helps identify text vs handwritten documents from the National Archive to help identify what documents can be easily transcribed. 

## Installation
### The Composer Way
In your composer file add the following two snippets to your Drupal 8 root Composer file. This will download the NARA Image Tool module, and Captcha. 
```
"repositories": [
  {
    "type": "vcs",
    "url": "git@github.com:agencychief/nara_captcha.git"
  }  
]
```
```
"require": {
  "agencychief/nara_image_tool": "dev-develop"
}
```
After running `composer install` enable the module, which will also enable Drupal Captcha. 

### The Non Composer Way
Clone this repository into your Drupal 8 module folder and enable the module. You will also need to download and add Captcha to your site for this work.

## Adding NARA Captcha to a form
Since this is a plugin for the Drupal Captcha module, you will need to add captchas though it's admin interface. Visitng `/admin/config/people/captcha/nara_captcha` after logging into your site will allow you to set any keys or paths that are required. 

The path `/admin/config/people/captcha/captcha-points` will list all form types on the site and allow you to add NARA Captcha as the challenge type.
