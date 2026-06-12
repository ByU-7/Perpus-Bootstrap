document.addEventListener('DOMContentLoaded', () => {
    // Inject the HTML overlay
    const wrapper = document.createElement('div');
    wrapper.className = 'book-transition-wrapper';
    wrapper.id = 'book-transition';
    wrapper.innerHTML = `
        <div class="book-page-left">
            <div class="book-page-content"><i class="bi bi-book-half" style="font-size: 5rem;"></i></div>
        </div>
        <div class="book-page-right">
            <div class="book-page-content"><h2 style="font-family: 'Lora', serif; font-weight: bold; margin-top: 1rem;">Perpus Bayu</h2></div>
        </div>
    `;
    document.body.appendChild(wrapper);

    // If session storage has the flag, start closed and open it
    if (sessionStorage.getItem('bookTransitionOpen') === 'true') {
        wrapper.classList.add('book-closed');
        wrapper.style.pointerEvents = 'auto'; 
        
        // Force reflow
        void wrapper.offsetWidth;
        
        // Open the book
        setTimeout(() => {
            wrapper.classList.remove('book-closed');
            wrapper.style.pointerEvents = 'none';
        }, 50);
        
        sessionStorage.removeItem('bookTransitionOpen');
    } else {
        wrapper.classList.remove('book-closed');
    }

    window.triggerBookTransition = function(action) {
        const book = document.getElementById('book-transition');
        book.classList.add('book-closed');
        book.style.pointerEvents = 'auto';
        
        setTimeout(() => {
            sessionStorage.setItem('bookTransitionOpen', 'true');
            if(typeof action === 'string') {
                window.location.href = action;
            } else {
                action();
            }
        }, 800); 
    };

    // Attach to specific links
    const adminLinks = document.querySelectorAll('a[href*="admin/login.php"], a[href="login.php"]');
    adminLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(link.href);
        });
    });

    // For login form
    const loginForm = document.querySelector('form[action="proses_login.php"]');
    if(loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            triggerBookTransition(() => {
                loginForm.submit();
            });
        });
    }

    // For logout link
    const logoutLinks = document.querySelectorAll('a[href="logout.php"], a[href*="admin/logout.php"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(link.href);
        });
    });
    
    // For return to public link
    const backToPublic = document.querySelector('a[href="../index.php"]');
    if(backToPublic && window.location.href.includes('admin/login.php')) {
        backToPublic.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(backToPublic.href);
        });
    }
});
