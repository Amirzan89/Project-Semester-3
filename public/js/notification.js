const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const popup = document.querySelector('div#popup');
const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
const inpEmail = document.getElementById('inpEmail');
const inpPassword = document.getElementById('inpPassword');
const loginForm = document.getElementById('loginForm');
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
function dashboardPage(){
    window.location.href = '/dashboard';
}
function showGreenPopup(data, div = null){
    if(div == 'dashboard'){
        greenPopup.innerHTML = `
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <div class="icon"><img src="${window.location.origin + tPath}/assets/img/check.png" alt=""></div>
            </div>
            <span class="closePopup" onclick="closePopup('green',true)">X</span>
            <label>${data.message}</label>
        `;
        greenPopup.style.display = 'block';
        setTimeout(() => {
            dashboardPage();
        }, 3000);
    }else{
        let dataa = JSON.stringify(data);
        if(dataa.includes('logout') ||dataa.includes('Logout') ){
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin + tPath}/assets/img/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true)">X</span>
                <label>${dataa}</label>
            `;
            greenPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('green');
            }, 3000);
        }else{
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin + tPath}/assets/img/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true)">X</span>
                <label>${data.message}</label>
            `;
            greenPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('green');
            }, 3000);
        }
    }
}
function showRedPopup(data){
    if(data.message){
        redPopup.innerHTML = `
            <div class="bg" onclick="closePopup('red',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <span>!</span>
            </div>
            <span class="closePopup" onclick="closePopup('red',true)">X</span>
            <label>${data.message}</label>
        `;
        redPopup.style.display = 'block';
        setTimeout(() => {
            closePopup('red');
        }, 3000);
    }else{
        redPopup.innerHTML = `
            <div class="bg" onclick="closePopup('red',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <span>!</span>
            </div>
            <span class="closePopup" onclick="closePopup('red', true)">X</span>
            <label>${data}</label>
        `;
        redPopup.style.display = 'block';
        setTimeout(() => {
            closePopup('red');
        }, 3000);
    }
}
function closePopup(div, click = false) {
    if(click){
        if (div == 'green') {
            greenPopup.style.display = 'none';
            greenPopup.innerHTML = '';
        } else if (div == 'red') {
            redPopup.style.display = 'none';
            redPopup.innerHTML = '';
        }
    }else{
        if (div == 'green') {
            greenPopup.classList.add('fade-out');
            setTimeout(() => {
                greenPopup.style.display = 'none';
                greenPopup.classList.remove('fade-out');
                greenPopup.innerHTML = '';
            }, 750);
        } else if (div == 'red') {
            redPopup.classList.add('fade-out');
            setTimeout(() => {
                redPopup.style.display = 'none';
                redPopup.classList.remove('fade-out');
                redPopup.innerHTML = '';
            }, 750);
        }
    }
}