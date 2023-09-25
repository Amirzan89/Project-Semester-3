const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const btnVerify = document.getElementById('Verify');
btnLogout.addEventListener('click', ()=>{
    var xhr = new XMLHttpRequest();
    xhr.open('POST', domain+'/users/verify/email', true);
    // Set headers
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    // xhr.setRequestHeader('Authorization', token);
    // Set request body
    var requestBody = {
        //get from input
        email: '',
        
    };
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            // Process the response here
            console.log('Request successful');
            console.log('Response headers:', responseHeaders);
            console.log('Response body:', responseBody);
            console.log(response);
        } else {
            console.log('eorrr');
            console.error('Request failed with status ' + xhr.status);
        }
    };
    xhr.send(JSON.stringify(requestBody));
});