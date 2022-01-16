// Bind ajax request and disable default href's
export function ajax(x = []) {

    const $ = require('jquery');

    if (x.length === 0) x = Array.from(document.querySelectorAll("[data-ajax=true]"));
    x.forEach(element => {
        if (element.getAttribute("data-ajax") === "true" && element.getAttribute("data-ajax-bound") !== true) {
            // Remove default <a> redirects, as javascript is enabled
            element.addEventListener("click", e => { e.preventDefault(); } );

            // Convert into ajax request / load
            element.addEventListener("click", async () => {

                // Fetch the <a>'s href. Set XMLHttp so symfony recognizes it as ajax.
                fetch(element.getAttribute("href"), {headers: {'X-Requested-With': 'XMLHttpRequest'}})

                    // Grab text from response
                    .then(response => response.text())

                    // Set target data, and call (this) on it
                    .then(responseText => {

                        // Set targeted element's body to the response
                        document
                            .getElementsByClassName(element.getAttribute("data-ajax-target"))
                            .item(0).innerHTML = responseText;

                        /** @TODO: Run all desired events on a loop with method variable of all objects */

                        // Run (this) function across any new ajax supporting <a>'s in the newly-retrieved data.
                        ajax(document
                            .getElementsByClassName(element.getAttribute("data-ajax-target"))
                            .item(0)
                            .querySelectorAll("[data-ajax=true]")
                        );

                        // Register all popup events on immuted elements
                        /** @TODO: Convert "a" to all elements */
                        const Popup = require('../scripts/popup');
                        Popup.register(document
                            .getElementsByClassName(element.getAttribute("data-ajax-target"))
                            .item(0)
                            .querySelectorAll("[data-popup=true]")
                        );

                        // Rewrite the url
                        window.history.pushState("", document.title, element.getAttribute("href"))

                    });
            });
            element.setAttribute("data-ajax-bound", true)
        }
    })
}