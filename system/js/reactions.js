/*
 |  Reactions   Let your users react to your content.
 |  @file       ./system/js/reactions.js
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
;(function() {
    "use strict";

    /*
     |  AJAX HELPER
     |  @since  1.0.0 [1.0.0]
     |
     |  @param  string  The URL, which should be called.
     |  @param  string  The HTTP request method.
     |  @param  multi   The data, which should be passed,
     |  @param  callb.  The callback function.
     |  @param  multi   An custom <this> value for the callback function.
     |
     |  @return object  The XML Http Request instance.
     */
    function ajax(url, type, data, callback, self) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if(this.readyState === 4) {
                callback.call(self || this, this.responseText, this);
            }
        };

        xhr.open(type, url, true);
        xhr.setRequestHeader("Cache-Control", "no-cache");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        if(type === "GET") {
            xhr.send();
        } else {
            if(data instanceof FormData) {
                xhr.send(data);
            } else {
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(data);
            }
        }
        return xhr;
    }

    // Ready?!
    document.addEventListener("DOMContentLoaded", function() {
        "use strict";

        /*
         |  HANDLE FORM SUBMITs
         */
        var form = document.querySelector("form.reactions");
        if(form) {
            form.addEventListener("submit", function(event) {
                event.preventDefault();

                // Catch Vote
                if(this.querySelector("[data-submit='true']") === false) {
                    return;
                }
                var vote = this.querySelector("[data-submit='true']").value;
                this.querySelector("[data-submit='true']").removeAttribute("data-submit");

                // Prepare Request
                var action = this.getAttribute("action")
                var method = this.getAttribute("method").toUpperCase();
                var formData = new FormData(this);
                    formData.append("reactions-vote", vote);

                // Call to Vote
                ajax(action, method, formData, function(response) {
                    var data = JSON.parse(response);

                    if(data.status === "success") {
                        var parse = document.createElement("DIV");
                            parse.innerHTML = data.message;

                        // Replace Form Content and re-prepare new buttons
                        this.innerHTML = parse.querySelector("form.reactions").innerHTML
                        prepareButtons();
                    }
                }, this);
            });
        }

        /*
         |  PREPARE FORM BUTTONs
         */
        function prepareButtons() {
            var buttons = document.querySelectorAll("form.reactions button");
            for(var i = 0; i < buttons.length; i++) {
                buttons[i].addEventListener("click", function() {
                    this.setAttribute("data-submit", "true");
                });
            }
        }
        prepareButtons();
    });
}());
