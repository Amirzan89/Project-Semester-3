const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
// const tableDevice = document.querySelector('#table-device');
const logoutForms = document.querySelectorAll('form#logoutForm');
const editDeviceForm = document.getElementById('editDeviceForm');
const tambahDeviceForm = document.getElementById('tambahDeviceForm');
const idDeviceInput = tambahDeviceForm.querySelector('#idDeviceInput');
const tokenInput = tambahDeviceForm.querySelector('#tokenInput');
const gpsInput = document.getElementById('gpsInput');
// console.log('fileee device');
function showLoading(){
    document.querySelector('div.loading').style.display = block;
}
function closeLoading(){
    document.querySelector('div.loading').style.display = none;
}
function generateString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }
    return result;
}
function createDeviceToken(){
    return generateString(30);
}
function createIdDevice(){
    return generateString(7)+'-'+generateString(7)+'-'+generateString(7);
}
function getTambahDevice(){
    id = createIdDevice();
    token = createDeviceToken();
    idDeviceInput.value = id;
    tokenInput.value = token;
}
function copyValue(input) {
    input.select();
    document.execCommand('copy');
    console.log('Value copied: ' + input.value);
}
idDeviceInput.addEventListener('click',function(){
    copyValue(idDeviceInput);
});
tokenInput.addEventListener('click',function(){
    copyValue(tokenInput);
});
// addDeviceForm.onsubmit = function(event){
//     event.preventDefault();
//     var xhr = new XMLHttpRequest();
//     var requestBody = {
//         email: email,
//         nama:namaDeviceInput.value,
//         token:tokenInput.value,
//         gps:gpsInput.value,
//     };
//     //open the request
//     xhr.open('POST',domain+"/device/create")
//     xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
//     xhr.setRequestHeader('Content-Type', 'application/json');
//     //send the form data
//     xhr.send(JSON.stringify(requestBody));
//     xhr.onreadystatechange = function() {
//         if (xhr.readyState == XMLHttpRequest.DONE) {
//             if (xhr.status === 200) {
//                 var response = JSON.parse(xhr.responseText);
//                 console.log(response)
//                 addDeviceForm.reset();
//                 getDevice();
//                 console.log('pop up adddd')
//                 document.querySelector('div#popUpSuccess').style.display = 'block';
//                 console.log('done')
//             } else {
//                 var response = xhr.responseText;
//                 console.log('errorrr '+response);
//                 // Handle the error case
//                 // ...
//             }
//         }
//     }
//     return false; 
// }
function closePopup(){
    document.querySelector('div#popUpSuccess').style.display = 'none';
}
// editDeviceForm.onsubmit = function(event){
//     event.preventDefault();
//     var xhr = new XMLHttpRequest();
//     var requestBody = {
//         email: email,
//         nama:namaDeviceInput.value,
//         token:tokenInput.value,
//         gps:gpsInput.value,
//     };
//     //open the request
//     xhr.open('POST',domain+"/device/create")
//     xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
//     xhr.setRequestHeader('Content-Type', 'application/json');
//     //send the form data
//     xhr.send(JSON.stringify(requestBody));
//     xhr.onreadystatechange = function() {
//         if (xhr.readyState == XMLHttpRequest.DONE) {
//             if (xhr.status === 200) {
//                 var response = JSON.parse(xhr.responseText);
//                 // console.log('email  reload '+email);
//                 console.log(response)
//                 editDeviceForm.reset(); //reset form after AJAX success.
//                 // Handle the response data
//                 // ...
//                 getDevice();
//             } else {
//                 var response = xhr.responseText;
//                 console.log('errorrr '+response);
//                 // Handle the error case
//                 // ...
//             }
//         }
//     }
//     return false; 
// }
logoutForms.forEach(function(form) {
    form.onsubmit = function(event){
        // showLoading();
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var requestBody = {
            email: email,
            number:number
        };
        //open the request
        xhr.open('POST',"/users/logout");
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Content-Type', 'application/json');
        //send the form data
        xhr.send(JSON.stringify(requestBody));
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // closeLoading();
                    var response = xhr.responseText;
                    form.reset(); //reset form after AJAX success.
                    window.location.reload();
                    // console.log("hasil" + response)
                    // Handle the response data
                    // ...
                } else {
                    // closeLoading();
                    // Handle the error case
                    // ...
                }
            }
        }
        return false; 
    }
});