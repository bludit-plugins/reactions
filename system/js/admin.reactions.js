/*
 |  Reactions   Let your users react to your content.
 |  @file       ./system/js/admin.reactions.js
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
(function() {
    "use strict";

    jQuery(document).ready(function() {
        /*
         |  CHANGE WIDGET MODE
         */
        $('.reactions-mode .nav a').on("click", function() {
            var value = this.hash;
                value = value.substr(value.lastIndexOf("-") + 1);
            $("input[name='widget_mode']").val(value);
        });

        /*
         |  ENABLE DISLIKE BUTTON
         */
        $('#reaction-dislike').on("change", function() {
            var el = $('[data-reaction-form^="dislike-"]').find('.reaction-select,.select-toggle');
            el[this.checked? "removeClass": "addClass"]("disabled");
        });

        /*
         |  FORM LISTENER
         */
        $('[data-reaction-form] a').on("click", function(event) {
            event.preventDefault();

            var key = this.hash.substr(1).split(":");
            var val = key[1];
                key = key[0];

            // Set new Label
            var html = this.innerHTML;
            if(key.indexOf("icon") >= 0) {
                html = this.querySelector(".value-icon").outerHTML;
            }
            $('[data-reaction-form="' + key + '"] .reaction-select .select-value').html(html);

            // Set new Value
            $('[data-reaction-value="' + key + '"]').val(val);
        });

        /*
         |  INIT EMOJI ONE AREA
         */
        var emoji = $('[data-emoji-placeholder]').emojioneArea({
            search: false,
            tones: false,
            standalone: true,
            autocomplete: false,
            recentEmojis: false,
            pickerPosition: "bottom",
            events: {
                "onLoad": function(el){
                    var input = el[0].parentElement.nextElementSibling;
                    this.setText(input.getAttribute("value"));
                },
                "emojibtn.click": function(btn){
                    var input = this.source[0].nextElementSibling.nextElementSibling;
                    input.value = btn[0].getAttribute("data-name");
                }
            }
        });
    });
}());
