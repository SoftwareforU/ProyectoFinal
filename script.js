const sideMenu = document.getElementById('side-menu');
const openBtn = document.getElementById('open-menu');
const closeBtn = document.getElementById('menu-toggle');

openBtn.addEventListener('click', () => {
  sideMenu.classList.add('active');
});

closeBtn.addEventListener('click', () => {
  sideMenu.classList.remove('active');
});