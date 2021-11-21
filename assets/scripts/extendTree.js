function extendTree(elements) {
   elements.forEach(a => {
      a.addEventListener("click", a => {
         console.log(a.target.getAttribute("data-uuid"));
      })
   });

}


extendTree(Array.from(document.getElementsByClassName("explorer-caret")))