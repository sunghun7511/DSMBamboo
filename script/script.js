window.onload = function() {
    
//    contact
    var contact = document.getElementById("contact");
    var contactModal = document.getElementById("contactModal");
    contact.onclick = function() {
        contactModal.classList.toggle("viewOn");
    }
    
    
//    login
    var login = document.getElementById("login");
    if(login == null){
        login = document.getElementById("logout");
    }else{
        var loginModal = document.getElementById("loginModal");
        login.onclick = function() {
            loginModal.classList.toggle("viewOn");
        }
    }

//    exit
    var i;
    var exit = document.getElementsByClassName("exit");
    for(i=0;i<exit.length;i++) {   
        exit[i].onclick = function() {
            contactModal.classList.remove("viewOn");
            if(loginModal != null)
                loginModal.classList.remove("viewOn");
        }
    }
    
}