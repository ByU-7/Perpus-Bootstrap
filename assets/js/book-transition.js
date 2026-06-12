document.addEventListener('DOMContentLoaded', () => {
    // Inject the HTML overlay
    const wrapper = document.createElement('div');
    wrapper.className = 'book-transition-wrapper';
    wrapper.id = 'book-transition';
    wrapper.innerHTML = `
        <div class="book-bg"></div>
        <div class="book-spine"></div>
        <div class="page-flipper">
            <div class="page-front">
                <div class="page-content"><i class="bi bi-book" style="font-size: 5rem; opacity: 0.2;"></i></div>
            </div>
            <div class="page-back">
                <div class="page-content"><h2 style="font-family: 'Lora', serif; font-weight: bold; opacity: 0.8;">Perpus Bayu</h2></div>
            </div>
        </div>
    `;
    document.body.appendChild(wrapper);

    if (sessionStorage.getItem('bookTransitionOpen') === 'true') {
        wrapper.classList.add('instant');
        wrapper.classList.add('active');
        wrapper.classList.add('turning'); 
        wrapper.style.pointerEvents = 'auto'; 
        
        // Force reflow so it renders instantly in 'turning' state
        void wrapper.offsetWidth;
        
        // Now remove instant so transitions work again
        wrapper.classList.remove('instant');
        
        // Open the page by removing turning
        setTimeout(() => {
            wrapper.classList.remove('turning');
            
            // After turn is done, fade out background
            setTimeout(() => {
                wrapper.classList.remove('active');
                wrapper.style.pointerEvents = 'none';
            }, 800);
        }, 50);
        
        sessionStorage.removeItem('bookTransitionOpen');
    }

    window.triggerBookTransition = function(action) {
        const book = document.getElementById('book-transition');
        book.classList.add('active'); // Fades in background
        book.style.pointerEvents = 'auto';
        
        setTimeout(() => {
            book.classList.add('turning'); // Flips the page
            
            setTimeout(() => {
                sessionStorage.setItem('bookTransitionOpen', 'true');
                if(typeof action === 'string') {
                    window.location.href = action;
                } else {
                    action();
                }
            }, 800); // Wait for page turn
        }, 100); // Wait a bit for bg to fade in
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
