document.addEventListener('DOMContentLoaded', function() {
  let referenceTitle = document.getElementById("reference-notes");
  let referenceList = document.getElementById("reference-list");

  referenceTitle.classList.add('arrow-right');

  referenceList.classList.add('hide-footnote');

  referenceTitle.addEventListener('click', function() {
    referenceList.classList.toggle('hide-footnote');
    this.classList.toggle('arrow-down');
  })
})  