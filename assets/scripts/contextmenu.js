export function contextmenu(x = []) {

    const Swal = require("sweetalert2");

    // Grab all elements that desire a contextmenu, if none are described
    if (x.length === 0) x = Array.from(document.querySelectorAll("[data-contextmenu=true]"));

    // Iterate through each element
    x.forEach(element => {
        if (element.getAttribute("data-contextmenu") === "true"
            && element.getAttribute("data-contextmenu-binded") !== true) {

            element.addEventListener('contextmenu', (event) => {
                event.preventDefault();

                // Remove old instances
                let all = document.querySelectorAll(".contextmenu")
                if (all.length > 0) {
                    all.forEach((a) => {
                        a.classList.add("hidden");
                    })
                }

                // Display current instance
                let menu = element.querySelector(".contextmenu");
                menu.classList.remove("hidden")
                menu.style.left = event.clientX + "px";
                menu.style.top = event.clientY + "px";

                // Bind menu buttons
                Array.from(menu.children[0].children).forEach((a)=>{
                    if (a.getAttribute("data-contextmenu-action-bound") !== "true")
                    {
                        switch (a.getAttribute("data-contextmenu-action")) {
                            case 'popup':
                                a.addEventListener('click', ()=>{
                                    fetch(a.getAttribute('data-contextmenu-href'))
                                        .then(response => response.text())
                                        .then(response => {
                                            Swal.fire({
                                                title: a.getAttribute('data-contextmenu-title'),
                                                html: response,
                                                width: 330,
                                                showCancelButton: false,
                                                showConfirmButton: false,
                                            })
                                        });
                                });
                                a.setAttribute("data-contextmenu-action-bound", "true");
                                break;

                            case 'popup-confirm':
                                a.addEventListener('click', ()=> {
                                    const swalWithBootstrapButtons = Swal.mixin({
                                        customClass: {
                                            confirmButton: 'btn btn-success',
                                            cancelButton: 'btn btn-danger'
                                        },
                                        buttonsStyling: false
                                    })

                                    swalWithBootstrapButtons.fire({
                                        title: 'Are you sure?',
                                        text: "This action is irreversible!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, Proceed.',
                                        cancelButtonText: 'No, Cancel',
                                        reverseButtons: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            fetch(a.getAttribute('data-contextmenu-href'))
                                                .then(response => response.text())
                                                .then(response => {
                                                    Swal.fire({
                                                        title: a.getAttribute('data-contextmenu-title'),
                                                        html: response,
                                                        width: 330,
                                                        showCancelButton: false,
                                                        showConfirmButton: false,
                                                    })
                                                })

                                        } else if (
                                            /* Read more about handling dismissals below */
                                            result.dismiss === Swal.DismissReason.cancel
                                        ) {
                                        }
                                    });
                                });
                                break;
                        }
                    }
                });


                // Close menu if non-menu click detected
                function closer(a) {
                    if (!(a.target === menu || a.target.offsetParent === menu)) {
                        menu.classList.add("hidden");
                        document.removeEventListener('click', closer);
                    }
                }
                document.addEventListener("click", closer);

            })
            element.setAttribute("data-contextmenu-binded", true)
        }
    })

}