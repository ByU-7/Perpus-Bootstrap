// book-transition.js
document.addEventListener('DOMContentLoaded', () => {
    // Inject the HTML overlay
    const wrapper = document.createElement('div');
    wrapper.className = 'book-transition-wrapper';
    wrapper.id = 'book-transition';
    wrapper.innerHTML = `
        <div class="desk-bg"></div>
        <div class="book-right-page"></div>
        <div class="page-flipper">
            <div class="cover-outside">
                <i class="bi bi-book-half" style="font-size: 5rem; color: white;"></i>
                <h2 style="font-family: 'Lora', serif; font-weight: bold; color: white; margin-top: 1rem; text-align: center;">Perpus Bayu</h2>
            </div>
            <div class="cover-inside"></div>
        </div>
    `;
    document.body.appendChild(wrapper);

    // Incoming transition (Opening the book)
    if (sessionStorage.getItem('bookTransitionOpen') === 'true') {
        wrapper.classList.add('instant');
        wrapper.classList.add('active');
        wrapper.classList.add('closing'); // Book starts closed
        wrapper.style.pointerEvents = 'auto'; 
        
        // Force reflow so it renders instantly in 'closed' state
        void wrapper.offsetWidth;
        
        wrapper.classList.remove('instant');
        
        // Open the book
        setTimeout(() => {
            wrapper.classList.remove('closing');
            
            // After turn is done, fade out everything
            setTimeout(() => {
                wrapper.classList.remove('active');
                wrapper.style.pointerEvents = 'none';
            }, 800);
        }, 50);
        
        sessionStorage.removeItem('bookTransitionOpen');
    }

    // Outgoing transition (Closing the book)
    window.triggerBookTransition = function(action) {
        const book = document.getElementById('book-transition');
        book.classList.add('active'); 
        book.style.pointerEvents = 'auto';
        
        setTimeout(() => {
            book.classList.add('closing'); 
            
            setTimeout(() => {
                sessionStorage.setItem('bookTransitionOpen', 'true');
                if(typeof action === 'string') {
                    window.location.href = action;
                } else {
                    action();
                }
            }, 800); // Wait for cover to close
        }, 100); // Wait a bit for desk to fade in
    };

    // Attach to specific links
    const adminLinks = document.querySelectorAll('a[href*="admin/login.php"], a[href="login.php"]');
    adminLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(link.href);
        });
    });

    const loginForm = document.querySelector('form[action="proses_login.php"]');
    if(loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            triggerBookTransition(() => {
                loginForm.submit();
            });
        });
    }

    const logoutLinks = document.querySelectorAll('a[href="logout.php"], a[href*="admin/logout.php"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(link.href);
        });
    });
    
    const backToPublic = document.querySelector('a[href="../index.php"]');
    if(backToPublic && window.location.href.includes('admin/login.php')) {
        backToPublic.addEventListener('click', (e) => {
            e.preventDefault();
            triggerBookTransition(backToPublic.href);
        });
    }
});
