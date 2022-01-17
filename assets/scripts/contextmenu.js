export function contextmenu(x = []) {

    /** @TODO: refactor actions and add abstractions */
    let actions = {
        "folder": {
            "Rename": {
                "classes": [],
                "callback": function (title, baseurl, uuid) {
                    const Swal = require('sweetalert2');
                    /** @TODO: figure out how to do proper js routing */
                    fetch(baseurl + "folder/rename/" + uuid)
                        .then(response => response.text())
                        .then(response => {
                            Swal.fire({
                                title: title,
                                html: response,
                                width: 330,
                                showCancelButton: false,
                                showConfirmButton: false,
                            })
                        })
                }
            },
            "Move":  {
                "classes": [],
                "callback": function (title, baseurl, uuid) {
                    const Swal = require('sweetalert2');
                    /** @TODO: figure out how to do proper js routing */
                    fetch(baseurl + "folder/move/" + uuid)
                        .then(response => response.text())
                        .then(response => {
                            Swal.fire({
                                title: title,
                                html: response,
                                width: 330,
                                showCancelButton: false,
                                showConfirmButton: false,
                            })
                        })
                }
            },
            "Delete": {
                "classes": ['danger'],
                "callback": function (title, baseurl, uuid) {

                    const Swal = require('sweetalert2');
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
                        confirmButtonText: 'Yes, delete this.',
                        cancelButtonText: 'No, Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(baseurl + "folder/delete/" + uuid)
                                .then(response => response.text())
                                .then(response => {
                                    Swal.fire({
                                        title: title,
                                        html: response,
                                        width: 330,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                    })
                                })

                        } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ){}
                    })
                }
            }
        },
        "file": {}
    }

    if (x.length === 0) x = Array.from(document.querySelectorAll("[data-contextmenu=true]"));
    x.forEach(element => {


        if (element.getAttribute("data-contextmenu") === "true"
            && element.getAttribute("data-contextmenu-binded") !== true
            && element.getAttribute("data-contextmenu-uuid").length > 0) {

            element.addEventListener('contextmenu', (event) => {
                event.preventDefault();

                // Remove old instances
                let all = document.querySelectorAll(".contextmenu")
                if (all.length > 0)
                {
                    all.forEach((a)=>{
                        a.remove();
                    })
                }

                // Create container
                let menu = document.createElement("div");
                menu.classList.add('contextmenu');
                menu.style.left = event.clientX + "px";
                menu.style.top = event.clientY + "px";

                // Crate list
                let list = document.createElement("ul");
                menu.appendChild(list);

                function closer(a) {
                    if (!(a.target === menu || a.target.offsetParent === menu)) {
                        menu.remove();
                        document.removeEventListener('click', closer);
                    }
                }
                document.addEventListener("click", closer);

                Object.entries(actions[element.getAttribute("data-contextmenu-action")]).forEach(([title, func]) => {
                    let li = document.createElement("li");
                    li.innerHTML = title;
                    func['classes'].forEach((a)=>{
                        li.classList.add(a);
                    })
                    li.addEventListener("click", () => {
                        func['callback'](title,
                            element.getAttribute("data-contextmenu-baseurl"),
                            element.getAttribute("data-contextmenu-uuid")
                        );

                    })
                    list.appendChild(li);
                })
                document.body.appendChild(menu);
            })
            element.setAttribute("data-contextmenu-binded", true)
        }
    })

}