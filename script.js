let menu = document.querySelector('#menu-icon');
let navbar =document.querySelector('.navbar');

menu.onclick = ()=> {
    menu.classList.toggle('bx-x');
    navbar.classList.toggle('active');
}

const typed = new Typed('.multiple-text', {
    strings: ['Burn Some Fat', 'Join The Community','One Step Ahead','The Art Of Fitness','Join With FitZone'],
    typeSpeed: 60,
    backSpeed: 60,
    backDelay: 1000,
    loop: true,
});





