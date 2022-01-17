export function tree(x = []) {

    const ajaxModule = require("../scripts/ajax");

    /** @TODO: convert caret to data attribute and queryselector */
    if (x.length === 0) x = Array.from(document.getElementsByClassName("explorer-caret"));
    x.forEach(ele => {
        ele.addEventListener("click", a => {
            let activeRow = a.target;

            // Workaround because, apparently, elements inside of svg are targeted as well by this event.
            if (activeRow.nodeName === 'path' && activeRow.parentElement.nodeName === "svg") {
                activeRow = activeRow.parentElement;
            }

            // Verify that valid explorer element is targeted
            if (activeRow.getAttribute("data-uuid") != null) {

                // Grab container from explorer element
                let container = activeRow.parentElement.parentElement.children[1];

                if (!activeRow.classList.contains("explorer-extended")) {
                    if (container.classList.contains('hidden')) {
                        container.classList.remove('hidden');
                    }
                    activeRow.classList.add("explorer-extended");
                    if (container.innerHTML.length === 0)
                    {
                        fetch(activeRow.getAttribute("data-uuid"), {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                            .then(response => response.text())
                            .then(responseText => {
                                container.innerHTML = responseText;
                                Array.from(container.children[0].children)
                                    .forEach((child) => {

                                        // recursion \o/
                                        tree([child.children[0].children[0]]);
                                        ajaxModule.ajax([child.children[0].children[1].children[0]]);
                                    });
                            });

                    }
                } else {
                    container.classList.add('hidden');
                    activeRow.classList.remove("explorer-extended");
                }
            }
        })

    });

}

