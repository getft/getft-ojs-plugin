# GetFTR Integration Plugin for OJS

## Overview
The GetFTR Integration Plugin adds [GetFTR (Get Full Text Research)](https://getfulltextresearch.com) functionality to your [Open Journal Systems (OJS)](https://pkp.sfu.ca/ojs/) installation. 

This plugin automatically adds the [GetFTR Drop-in Button](https://docs.getfulltextresearch.com/latest/integrators/dropin-button) into your journals pages and article landing pages, signalling where full text is available and providing streamlined access.

## Features

- **Seamless Integration** – Automatically embeds the GetFTR Drop-in Button
 in article list items and individual article pages.
- **Real-time Access Checks** – Displays the availability of full text via GetFTR for users with institutional access or Open Access content.
- **Configurable** – Admins can enable/disable the integration and set the GetFTR integratorId.
- **Lightweight** – Designed to be fast, standards-compliant, and compatible with modern OJS.

## Requirements

- **OJS version**: 3.5+
- **GetFTR integratorId**: A valid GetFTR Integrator ID is required.  
  To obtain one, please [contact us](#contact).  
  Learn more: [GetFTR Integrator Documentation](https://docs.getfulltextresearch.com/latest/integrators/overview)

## Installation - OJS Plugin Gallery (recommended)

1. On your OJS site go to `Settings` > `Website` > `Plugins` > `Plugin Gallery`
2. Search for "GetFTR" plugin and if found, install

## Installation - manual
0. Download the code from GitHub
1. Enter the administration area of ​​your OJS website through the **Dashboard**.
2. Navigate to `Settings` > `Website` > `Plugins` > `Upload a new plugin`.
3. Under **Upload file** select the **downloaded archive file**.
4. Click **Save** and the plugin will be installed on your website.

## Configuration Options

| Setting | Description |
| ------- | ----------- |
| integratorId | The GetFTR integrator ID used to access the GetFTR service. |

## Contact
For enquiries regarding usage, support, bugfixes, or comments please email: support@getfulltextresearch.com