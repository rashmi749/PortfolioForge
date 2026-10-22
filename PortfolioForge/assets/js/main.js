// main.js - Core application interactions

document.addEventListener('DOMContentLoaded', () => {
    // 1. Scroll Progress Bar
    const progressBar = document.getElementById('scroll-progress');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }

    // 2. Scroll Animation Reveal
    const revealElements = document.querySelectorAll('.reveal');
    const revealOnScroll = () => {
        for (let i = 0; i < revealElements.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = revealElements[i].getBoundingClientRect().top;
            const elementVisible = 150;
            if (elementTop < windowHeight - elementVisible) {
                revealElements[i].classList.add('active');
            } else {
                revealElements[i].classList.remove('active');
            }
        }
    };
    if (revealElements.length > 0) {
        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll(); // Trigger once on load
    }

    // 3. Image Upload Instant Preview
    const imageUploadInputs = document.querySelectorAll('.image-preview-input');
    imageUploadInputs.forEach(input => {
        input.addEventListener('change', function() {
            const targetId = this.dataset.previewTarget;
            const previewImg = document.getElementById(targetId);
            if (previewImg && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // 4. Contact Form Handler (AJAX)
    const contactForm = document.getElementById('portfolio-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('form-alert-container');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            
            const formData = new FormData(contactForm);
            
            fetch('/PortfolioForge/submit_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alertContainer.innerHTML = '';
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show border-0 shadow-sm rounded-3`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    <div>${data.message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                alertContainer.appendChild(alertDiv);
                
                if (data.success) {
                    contactForm.reset();
                }
            })
            .catch(error => {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <div>An error occurred while sending your message. Please try again.</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // 5. Portfolio Layout Picker (card-style radio selection)
    const layoutOptions = document.querySelectorAll('.pf-layout-option');
    layoutOptions.forEach(option => {
        option.addEventListener('click', () => {
            layoutOptions.forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            const radio = option.querySelector('.pf-layout-radio');
            if (radio) radio.checked = true;
        });
    });
});
