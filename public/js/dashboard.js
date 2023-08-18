const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const logoutForms = document.querySelectorAll('form#logoutForm');
logoutForms.forEach(function(form) {
    form.onsubmit = function(event){
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var requestBody = {
            email: email,
            number:number
        };
        //open the request
        xhr.open('POST', "/users/logout");
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Content-Type', 'application/json');
        //send the form data
        xhr.send(JSON.stringify(requestBody));
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    form.reset(); //reset form after AJAX success.
                    window.location.reload();
                    // console.log("hasil" + response)
                    // Handle the response data
                    // ...
                } else {
                    // Handle the error case
                    // ...
                }
            }
        }
        return false; 
    }
});