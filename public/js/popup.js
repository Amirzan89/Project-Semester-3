const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
var isPopupVisible = false;
function showGreenPopup(data, div = null){
    if(div == 'dashboard'){
        greenPopup.innerHTML = `
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <div class="icon"><img src="${window.location.origin + tPath}/public/assets/img/check.png" alt=""></div>
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
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin + tPath}/public/assets/img/check.png" alt=""></div>
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
function showRedPopup(data, div){
    if(div == 'otp' && !isPopupVisible){
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
        showDiv(div);
        isPopupVisible = true;
        setTimeout(() => {
            closePopup('red');
            isPopupVisible = false;
        }, 1000);
    }else if(!isPopupVisible){
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
            isPopupVisible = true; 
            setTimeout(() => {
                closePopup('red');
                isPopupVisible = false; 
            }, 1000);
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
            isPopupVisible = true; 
            setTimeout(() => {
                closePopup('red');
                isPopupVisible = false; 
            }, 1000);
        }
    }
}
function closePopup(opt,click = false, div= null) {
    if(click){
        if (opt == 'green') {
            greenPopup.style.display = 'none';
            greenPopup.innerHTML = '';
        } else if (opt == 'red') {
            redPopup.style.display = 'none';
            redPopup.innerHTML = '';
        }
    }else{
        if (opt == 'green') {
            greenPopup.classList.add('fade-out');
            setTimeout(() => {
                greenPopup.style.display = 'none';
                greenPopup.classList.remove('fade-out');
                greenPopup.innerHTML = '';
            }, 750);
        } else if (opt == 'red') {
            redPopup.classList.add('fade-out');
            setTimeout(() => {
                redPopup.style.display = 'none';
                redPopup.classList.remove('fade-out');
                redPopup.innerHTML = '';
            }, 750);
        }
    }
    if(div){
        if(div == 'login'){
            loginPage();
        }else{
            showDiv(div);
        }
    }
}
function showDiv(div){
    if(div == 'otp'){
        document.querySelector('div#registerDiv').style.display = 'none';
        document.querySelector('div#otp').style.display = 'block';
    }
}