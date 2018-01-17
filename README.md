# SkyBOX Checkout

This module allows you to integrate SkyBOX Checkout.

## Manual install

The module directory must be into locate ```app/code/Skybox/Checkout``` directory.

- From a .zip file, decompress it into before directory.
- From a git repository, clone the branch assigned it into before directory:
  - `cd app/code/Skybox/Checkout`
  - `git clone -b [branch] https://[account]@bitbucket.org/skylogistics/magento2.git .`

## Installation Instructions

1. Enable module:
    `bin/magento module:enable Skybox_Checkout`

2. Update database schema without clearing compiled code:
    `magento setup:upgrade --keep-generated`

3. Configure Credentials:
     Go to Magento Admin > Stores > Configuration > SkyBox Checkout.
