const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
document.getElementById("verifyChange").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form submission
    // Get the form data
    var formData = new FormData(this);
    formData.append('email',email);
    formData.append('code',code);
    formData.append('link',link);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", domain+"/verify/token/password",true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            console.log('Request successful');
            window.location.reload();
            console.log(response);
        } else {
            console.log('eorrr');
            console.error('Request failed with status ' + xhr.status);
        }
    };
    xhr.send(formData);
});
var form = document.getElementById('RegisterPass');
form.onsubmit = function(event){
    var xhr = new XMLHttpRequest();
    var data = new FormData(form);
    data.append('email',email); 
    data.append('nama',nama);
    data.append('username',username);
    //open the request
    xhr.open('POST','http://localhost:7000/tests/v1.0/form')
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(data);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = xhr.responseText;
                form.reset(); //reset form after AJAX success.
                window.location.reload();
                // Handle the response data
                // ...
            } else {
                // Handle the error case
                // ...
            }
        }
    }

    //Dont submit the form.
    return false; 
}