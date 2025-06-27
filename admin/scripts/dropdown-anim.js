document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
  const selected = dropdown.querySelector('.selected');
  const options = dropdown.querySelector('.dropdown-options');
  const hiddenInput = dropdown.querySelector('input[type="hidden"]');

  selected.addEventListener('click', () => {
    dropdown.classList.toggle('open');
  });

  options.querySelectorAll('li').forEach(option => {
    option.addEventListener('click', () => {
      selected.textContent = option.textContent;
      hiddenInput.value = option.getAttribute('data-value');
      dropdown.classList.remove('open');
    });
  });

  // Opcionálisan zárd le kattintásra máshol
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  });
});