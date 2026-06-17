const root = document.documentElement;
const settingsToggle = document.querySelector('#settingsToggle');
const settingsPanel = document.querySelector('#settingsPanel');
const themeToggle = document.querySelector('#themeToggle');
const filterButtons = document.querySelectorAll('.filter-button');
const menuCards = document.querySelectorAll('.menu-card');

const savedTheme = localStorage.getItem('cascade-theme') || 'day';
root.dataset.theme = savedTheme;

if (themeToggle) {
  themeToggle.checked = savedTheme === 'night';
  themeToggle.addEventListener('change', () => {
    const nextTheme = themeToggle.checked ? 'night' : 'day';
    root.dataset.theme = nextTheme;
    localStorage.setItem('cascade-theme', nextTheme);
  });
}

if (settingsToggle && settingsPanel) {
  settingsToggle.addEventListener('click', () => {
    const isOpen = settingsPanel.hidden;
    settingsPanel.hidden = !isOpen;
    settingsToggle.setAttribute('aria-expanded', String(isOpen));
  });

  document.addEventListener('click', (event) => {
    if (!settingsPanel.hidden && !event.target.closest('.settings-menu')) {
      settingsPanel.hidden = true;
      settingsToggle.setAttribute('aria-expanded', 'false');
    }
  });
}

filterButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const filter = button.dataset.filter;

    filterButtons.forEach((item) => item.classList.toggle('active', item === button));
    menuCards.forEach((card) => {
      const shouldShow = filter === 'all' || card.dataset.category === filter;
      card.hidden = !shouldShow;
    });
  });
});
