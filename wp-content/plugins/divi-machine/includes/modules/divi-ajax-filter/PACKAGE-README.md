# Packaging this plugin via the CLI

Before proceding with the below steps, you must ensure you have ran `npm install` - you will need node version 14.0.0

Once you have done `npm install` you will also need to run `npm install -g grunt-cli`. Note: You will only need to do this once on your system as it installs globally.

## Steps to follow before packaging on each release
- Update version number in the following places
- - package.json
- - main plugin file
- - changelog
- Add release date to changelog
- Minify JS and CSS files
-- https://www.toptal.com/developers/javascript-minifier
-- https://www.toptal.com/developers/cssminifier

## Packaging process

### The whole lot
- Running `grunt` will do the following
- 1) Create a package with the filename `[plugin-name].[version].zip` to be distributed via Divi Engine
- 2) Replace `d_e` with `m_a` in `divi-ajax-filter.php`
- 3) Create a package with the filename `[plugin-name].zip` to be distributed via Elegant Marketplace
- 4) Replace `m_a` with `d_e` in `divi-ajax-filter.php`
- 4) Copy the DE Package to BodyCommerce
- 5) Copy the DE Package to Machine

The zip files are now ready to be uploaded to the site via FTP and WooCommerce to be updated. 

## Just AF via DE
Run `grunt depkg` to create a package ready for distribution via Divi Engine

## Just AF via ETM
Run `grunt etmpkg` to create a package ready for distribution via Divi Engine

## Just AF to BC
Run `grunt bc` to create a package and copy this to BC

## Just AF to MACH
Run `grunt mach` to create a package and copy this to MACH

*The expected package time for the `grunt` command is 9:00m*


### PETE NOTES
Grunt 
1) Package up Ajax Filter for DE
2) Package up Ajax Filter for MA
3) Move to folder ../_Releases/Divi Ajax Filter/Version/
4) Copy DE version to Machine & BC
5) Package up Machine
6) Move to folder ../_Releases/Divi Machine/Version/
7) Package up BodyCommerce
8) Move to folder ../_Releases/Divi BodyCommerce/Version/