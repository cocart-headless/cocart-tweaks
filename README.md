# CoCart Tweaks

This is a WordPress plugin, meant as a starting point for developers to tweak [CoCart](https://cocart.xyz) using WordPress filters and hooks.

## Requirement

You will need to be using CoCart **v2.0.0** and up before applying any tweaks.

## Installation

Create a `/co-cart-tweaks/` folder in `/wp-content/plugins/` and simply drop the `co-cart-tweaks.php` file into it. Then go to the Plugins page in your WordPress dashboard and activate it.

## Setup

Open the `co-cart-tweaks.php` file and take a look at the `__construct()` function. You will notice that all the calls to `add_filter()` and `add_action()` are commented out. So, at the moment the plugin does nothing even though it's activated. To enable a filter, simply uncomment the appropriate `add_filter()` or `add_action()` line, but please make sure the corresponding function further down in the plugin's source has been adjusted to fit your needs.

Most of the examples in the plugin's handler functions **will need editing** before they can be used.
