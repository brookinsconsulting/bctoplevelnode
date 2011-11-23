<?php /* #?ini charset="utf-8"?

# Here is an example menu.ini.append.php ini settings override file content
# I think you could / would need for this use case to provide a top menu entry
# to your custom top level node in the admin
#
# To use simply copy into your settings/override/menu.ini.append.php
# and uncomment the first level of comment characters.
#
#
##[MenuContentSettings]
## This list contains the identifiers of the classse
## that are allowed to be shown in top menues
## only use if needed, provided as a reference
## TopIdentifierList[]=customfolder
## TopIdentifierList[]=customtoplevelfolder

#[TopAdminMenu]
## This list contains menuitems of the top menu in admin interface
#Tabs[]=customcontent
# 
#[Topmenu_customcontent]
#URL[]
#URL[default]=content/view/full/4400
#URL[browse]=content/browse/4400
#NavigationPartIdentifier=ezcontentnavigationpart
##Name=Custom Content structure
##Tooltip=Manage the main content structure of the site.
#Enabled[]
#Enabled[default]=true
#Enabled[browse]=true
#Enabled[edit]=false
#Shown[]
#Shown[default]=true
#Shown[navigation]=true
#Shown[browse]=true
#PolicyList[]=2

*/ ?>