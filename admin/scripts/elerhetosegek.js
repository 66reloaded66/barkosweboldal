const toggle = document.getElementById('contacts-toggle');
    const contacts = document.getElementById('contacts');
  
    let hoverTimeout;
    let isClicked = false;
  
    toggle.addEventListener('click', () => {
      isClicked = !isClicked;
      contacts.classList.toggle('expanded', isClicked);
    });
  
    toggle.addEventListener('mouseenter', () => {
      hoverTimeout = setTimeout(() => {
        if (!isClicked) contacts.classList.add('expanded');
      }, 400);
    });
  
    toggle.addEventListener('mouseleave', () => {
      clearTimeout(hoverTimeout);
      if (!isClicked) contacts.classList.remove('expanded');
    });