; ----------------
; Makefile for fbconnect
; ----------------
;
;
; ------------
; Core version
; ------------

core = 7.x

; ------------
; API version
; ------------

api = 2

; ------------
; Core project
; ------------

projects[drupal][version] = 7

projects[libraries][version] = 2.0-alpha2
projects[libraries][type] = module
projects[libraries][subdir] = contrib

; ------------
; Libraries
; ------------

libraries[facebook-php-sdk-v4][download][type] = "get"
libraries[facebook-php-sdk-v4][download][url] = "https://github.com/facebook/facebook-php-sdk-v4/archive/4.1-dev.zip"
libraries[facebook-php-sdk-v4][destination] = "libraries"

