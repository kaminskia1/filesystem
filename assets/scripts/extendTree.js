export function extendTree(ajaxModule, x = null) {
    if (x == null) x = Array.from(document.getElementsByClassName("explorer-caret"));
    x.forEach(ele => {
        ele.addEventListener("click", a => {
            let activeRow = a.target;

            // Workaround because, apparently, elements inside of svg are targeted as well by this event.
            if (activeRow.nodeName === 'path' && activeRow.parentElement.nodeName === "svg") {
                console.log("adapted")
                activeRow = activeRow.parentElement;
            }

            if (activeRow.getAttribute("data-uuid") != null) {
                let container = activeRow.parentElement.parentElement.children[1];
                if (!activeRow.classList.contains("explorer-extended")) {
                    if (container.classList.contains('hidden')) {
                        container.classList.remove('hidden');
                    }
                    activeRow.classList.add("explorer-extended");
                    fetch(activeRow.getAttribute("data-uuid"), {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                        .then(response => response.text())
                        .then(responseText => {
                            container.innerHTML = responseText;
                            Array.from(container.children[0].children)
                                .forEach((child) => {
                                    // recursion \o/
                                    extendTree(ajaxModule, [child.children[0].children[0]]);
                                    ajaxModule.ajax([child.children[0].children[1].children[0]]);
                                });
                        });

                } else {
                    container.classList.add('hidden');
                    activeRow.classList.remove("explorer-extended");
                }
            }
        })

    });

}

