// ================== COMPLETE RESPONSIVE SIDEBAR SOLUTION ==================

document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const sidebarCollapseBtn = document.getElementById("sidebarCollapse");
    
    // إنشاء Backdrop للموبايل
    let backdrop = document.querySelector(".sidebar-backdrop");
    if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "sidebar-backdrop";
        document.body.appendChild(backdrop);
    }
    
    // دالة فتح الـ Sidebar (للموبايل)
    function openSidebar() {
        sidebar.classList.add("active");
        backdrop.classList.add("active");
        document.body.style.overflow = "hidden";
    }
    
    // دالة إغلاق الـ Sidebar (للموبايل)
    function closeSidebar() {
        sidebar.classList.remove("active");
        backdrop.classList.remove("active");
        document.body.style.overflow = "";
    }
    
    // دالة Toggle حسب حجم الشاشة
    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            // على الموبايل - Overlay Mode
            if (sidebar.classList.contains("active")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        } else {
            // على الشاشات الكبيرة - Collapse Mode
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("expanded");
            
            // حفظ الحالة (اختياري)
            const isCollapsed = sidebar.classList.contains("collapsed");
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
    }
    
    // عند الضغط على زر Toggle
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // عند الضغط على Backdrop - إغلاق Sidebar
    backdrop.addEventListener("click", function() {
        if (window.innerWidth <= 768) {
            closeSidebar();
        }
    });
    
    // إغلاق Sidebar عند الضغط على أي رابط (للموبايل فقط)
    const sidebarLinks = sidebar.querySelectorAll("a");
    sidebarLinks.forEach(link => {
        link.addEventListener("click", function() {
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    closeSidebar();
                }, 150);
            }
        });
    });
    
    // معالجة تغيير حجم الشاشة
    let resizeTimer;
    window.addEventListener("resize", function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                // الشاشات الكبيرة
                sidebar.classList.remove("active");
                backdrop.classList.remove("active");
                document.body.style.overflow = "";
                
                // استرجاع الحالة المحفوظة
                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    sidebar.classList.add("collapsed");
                    content.classList.add("expanded");
                }
            } else {
                // الموبايل
                sidebar.classList.remove("collapsed");
                content.classList.remove("expanded");
                
                // التأكد من إخفاء Sidebar
                if (!sidebar.classList.contains("active")) {
                    closeSidebar();
                }
            }
        }, 250);
    });
    
    // منع Scroll عند فتح Sidebar على الموبايل
    sidebar.addEventListener("touchmove", function(e) {
        if (window.innerWidth <= 768 && sidebar.classList.contains("active")) {
            e.stopPropagation();
        }
    }, { passive: true });
    
    // إغلاق Sidebar عند الضغط على ESC
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && window.innerWidth <= 768 && sidebar.classList.contains("active")) {
            closeSidebar();
        }
    });
    
    // تهيئة الحالة الابتدائية
    if (window.innerWidth > 768) {
        // استرجاع الحالة المحفوظة للشاشات الكبيرة
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add("collapsed");
            content.classList.add("expanded");
        }
    } else {
        // التأكد من إخفاء Sidebar على الموبايل
        sidebar.classList.remove("active");
        backdrop.classList.remove("active");
        document.body.style.overflow = "";
    }
});

// ================== RESPONSIVE UTILITIES ==================

// دوال للتحقق من حجم الشاشة
function isMobile() {
    return window.innerWidth <= 768;
}

function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 991;
}

function isDesktop() {
    return window.innerWidth > 991;
}

// إضافة Classes حسب حجم الشاشة
function updateScreenSizeClasses() {
    const body = document.body;
    
    body.classList.remove('is-mobile', 'is-tablet', 'is-desktop');
    
    if (isMobile()) {
        body.classList.add('is-mobile');
    } else if (isTablet()) {
        body.classList.add('is-tablet');
    } else {
        body.classList.add('is-desktop');
    }
}

// تشغيل عند التحميل وعند تغيير الحجم
document.addEventListener("DOMContentLoaded", updateScreenSizeClasses);
window.addEventListener("resize", updateScreenSizeClasses);

// ================== FORM ENHANCEMENTS ==================

document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll(".form-control");
        
        inputs.forEach(input => {
            // تأثير Focus
            input.addEventListener("focus", function() {
                this.parentElement.classList.add("focused");
            });
            
            // تأثير Blur
            input.addEventListener("blur", function() {
                this.parentElement.classList.remove("focused");
            });
            
            // Validation في الوقت الفعلي
            input.addEventListener("input", function() {
                if (this.checkValidity()) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                } else {
                    this.classList.remove("is-valid");
                    if (this.value.length > 0) {
                        this.classList.add("is-invalid");
                    }
                }
            });
        });
    });
});

// ================== SMOOTH SCROLL ==================

document.addEventListener("DOMContentLoaded", function() {
    const anchors = document.querySelectorAll('a[href^="#"]');
    
    anchors.forEach(anchor => {
        anchor.addEventListener("click", function(e) {
            const href = this.getAttribute("href");
            if (href !== "#" && href !== "#!") {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            }
        });
    });
});

// ================== AUTO HIDE ALERTS ==================

document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll(".alert:not(.alert-danger)");
    
    alerts.forEach(alert => {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.display = 'none';
            }
        }, 5000);
    });
});

// ================== TOOLTIP INITIALIZATION ==================

document.addEventListener("DOMContentLoaded", function() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// ================== PREVENT DOUBLE SUBMIT ==================

document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");
    
    forms.forEach(form => {
        form.addEventListener("submit", function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // حفظ النص الأصلي
                const originalText = submitBtn.innerHTML;
                submitBtn.setAttribute('data-original-text', originalText);
                
                // تعطيل الزر وتغيير النص
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري المعالجة...';
                
                // إعادة تفعيل بعد 5 ثوان (في حالة فشل الإرسال)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }
        });
    });
});

// ================== TABLE RESPONSIVE ENHANCEMENTS ==================

document.addEventListener("DOMContentLoaded", function() {
    // إضافة wrapper للجداول إذا لم يكن موجود
    const tables = document.querySelectorAll('table:not(.table-responsive table)');
    tables.forEach(table => {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
});

// ================== MOBILE MENU SWIPE GESTURE ==================

document.addEventListener("DOMContentLoaded", function() {
    if ('ontouchstart' in window) {
        const sidebar = document.getElementById("sidebar");
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            if (window.innerWidth <= 768) {
                // Swipe من اليمين لليسار - فتح Sidebar
                if (touchStartX - touchEndX > 50 && touchStartX > window.innerWidth - 50) {
                    if (!sidebar.classList.contains("active")) {
                        sidebar.classList.add("active");
                        document.querySelector(".sidebar-backdrop").classList.add("active");
                        document.body.style.overflow = "hidden";
                    }
                }
                // Swipe من اليسار لليمين - إغلاق Sidebar
                if (touchEndX - touchStartX > 50 && sidebar.classList.contains("active")) {
                    sidebar.classList.remove("active");
                    document.querySelector(".sidebar-backdrop").classList.remove("active");
                    document.body.style.overflow = "";
                }
            }
        }
    }
});

// ================== IMAGE LAZY LOADING ==================

document.addEventListener("DOMContentLoaded", function() {
    if ('IntersectionObserver' in window) {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
});

// ================== ORIENTATION CHANGE HANDLER ==================

window.addEventListener('orientationchange', function() {
    // إعادة تحميل الصفحة عند تغيير الاتجاه (اختياري)
    setTimeout(function() {
        updateScreenSizeClasses();
    }, 200);
});

// ================== PERFORMANCE OPTIMIZATION ==================

// Debounce function للأحداث المتكررة
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// تطبيق Debounce على Resize
window.addEventListener('resize', debounce(function() {
    updateScreenSizeClasses();
}, 250));

// ================== ACCESSIBILITY ENHANCEMENTS ==================

document.addEventListener("DOMContentLoaded", function() {
    // إضافة ARIA labels للعناصر التفاعلية
    const buttons = document.querySelectorAll('button:not([aria-label])');
    buttons.forEach(button => {
        if (!button.getAttribute('aria-label') && button.textContent.trim()) {
            button.setAttribute('aria-label', button.textContent.trim());
        }
    });
    
    // التأكد من إمكانية الوصول للوحة المفاتيح
    const clickableElements = document.querySelectorAll('[onclick]:not(button):not(a)');
    clickableElements.forEach(element => {
        if (!element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '0');
        }
        
        element.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});

// ================== CONSOLE WELCOME MESSAGE ==================

console.log(`
╔════════════════════════════════════════════════════╗
║   🎨 Responsive Dashboard System Loaded           ║
║   📱 Mobile-First Design                          ║
║   ✨ Version: 3.0 - Fully Responsive              ║
║   🚀 Optimized for All Screen Sizes               ║
╚════════════════════════════════════════════════════╝

Screen Breakpoints:
• Mobile:    ≤ 768px
• Tablet:    769px - 991px  
• Desktop:   ≥ 992px

Current Screen: ${isMobile() ? 'Mobile 📱' : isTablet() ? 'Tablet 📲' : 'Desktop 💻'}
`);

// ================== ERROR HANDLING ==================

window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.message);
    // يمكن إضافة تتبع الأخطاء هنا
});

// ================== LOADING ANIMATION ==================

window.addEventListener('load', function() {
    // إخفاء شاشة التحميل إذا كانت موجودة
    const loader = document.querySelector('.page-loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 300);
    }
    
    // إضافة animation للعناصر
    const animatedElements = document.querySelectorAll('.stats-card-new, .statement-card');
    animatedElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
});