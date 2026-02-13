INSTALLATION
1) unzip simple_map_locator.zip
2) copy all files and directories into your hosting server root directory or subdirectory
3) access to http://yourserver/directory/install/start.php
4) follow the wizard and complete the installation

UPDATE
0) Take backup of files and database of the previous version
1) Go to the update menu
2) Click UPGRADE NOW!
or
1) Unzip the new version of simple_map_locator.zip
2) Upload new versions of files by replacing them in the directory of your hosting server being careful not to delete existing contents
3) Logout from backend and re-login
N.B. no need to run the installer again

Changelog
4.0
- added the ability for markers to open the details window or not
- added the ability for markers to view the popup or not
- added the ability to set the marker popup image height
- added the ability to set the marker popup background and color
- added more bulk actions for markers
- upgraded the map rendering engine
- organized the markers edit page into tabs
- improved the import of the marker file exported from another map
- fixed categories not showing on adding a new marker from map
- fixed hours and description font sizes
3.9.1
- added the ability to auto-approve markers added from map
- added the ability to select multiple categories into markers added from map
- fixed scroll issue on sheet detail on mobile
- improved security
3.9
- added the ability to draws geometries on map
- added shortcuts to simplify adding extra field values
- added bulk delete markers
- extended search for all fields
- updated map access graph into statistics
- fixed geojson colors not being applied
- fixed an issue filtering by category sometimes gave empty results
3.8
- added Globe view
- added Categories Filter Type (AND / OR)
- added some graphical changes to the backend
- added the ability to share and open the link of a map into the Publish section
- added the ability to set meta tags of the map
- fixed an issue with the default language
3.7
- added weather
- added Indonesian language
- added the ability to massively assign an icon style to markers from the map or category
- now you can drag the marker to change its position on the add/edit marker sections
- when you add a new marker the map is now centered based on the previously added markers
- update the core map library
- fixed an issue with search box when geolocation is on
3.6
- added support to geoJSON
- added pop-up details when moving between markers in the story
- added an option to publish the map as the first page of your domain
- added the possibility to set the order of the markers by priority, name or city
- when adding a new marker from the map you are now asked whether to use your position or not
- fixed search issue when geolocation is on
3.5
- added featured markers
- added the ability to highlight the position of the markers when you hover the mouse over the search results
- fixed a flickering issue when there are many icons
- fixed an issue preventing review texts from wrapping
3.4
- now adding a marker from the map takes the default style set in the settings
- added the possibility to select the category in the new markers mode on the map
- added the possibility to delete images in the new markers mode on the map
- fixed an issue with editor permissions
- fixed add marker not working when geolocation is enabled
- fixed an issue that prevented the display of the marker list when the city search was active
3.3
- added Korean, Thai languages
- added the ability to search location / city into maps
- added attributions for maps
- added the possibility to set for each marker the zoom level in which it must appear
- added default style parameters for markers in map settings
- added the ability to duplicate markers
- increase quality of image icons
- fixed an issue on geolocation
- fixed an issue for some systems not displaying the markers list on the backend
3.2
- redesigned the search box which is now always visible on the map
- added the story feature that allows you to scroll through the markers in a narrative way
- added the ability to disable/enable search, list and categories filter
- added the ability to change font for maps
- added the ability to change the default view for maps (street or satellite)
- added am option to set the quality of the map
- added an option to activate geolocation on map load
- added an option to set the default zoom for geolocation
- increased number of extra fields to 20
- now import excel can also update markers if the id is provided
- improved the click of markers on mobile devices
- improved the performance of the list and the search with many markers
- fixed an issue on saving settings
- fixed an issue that prevented some markers from displaying in the backend
3.1
- restyled the viewer
- added controls arrows to loop through the markers
- added the ability to sort markers
- added the ability to add markers directly from maps
- added the validation of the markers inserted from the map
- added the ability to individually disable directions, street view and reviews buttons for markers
- added a customizable button to each markers that open a modal with an html content such iframe text or others
- added toggle button to show or hide marker connections
- on the map with a single marker some controls are hidden
- hide connection between markers not visible
3.0
- added fullscreen mode
- added parameter to set the density distance that allows you to group markers together
- added the possibility to center the map on a selected marker
- added edit next / previous marker buttons into edit marker's page
- redesigned the selection of marker icons in the backend
- added editor role with permissions
- added captcha verification for reviews
- added marker zoom's option into maps to control the zoom level when the marker is clicked
- removed empty image space from popup and sheets
- the icons in the marker detail tab have been moved and grouped under the name
- added street view link
- added popup to marker connections with customizable title and description
- added edit button shortcut near map selector
2.9.2
- fixed an issue with category filters
2.9.1
- added customizable items on backend's footer
- added Romanian language
- move delete into the marker's list
- fixed bug on adding marker
2.9
- added the ability to update the application automatically (only for administrators)
- added marker's connections to draw a line between them
- added custom map style
- added zoom controls to map
- added the ability to set background, color and size of each individual marker
- added icon preview in marker's list
- added the ability to set marker's custom fields values with html content
- added the ability to duplicate maps
- added Swedish language
- modified the import template excel with all the markers parameters
- fixed bug hide rating when review is disabled
- fixed bug on import markers
- fixed upload of icon with size too small
- fixed encoding on export csv
2.8
- added multi-language backend / viewer
- added language switcher for backend
- added the ability to change language for maps
- added the ability to change font for backend and viewer
- added the ability to change theme color for backend
- added custom logo for backend
- added custom image for login page
- added the ability to edit profile for change username, language and password
- added custom css editors for backend and viewer maps
2.7
- added whatsapp field to markers
- added preview box when hover the markers
- added pinned flag to markers to shows on top of the list
- added sub categories
- added the ability to assign a marker to multiple categories
- added categories in exported markers file
- added statistics
- added gif compatibility for icon markers
2.6
- added a button to toggle categories filter
- fixed map not showing
2.5
- fixed a bug that deletes the icon library when saving marker
- added the ability to administrators to change the user owner of the map
- added markers size
- added review system with ratings
2.4
- added cursor pointer to markers
- click the grouped markers to expand them
- added url variables &coord=xxxxx,yyyyy&zoom=z (xxxxx = lat, yyyyy = lon, z = zoom)
2.3
- improved search with also description and custom fields
- added direct links to markers and categories
- added satellite view switcher
2.2.1
- fixed import of markers
2.2
- fixed bug on generating thumbs
- fixed bug on duplicating images when saving marker multiple times
- added density color toggle to maps to displays different colors based on the density of marker grouping
- added checkbox and counts on map's categories
- reset the sort order of the marker list when geolocation is off
- added a flag to the categories to be selected as default
- the map is centered on the position of the first geolocation
- added password to protect the map
2.1
- added custom icon library
- added extra fields to markers
- website url in sheet is now clickable
- the address fields are now not mandatory
- adjust zoom on click categories to fit the markers
2.0.1
- fixed bug on sorting list
- fixed bug on search
2.0
- added categories to markers
- added filter by category on maps
1.9.1
- fixed a bug on geolocation not working on iframe
- ignoring not valid coordinates on viewer
1.9
- added an option to maps to auto show marker's list
- added default zoom option to maps
- added a search box in the marker's list
1.8
- added possibility to change single marker color
1.7
- added a button to convert marker's address to its coordinates
- fixed a bug on import excel markers
1.6
- added an active parameter to markers to enable or disable it
1.5.1
- added possibility to get coordinates with gps to set marker position
- added possibility to delete users
- bugfix on initial map loading
1.5
- added to dashboard a chart with num markers grouped by maps
- added possibility to export all markers of a map
- added possibility to activate / deactivate maps
- added friendly url to publish link with a custom url
- added google analytics integration
1.4.1
- minor bugfix on import markers
1.4
- import massive markers with excel
- added users menu backend (only for administrator): manage customers and administrators who can use the application
1.3
- added possibility to input lat / lon manually
1.2
- added marker icons (fontawesome)
- added directions button
- fixed gallery images
1.1
- maptiler integration
1.0
- initial release