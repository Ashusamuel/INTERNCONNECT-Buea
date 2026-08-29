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

// 3. Auto-dismiss alerts
var alerts = document.querySelectorAll('.alert');
alerts.forEach(function(alert) {
    setTimeout(function() {
        alert.style.transition = 'opacity 0.5s ease, margin 0.5s ease, padding 0.5s ease, height 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.style.display = 'none';
        }, 500);
    }, 4000);
});

// 4. Fade in cards and stats on load
document.addEventListener("DOMContentLoaded", function() {
    var animateItems = document.querySelectorAll('.card, .stat');
    animateItems.forEach(function(item, index) {
        // Initial state
        item.style.opacity = '0';
        item.style.transform = 'translateY(15px)';
        item.style.transition = 'opacity 0.4s ease, transform 0.4s ease, box-shadow 0.2s ease';
        
        // Trigger animation with a slight stagger
        setTimeout(function() {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
            
            // Restore hover transitions after animation
            setTimeout(function() {
                item.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
            }, 400);
        }, index * 60 + 50);
    });
});
