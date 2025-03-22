const toggleBtn = document.getElementById('toggle-nav-btn');
const navBar = document.getElementById('navigation-bar');
const chatContainer = document.getElementById('chat-container');

let isNavVisible = true;

toggleBtn.addEventListener('click', () => {
    isNavVisible = !isNavVisible;
    if (isNavVisible) {
        navBar.style.display = "flex";
        toggleBtn.textContent = "<<";
    } else {
        navBar.style.display = "none";
        toggleBtn.textContent = ">>";
    }
});