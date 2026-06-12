// book-transition.js
document.addEventListener('DOMContentLoaded', () => {
    // Inject the HTML overlay
    const wrapper = document.createElement('div');
    wrapper.className = 'book-transition-wrapper';
    wrapper.id = 'book-transition';
    document.body.appendChild(wrapper);

    function nextFrame(callback) {
        requestAnimationFrame(() => {
            requestAnimationFrame(callback);
        });
    }

    const HTML_TEMPLATES = {
        'public-cover': `
            <div class="desk-bg"></div>
            <div class="book-scaler">
                <div class="book-right-page"></div>
                
                <div class="page-flipper flipper-title-page">
                    <div class="face-front paper-front d-flex flex-column justify-content-center align-items-center text-center p-4 p-md-5">
                        <div class="splash-content w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                            <h3 style="font-family: 'Lora', serif; color: #1a252f; margin-bottom: 20px; font-weight: bold; letter-spacing: 1px;">Selamat Datang di<br>Jendela Dunia</h3>
                            <p style="color: #495057; font-style: italic; max-width: 90%; line-height: 1.8; font-size: 1.05rem;">
                                "Setiap halaman yang Anda balik adalah sebuah langkah menuju petualangan baru. 
                                Temukan inspirasi, pelajari hal baru, dan wujudkan imajinasi Anda bersama koleksi literatur terbaik kami."
                            </p>
                            <div class="mt-auto pulse-text" style="color: #b8975a; font-weight: bold; cursor: pointer; user-select: none;">
                                [ Klik di mana saja untuk mulai membaca... ]
                            </div>
                        </div>
                    </div>
                    <div class="face-back paper-back"></div>
                </div>

                <div class="page-flipper flipper-cover">
                    <div class="face-front cover-gold">
                        <i class="bi bi-book-half" style="font-size: 5rem; color: white;"></i>
                        <h2 style="font-family: 'Lora', serif; font-weight: bold; color: white; margin-top: 1rem; text-align: center;">Buku Pengunjung</h2>
                    </div>
                    <div class="face-back cover-inside d-flex flex-column justify-content-center align-items-center">
                        <div class="splash-content text-center">
                            <i class="bi bi-book-half" style="font-size: 6rem; color: #b8975a; opacity: 0.9;"></i>
                            <h2 style="font-family: 'Lora', serif; font-weight: bold; color: #1a252f; margin-top: 1rem; letter-spacing: 2px; text-transform: uppercase;">Perpus Bayu</h2>
                            <div style="width: 50px; height: 3px; background-color: #b8975a; margin: 15px auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        'admin-cover': `
            <div class="desk-bg"></div>
            <div class="book-scaler">
                <div class="book-right-page"></div>
                <div class="page-flipper flipper-cover">
                    <div class="face-front cover-dark">
                        <i class="bi bi-shield-lock" style="font-size: 5rem; color: white;"></i>
                        <h2 style="font-family: 'Lora', serif; font-weight: bold; color: white; margin-top: 1rem; text-align: center;">Buku Admin</h2>
                    </div>
                    <div class="face-back cover-inside"></div>
                </div>
            </div>
        `,
        'paper-page': `
            <div class="desk-bg"></div>
            <div class="book-scaler">
                <div class="book-left-page"></div>
                <div class="book-right-page"></div>
                <div class="page-flipper flipper-paper">
                    <div class="face-front paper-front"></div>
                    <div class="face-back paper-back"></div>
                </div>
            </div>
        `
    };

    // INCOMING TRANSITION LOGIC
    const incomingType = sessionStorage.getItem('incomingTransition');
    if (incomingType) {
        sessionStorage.removeItem('incomingTransition');
        wrapper.className = 'book-transition-wrapper instant active';
        
        if (incomingType === 'public-book-open') {
            wrapper.innerHTML = HTML_TEMPLATES['public-cover'];
            wrapper.classList.add('closed');
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                wrapper.classList.remove('closed'); // Opens
                setTimeout(() => wrapper.classList.remove('active'), 850);
            });
        } 
        else if (incomingType === 'admin-book-open') {
            wrapper.innerHTML = HTML_TEMPLATES['admin-cover'];
            wrapper.classList.add('closed');
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                wrapper.classList.remove('closed');
                setTimeout(() => wrapper.classList.remove('active'), 850);
            });
        }
        else if (incomingType === 'page-forward-in') {
            wrapper.innerHTML = HTML_TEMPLATES['paper-page'];
            wrapper.classList.add('turned-left'); // Starts at left (since it already flipped in outgoing)
            wrapper.classList.add('zoomed-out'); // Start zoomed out
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                
                // No flip needed here! Just zoom in and fade out
                setTimeout(() => {
                    wrapper.classList.remove('zoomed-out'); // Zoom back in
                    wrapper.classList.remove('active'); // Fade out wrapper
                }, 100); // Small delay to let the page settle
            });
        }
        else if (incomingType === 'page-backward-in') {
            wrapper.innerHTML = HTML_TEMPLATES['paper-page'];
            // Starts at right (since it already flipped to right in outgoing)
            wrapper.classList.add('zoomed-out'); // Start zoomed out
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                
                // No flip needed here! Just zoom in and fade out
                setTimeout(() => {
                    wrapper.classList.remove('zoomed-out'); // Zoom back in
                    wrapper.classList.remove('active'); // Fade out wrapper
                }, 100);
            });
        }
    } 
    // SPLASH SCREEN (If first time on index.php and no incoming transition)
    else if (!sessionStorage.getItem('splashShown') && (window.location.pathname.endsWith('/') || window.location.pathname.includes('index.php'))) {
        sessionStorage.setItem('splashShown', 'true');
        wrapper.className = 'book-transition-wrapper instant active closed';
        wrapper.innerHTML = HTML_TEMPLATES['public-cover'];
        
        nextFrame(() => {
            wrapper.classList.remove('instant');
            
            // Wait a moment before automatically opening the book
            setTimeout(() => {
                wrapper.classList.remove('closed'); // Opens book, revealing splash content
                
                // Add one-time click listener to enter site
                const enterSite = () => {
                    document.removeEventListener('click', enterSite);
                    wrapper.style.pointerEvents = 'none'; // Prevent further clicks
                    
                    // Cinematic Zoom Out & Flip for Splash
                    wrapper.classList.add('zoomed-out');
                    
                    setTimeout(() => {
                        // Turn the title page!
                        wrapper.classList.add('turning-left');
                        
                        setTimeout(() => {
                            // Zoom back in and fade out
                            wrapper.classList.remove('zoomed-out');
                            wrapper.classList.remove('active');
                        }, 850); // After flip finishes
                    }, 400); // After zoom out finishes
                };

                // Wait for the open animation to finish before allowing click
                setTimeout(() => {
                    document.addEventListener('click', enterSite);
                }, 850);
                
            }, 800); // 800ms delay before opening
        });
    }

    // OUTGOING TRANSITION LOGIC
    window.triggerBookTransition = function(targetUrl, outType, inType) {
        const book = document.getElementById('book-transition');
        
        if (outType === 'public-book-close') {
            book.innerHTML = HTML_TEMPLATES['public-cover'];
        }
        else if (outType === 'admin-book-close') {
            book.innerHTML = HTML_TEMPLATES['admin-cover'];
        }
        else if (outType === 'page-forward-out' || outType === 'page-backward-out') {
            book.innerHTML = HTML_TEMPLATES['paper-page'];
        }

        // Use nextFrame to ensure elements are rendered before we animate them
        nextFrame(() => {
            book.className = 'book-transition-wrapper active';
            book.style.pointerEvents = 'auto';
            
            if (outType === 'public-book-close' || outType === 'admin-book-close') {
                nextFrame(() => {
                    wrapper.classList.add('zoomed-out');
                    setTimeout(() => {
                        book.classList.add('closed');
                        setTimeout(() => { 
                            if (inType) sessionStorage.setItem('incomingTransition', inType);
                            if(typeof targetUrl === 'string') window.location.href = targetUrl;
                            else if (typeof targetUrl === 'function') targetUrl();
                        }, 850);
                    }, 400);
                });
            }
            else if (outType === 'page-forward-out' || outType === 'page-backward-out') {
                if (outType === 'page-backward-out') {
                    book.classList.add('instant');
                    book.classList.add('turned-left'); // Start at left for backward nav
                }

                nextFrame(() => {
                    book.classList.remove('instant');
                    wrapper.classList.add('zoomed-out'); // Zoom out before flipping
                    
                    setTimeout(() => {
                        if (outType === 'page-forward-out') {
                            book.classList.add('turning-left'); // flips right to left
                        } else {
                            book.classList.remove('turned-left'); // flips left to right
                        }
                        
                        setTimeout(() => { 
                            if (inType) sessionStorage.setItem('incomingTransition', inType);
                            if(typeof targetUrl === 'string') window.location.href = targetUrl;
                            else if (typeof targetUrl === 'function') targetUrl();
                        }, 850); // Wait for flip
                    }, 400); // Wait for zoom out
                });
            }
            else {
                // instant
                if (inType) sessionStorage.setItem('incomingTransition', inType);
                if(typeof targetUrl === 'string') window.location.href = targetUrl;
                else if (typeof targetUrl === 'function') targetUrl();
            }
        });
    };

    // Link Interceptor
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[data-out]');
        if (link) {
            e.preventDefault();
            const outType = link.getAttribute('data-out');
            const inType = link.getAttribute('data-in');
            triggerBookTransition(link.href, outType, inType);
        }
    });

    const loginForm = document.querySelector('form[action="proses_login.php"]');
    if(loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Form submit is going to Dashboard (page forward)
            triggerBookTransition(() => {
                loginForm.submit();
            }, 'page-forward-out', 'page-forward-in');
        });
    }
});
