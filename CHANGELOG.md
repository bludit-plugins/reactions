CHANGELOG
===========

Version 1.0.1 - Stable
----------------------
-   Bugfix: [PLUS] The emojione icons are not shown on the dashboard widget.

Version 1.0.0 - Stable
----------------------
-   Info: The new version requires at least Bludit 3.9.0 & PHP 7.2.0!
-   Add: New Like / Dislike and 3 / 5 Stars rating systems.
-   Add: Each single rating system stores the votes separately (3 databases).
-   Add: Disable Reactions too, when comments are disabled (configurable).
-   Add: The reaction system is now available within the global variable `$reactions`.
-   Add: The plugin instance is now available within the global variable `$reactions_plugin`.
-   Add: Use the new `siteSidebar`, `pageBegin`, `:before`, `:variable` injection hooks.
-   Add: The option to disable the hook injection at all (perfect for custom themes).
-   Add: A new 'Design' option, which allows to change the used design or disable it at all.
-   Add: The new Bot-Protection method 'Session Cookie Test'.
-   Add: The new Bot-Protection method 'Honey Pot Field'.
-   Add: The new Bot-Protection method 'Filter using HTTP Referrer & User Agent'.
-   Add: The option to submit the reaction form using AJAX instead of a default HTTP POST request.
-   Add: New translation strings using the `s18n` system.
-   Add: The german translation files `de`, `de_AT`, `de_CH` and `de_DE`.
-   Add: EXPERIMENTAL Function to inject the vote counter on the administration content list.
-   Update: The Reaction panel is now a form instead of just a link.
-   Update: The Emoji label is now optional.
-   Update: The Reactions Panel title is now optional.
-   Update: Moved the reactions data to the plugins workspace (instead of using a custom field).
-   Update: The styles has been completely adapted incl. the new frontend hook positions.

### Exclusive Plus Features
-   Add: Collect & Log all "New Reactions".
-   Add: A new Dashboard Widget, which shows the "New Reactions" Log.
-   Add: Store user votes as Cookie, instead of using the SESSION.
-   Add: Store user votes on your SERVER, using the hashed IP address, instead of using the SESSION.
-   Add: A cookie field name/value handler for "Allow Cookie" settings (to fulfil GDPR rules).

Version 0.1.2 - Alpha
---------------------
-   Bugfix: The Reactions Panel didn't worked on Child-Pages. Thanks to [#1](https://github.com/pytesNET/reactions/issues/1)

Version 0.1.1 - Alpha
---------------------
-   Add: A new option to change the Panel title.
-   Add: The new option to change the used frontend hook metod.
-   Add: Inject the content after the page's content (without and frontend hook).
-   Update: Add scrollable container for smaller screens.
-   Bugfix: Reactions Panel gets shown on custom 404 error pages.

Version 0.1.0 - Alpha
---------------------
-   Initial Version
