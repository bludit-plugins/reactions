/*
 |  Reactions   Let your users react to your content.
 |  @file       ./system/js/admin.content.js
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
(function() {
    "use strict";

    // Ready?
    jQuery(document).ready(function() {
        if($("#pages").length === 0){
            return false;
        }

        // Add Thead
        $('<th class="border-0 text-center">Votes</th>').insertAfter($("#pages thead tr th").get(1));
        $('<th class="border-0 text-center">Votes</th>').insertAfter($("#static thead tr th").get(1));
        $('<th class="border-0 text-center">Votes</th>').insertAfter($("#sticky thead tr th").get(1));

        // Add Tbody
        $('#pages tbody tr, #static tbody tr, #sticky tbody tr').each(function() {
            var page = $(this).find("a:first-child");
            var slug = page.attr("href").replace(/(?:.*?)edit-content\/(.*)$/, "$1");

            if(!(slug in REACTIONS_DATA)) {
                REACTIONS_DATA[slug] = 0;
            }
            $('<td class="text-center">' + REACTIONS_DATA[slug] + '</td>').insertAfter($(this).children().get(1));
        });
    });
}());
