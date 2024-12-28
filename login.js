function togglePassword(passwordFieldId) {
    const passwordField = document.getElementById(passwordFieldId);
    const toggleIcon = passwordField.nextElementSibling.querySelector('i');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bxs-hide');
        toggleIcon.classList.add('bxs-show');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bxs-show');
        toggleIcon.classList.add('bxs-hide');
    }
}

let menu = document.querySelector('#menu-icon');
let navbar = document.querySelector('.navbar');

menu.onclick = () => {
    menu.classList.toggle('bx-x');
    navbar.classList.toggle('active');
};

window.onscroll = () => {
    menu.classList.remove('bx-x');
    navbar.classList.remove('active');
};

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePassword(password) {
    
    return password.length >= 8 && /\d/.test(password);
}

function handleRememberMe() {
    const rememberMeCheckbox = document.getElementById('remember-me-login');
    const emailInput = document.querySelector('input[name="email"]');

    if (rememberMeCheckbox && emailInput) {
        
        const storedEmail = localStorage.getItem('rememberedEmail');
        if (storedEmail) {
            emailInput.value = storedEmail;
            rememberMeCheckbox.checked = true;
        }

        
        rememberMeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    
    const loginForm = document.querySelector('#login-form form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value;
            const password = this.querySelector('input[name="password"]').value;

            if (!validateEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
        });
    }

    const signupForm = document.querySelector('#signup-form form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const name = this.querySelector('input[name="name"]').value;
            const email = this.querySelector('input[name="email"]').value;
            const password = this.querySelector('input[name="password"]').value;

            if (name.length < 2) {
                e.preventDefault();
                alert('Name must be at least 2 characters long');
                return false;
            }

            if (!validateEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }

            if (!validatePassword(password)) {
                e.preventDefault();
                alert('Password must be at least 8 characters long and contain at least one number');
                return false;
            }
        });
    }

    handleRememberMe();
});