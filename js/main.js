// InternConnect Buea - small helpers only.

// 1. Mobile navigation toggle.
var navToggle = document.getElementById('navToggle');
var navLinks = document.getElementById('navLinks');

if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
        navLinks.classList.toggle('open');
    });
}

// 2. Ask before running a destructive action.
// Usage: <a href="delete.php?id=3" data-confirm="Delete this internship?">Delete</a>
var confirmLinks = document.querySelectorAll('[data-confirm]');
for (var i = 0; i < confirmLinks.length; i++) {
    confirmLinks[i].addEventListener('click', function (event) {
        if (!window.confirm(this.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
}
