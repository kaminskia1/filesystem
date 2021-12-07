// Bind ajax request and disable default href's
export function ajax(x = null) {
    if (x === null) x = Array.from(document.getElementsByTagName("a"));
    x.forEach(element => {
        if (element.getAttribute("data-ajax") === "true" && element.getAttribute("data-bound") !== true) {
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

                        // Run (this) function across any new ajax supporting <a>'s in the newly-retrieved data.
                        ajax(document
                            .getElementsByClassName(element.getAttribute("data-ajax-target"))
                            .item(0)
                            .getElementsByTagName("a")
                        );


                    });
            });
            element.setAttribute("data-bound", true)
        }
    })
}