export function popup(x = [])
{
    const $ = require('jquery');
    const Swal = require('sweetalert2');

    if (x.length === 0) x = Array.from(document.querySelectorAll("[data-popup=true]"));
    x.forEach(element => {
        if (element.getAttribute('data-popup') === "true" && element.getAttribute('data-popup-bound') !== "true")
        {
            let cache;
            $(element).click((a) => {
                if (cache == null) {
                    fetch(a.currentTarget.getAttribute('data-popup-url'))
                        .then(response => response.text())
                        .then(response => {
                            cache = response
                            Swal.fire({
                                title: a.currentTarget.getAttribute('data-popup-title'),
                                html: cache,
                                width: 330,
                                showCancelButton: false,
                                showConfirmButton: false,
                            })
                        })
                } else {
                    Swal.fire({
                        title: a.currentTarget.getAttribute('data-popup-title'),
                        html: cache,
                        width: 330,
                        showCancelButton: false,
                        showConfirmButton: false,
                    })
                }
            });
            element.setAttribute("data-popup-bound", true)
        }
    })
}