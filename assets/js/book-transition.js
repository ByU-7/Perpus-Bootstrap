// book-transition.js
document.addEventListener('DOMContentLoaded', () => {
    // Inject the HTML overlay
    const wrapper = document.createElement('div');
    wrapper.className = 'book-transition-wrapper';
    wrapper.id = 'book-transition';
    document.body.appendChild(wrapper);

    // Fade out anti-flicker overlay AFTER the book curtain is ready
    const antiFlicker = document.getElementById('anti-flicker-overlay');
    function removeAntiFlicker() {
        if (antiFlicker) {
            antiFlicker.style.transition = 'opacity 0.3s ease';
            antiFlicker.style.opacity = '0';
            setTimeout(() => antiFlicker.remove(), 300);
        }
    }

    function nextFrame(callback) {
        requestAnimationFrame(() => {
            requestAnimationFrame(callback);
        });
    }

    const triggerAnimations = () => {
        const antiFlickerStyle = document.getElementById('anti-flicker-style');
        if (antiFlickerStyle) antiFlickerStyle.remove();
        
        window.bookTransitionFinished = true;
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, once: true, offset: 100 });
        }
        if (typeof window.triggerInitAnimations === 'function') {
            window.triggerInitAnimations();
        }

        // Handle URL Hash Scroll
        if (window.location.hash) {
            const targetEl = document.querySelector(window.location.hash);
            if (targetEl) {
                setTimeout(() => {
                    targetEl.scrollIntoView({ behavior: 'smooth' });
                }, 100);
            }
        }
    };

    const HTML_TEMPLATES = {
        'public-cover': `
            <div class="desk-bg"></div>
            <div class="book-curtain"></div>
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
                            <div class="mt-auto pulse-text" style="color: #e6a756; font-weight: bold; cursor: pointer; user-select: none;">
                                [ Klik di mana saja untuk mulai membaca... ]
                            </div>
                        </div>
                    </div>
                    <div class="face-back paper-back">
                        <div class="paper-watermark"><i class="bi bi-book-half"></i></div>
                    </div>
                </div>

                <div class="page-flipper flipper-cover">
                    <div class="face-front cover-public">
                        <i class="bi bi-book-half" style="font-size: 5rem; color: white;"></i>
                        <h2 style="font-family: 'Lora', serif; font-weight: bold; color: white; margin-top: 1rem; text-align: center;">Buku Pengunjung</h2>
                    </div>
                    <div class="face-back cover-inside d-flex flex-column justify-content-center align-items-center">
                        <div class="splash-content text-center">
                            <i class="bi bi-book-half" style="font-size: 6rem; color: #e6a756; opacity: 0.9;"></i>
                            <h2 style="font-family: 'Lora', serif; font-weight: bold; color: #1a252f; margin-top: 1rem; letter-spacing: 2px; text-transform: uppercase;">Perpus Bayu</h2>
                            <div style="width: 50px; height: 3px; background-color: #e6a756; margin: 15px auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        'admin-cover': `
            <div class="desk-bg"></div>
            <div class="book-curtain"></div>
            <div class="book-scaler">
                <div class="book-right-page"></div>
                <div class="page-flipper flipper-cover">
                    <div class="face-front cover-admin">
                        <i class="bi bi-shield-lock" style="font-size: 5rem; color: white;"></i>
                        <h2 style="font-family: 'Lora', serif; font-weight: bold; color: white; margin-top: 1rem; text-align: center;">Buku Admin</h2>
                    </div>
                    <div class="face-back cover-inside"></div>
                </div>
            </div>
        `,
        'paper-page': `
            <div class="desk-bg"></div>
            <div class="book-curtain"></div>
            <div class="book-scaler">
                <div class="book-left-page">
                    <div class="paper-watermark"><i class="bi bi-book-half"></i></div>
                </div>
                <div class="book-right-page">
                    <div class="paper-watermark"><i class="bi bi-book-half"></i></div>
                </div>
                <div class="page-flipper flipper-paper">
                    <div class="face-front paper-front">
                        <div class="paper-watermark"><i class="bi bi-book-half"></i></div>
                    </div>
                    <div class="face-back paper-back">
                        <div class="paper-watermark"><i class="bi bi-book-half"></i></div>
                    </div>
                </div>
            </div>
        `
    };

    // Helper function for splash screen entry
    const attachEnterSiteListener = () => {
        const enterSite = () => {
            document.removeEventListener('click', enterSite);
            wrapper.style.pointerEvents = 'none'; // Prevent further clicks
            
            // Cinematic Zoom Out & Flip for Splash
            wrapper.classList.add('zoomed-out');
            
            setTimeout(() => {
                // Turn the title page!
                wrapper.classList.add('turning-left');
                
                setTimeout(() => {
                    // Zoom back in
                    wrapper.classList.remove('zoomed-out');
                    
                    // Fade out after zoom plays
                    setTimeout(() => {
                        wrapper.classList.add('fade-out-wrapper');
                        triggerAnimations();
                    }, 400);
                }, 850); // After flip finishes
            }, 400); // After zoom out finishes
        };

        // Delay to prevent accidental instant clicks
        setTimeout(() => {
            document.addEventListener('click', enterSite);
        }, 500);
    };

    // INCOMING TRANSITION LOGIC
    const incomingType = sessionStorage.getItem('incomingTransition');
    if (incomingType) {
        sessionStorage.removeItem('incomingTransition');
        wrapper.className = 'book-transition-wrapper instant active';
        
        if (incomingType === 'splash-screen-in') {
            sessionStorage.removeItem('splashShown');
            wrapper.innerHTML = HTML_TEMPLATES['public-cover'];
            wrapper.classList.add('zoomed-out');
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                wrapper.classList.remove('instant');
                removeAntiFlicker();
                // Book is already open and zoomed out. Wait for click.
                attachEnterSiteListener();
            });
        }
        else if (incomingType === 'public-book-open') {
            wrapper.innerHTML = HTML_TEMPLATES['public-cover'];
            wrapper.classList.add('closed');
            wrapper.classList.add('slide-in-start'); // Start below screen
            wrapper.classList.add('zoomed-out');
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                removeAntiFlicker();
                
                // 1. Slide Up
                setTimeout(() => {
                    wrapper.classList.remove('slide-in-start');
                    
                    // 2. Open Cover
                    setTimeout(() => {
                        wrapper.classList.remove('closed'); 
                        
                        // 3. Flip Title Page (The second page turn!)
                        setTimeout(() => {
                            wrapper.classList.add('turning-left');
                            
                            // 4. Zoom In & Fade Out
                            setTimeout(() => {
                                wrapper.classList.remove('zoomed-out');
                                setTimeout(() => {
                                    wrapper.classList.add('fade-out-wrapper');
                                    triggerAnimations();
                                }, 400);
                            }, 850); // Wait for title page to flip
                        }, 850); // Wait for cover to open
                    }, 600); // Wait for slide up
                }, 50);
            });
        } 
        else if (incomingType === 'admin-book-open') {
            wrapper.innerHTML = HTML_TEMPLATES['admin-cover'];
            wrapper.classList.add('closed');
            wrapper.classList.add('slide-in-start'); // Start below screen
            wrapper.classList.add('zoomed-out');
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                removeAntiFlicker();
                
                // 1. Slide Up
                setTimeout(() => {
                    wrapper.classList.remove('slide-in-start');
                    
                    // 2. Open Cover
                    setTimeout(() => {
                        wrapper.classList.remove('closed');
                        
                        // 3. Zoom In & Fade Out
                        setTimeout(() => {
                            wrapper.classList.remove('zoomed-out');
                            setTimeout(() => {
                                wrapper.classList.add('fade-out-wrapper');
                                triggerAnimations();
                            }, 400);
                        }, 850); // Wait for open
                    }, 600); // Wait for slide up
                }, 50);
            });
        }
        else if (incomingType === 'page-forward-in') {
            wrapper.innerHTML = HTML_TEMPLATES['paper-page'];
            wrapper.classList.add('turned-left'); // Starts at left (since it already flipped in outgoing)
            wrapper.classList.add('zoomed-out'); // Start zoomed out
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                removeAntiFlicker();
                
                // No flip needed here! Just zoom in
                setTimeout(() => {
                    wrapper.classList.remove('zoomed-out'); // Zoom back in
                    
                    // Wait for zoom to play before fading out
                    setTimeout(() => {
                        wrapper.classList.add('fade-out-wrapper'); // Fade out wrapper
                        triggerAnimations();
                    }, 400);
                }, 100); // Small delay to let the page settle
            });
        }
        else if (incomingType === 'page-backward-in') {
            wrapper.innerHTML = HTML_TEMPLATES['paper-page'];
            // Starts at right (since it already flipped to right in outgoing)
            wrapper.classList.add('zoomed-out'); // Start zoomed out
            
            nextFrame(() => {
                wrapper.classList.remove('instant');
                wrapper.classList.remove('instant');
                removeAntiFlicker();
                
                // No flip needed here! Just zoom in
                setTimeout(() => {
                    wrapper.classList.remove('zoomed-out'); // Zoom back in
                    
                    // Wait for zoom to play before fading out
                    setTimeout(() => {
                        wrapper.classList.add('fade-out-wrapper'); // Fade out wrapper
                        triggerAnimations();
                    }, 400);
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
            // Step 1: Enable transitions
            wrapper.classList.remove('instant');
            wrapper.classList.remove('fade-out-wrapper');
            wrapper.classList.remove('slide-out');
            
            // Step 2: Reveal
            removeAntiFlicker();
            
            // Wait a moment before automatically opening the book
            setTimeout(() => {
                wrapper.classList.remove('closed'); // Opens book, revealing splash content
                
                // Add one-time click listener to enter site
                attachEnterSiteListener();
                
            }, 800); // 800ms delay before opening
        });
    }
    else {
        // No transition happening, fire immediately
        removeAntiFlicker();
        triggerAnimations();
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
                            // Slide out down!
                            wrapper.classList.add('slide-out');
                            setTimeout(() => {
                                if (inType) sessionStorage.setItem('incomingTransition', inType);
                                if(typeof targetUrl === 'string') window.location.href = targetUrl;
                                else if (typeof targetUrl === 'function') targetUrl();
                            }, 600); // Wait for slide out
                        }, 850); // Wait for close
                    }, 400); // Wait for zoom out
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

