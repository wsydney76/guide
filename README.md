# Guide Plugin

Hint: Moved from module to plugin, as a temporary workaround before porting all of this to
the 'official' guide plugin.


# Setup Guide

## Plugins

Required Plugins:

* Redactor Rich Text Editor
* Field Manager. Can be uninstalled after importing the field definitions.

## Fields

Create a field group called 'Guide'

Import fields.json via Plugin Field Manager

## Section

Doing this manually is faster...

So create a 'structure' section with handle 'guide' and attach the imported fields to its
single entry type.

## Assets

Create a volume with hande 'guide'

## Content

Create the required guide entries

Recommended: Entries with the slug guide, content, assets

* Handle of volumes has to match the folder name,
e.g. Handle 'images' => Folder '@webroot/images'
