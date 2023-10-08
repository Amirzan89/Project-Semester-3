function logout(){
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: email,
        number:number
    };
    //open the request
    xhr.open('POST', "/web/logout.php");
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                window.location.reload();
            } else {
            }
        }
    }
}