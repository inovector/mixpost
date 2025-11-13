# Changelog

All notable changes to `mixpost` will be documented in this file.

## 2.4.0 - 2025-11-13

**Miscellaneous**

- Added support for Facebook Graph API v24.0
- Migrated fonts from ttf to woff2 for better performance

## v2.3.0 - 2025-06-06

**Miscellaneous**

- Added support for Facebook Graph API v23.0

## v2.2.0 - 2025-05-28

**Changes**

- Show the status of FFmpeg on the status page.

**Miscellaneous**

- Upload video without FFmpeg
- Support Laravel 12
- Upgraded to Tailwind CSS 4

## v2.1.3 - 2025-03-06

**Fixes**

- Fixed non-registered `PruneTemporaryDirectory` command

## v2.1.2 - 2025-02-21

**Fixes**

- Fixed video thumbnail generation when no keyframe is available at the requested time.

## v2.1.1 - 2025-02-12

**Fixed**

- Fixed URL weight in the post text for Mastodon

## v2.1.0 - 2025-01-29

**Changes**

- Added support for Facebook API v22.0

## v2.0.1 - 2025-01-20

**Fixes**

- Fixed style of media file name

## v2.0.0 - 2025-01-18

**New features**

- Added email notification for broken social account connections
- Begin processing analytics immediately after connecting a social account.
- Added system status page
- Added system logs page

**Fixes**

- Fixed filter by tags for calendar
- Fixed issue with X video/GIF media uploads
- Fixed issue with deleting temporarily downloaded media for the Facebook platform

**Changes**

- Service support status "Active/Inactive"
- Shows media file title
- Improved performance Emoji picker render
- Improved post validator
- Optimized schedule commands
- Improve editor and media layout for post page
- `uuid` instead `id` of resource in the URL

**Miscellaneous**

- Support Laravel 11
- Support Facebook API v21.0
- Support Carbon 3
- New command and schedule for pruning the temporary media directory.

## v1.7.2 - 2024-08-16

**Miscellaneous**

- Removed Unsplash doc link.

## v1.7.1 - 2024-06-14

**Fixes**

- Fixed Facebook service version validation.
- Fixed confirmation process for removing post versions.

## v1.7.0 - 2024-06-06

**Changes**

- Support `v20` for Facebook API

**Miscellaneous**

- Expanded compatibility of the post keyword filter with additional database versions

## v1.6.0 - 2024-05-09

**Fixes**

- Fixed media file item in the Safari browser
- Calendar Month: Fix timezone with (-)
- Calendar Month: Get posts for prev&next month

**Changes**

- Changed documentation URL

**Miscellaneous**

- Support `s3` disk for X (Twitter)

## v1.5.2 - 2024-04-12

**Fixes**

- Fixed pasting empty lines to the editor
- Fixed Mastodon post count of text characters
- Fixed loading media files in the calendar

**Changes**

- Renamed Twitter to X (icon changed)

**Miscellaneous**

- Improved design

## v1.5.1 - 2024-04-05

**Miscellaneous**

- Removed Facebook deprecated`page_engaged_users` metric
- Improved design CSS

## v1.5.0 - 2024-03-05

### Added

- Added support for Facebook v19.0

### Fixed

- Fixed image media library in Safari

### Miscellaneous

- Optimized posts query

## v1.4.0 - 2023-12-02

- Added Unsplash trigger download Job
- Added Unsplash credit attributes
- Optimize provider preview checking video format
- Fixed vulnerability on download media
- Fixed media library item width

**Internal Changes**

- Modified value of `external_media_terms` config

## v1.3.2 - 2023-08-15

- Fix Services when the form is undefined (Mastodon)

## v1.3.1 - 2023-07-12

- Fix logout redirect away

## v1.3.0 - 2023-06-29

- Refractory Services class
- Put Jobs into the social provider folder
- Add `clear-services-cache` command
- Fix modal z-index
- Fix alert configured service
- Fix missing day value of audience report

## v1.2.0 - 2023-05-31

- Twitter API Refactory
- Support Facebook API v.17 (added "business_management" scope)
- Add a "docs" link to the service form
- Add User menu in the sidebar
- Support update profile information
- Support update password
- Social Providers' Rate-Limit optimized
- Customize the error page
- Link to the post on the social platform
- Improve post validation
- Improve preview post
- Improve connecting account entities
- Fix calendar posts order
- Fix account long name
- Fix post tag creation
- Show the current version in the footer of the sidebar
- Prefill the body on creating a post by URL ?body=the text
- Add minor improvements and bug fixes

## v1.1.3 - 2023-03-18

Fix & prevent errors when the app tries to decrypt data with a new key.

## v1.1.2 - 2023-03-16

- Changed the text of the installation command
- Fix Progressbar
- Optimize the scope of the Facebook provider

## v1.1.1 - 2023-03-12

Fix Calendar Month on Safari browser.

## v1.1.0 - 2023-03-09

- Upgraded to Inertia v1
- Fix Mastodon upload media
- Added new command **php artisan mixpost:create-mastodon-app {server}** for a Mastodon server
- Load Google font locally (EU GDPR)
- Fix responsive for page Services
- Added scroll on mobile for list services on page Services
- Fix the post media list on the Safari browser

## v1.0.2 - 2023-03-06

Fixed searching posts by keyword

## v1.0.1 - 2023-03-02

Dashboard Analytics
Posts
Calendar
Media Library
Accounts
Settings
Services

## v1.0.0 - 2023-03-02

- Dashboard Analytics
- Posts
- Calendar
- Media Library
- Accounts
- Settings
- Services
