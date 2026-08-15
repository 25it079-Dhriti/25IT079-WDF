function vaildateForm() {
    let errorMessage ="";


    let fn = document.getElementById("fn").value;
    let ln = document.getElementById("ln").value;
    let em = document.getElementById("em").value;
    let num = document.getElementById("num").value;
    let pass = document.getElementById("pass").value;
    let cpass = document.getElementById("cpass").value;
    let course = document.getElementById("course").value;
    let year = document.getElementById("year").value;


    if(fn === "") {
        errorMessage += "First Name is req.\n";
    }
    if(ln === "") {
        errorMessage += "Last Name is req.\n";
    }
    if(em === "") {
        errorMessage += "Email is req.\n";
    }
    if(num === "") {
        errorMessage += "Number is req.\n";
    }
    if(pass === "") {
        errorMessage += "Password is req.\n";
    }
    if (cpass === "") {
        errorMessage += "Confirm Password is required\n";
    }
    if (pass !== "" && cpass !== "" && pass !== cpass) {
        errorMessage += "Password and Confirm Password do not match\n";
    }
    if(course === "") {
        errorMessage += "Course is req.\n";
    }
    if(year === "") {
        errorMessage += "Year is req.\n";
    }


    let gender = document.querySelector(
    'input[name="gender"]:checked'
    );
    if (!gender) {
    errorMessage += "Gender is required.\n";
    }

    let terms = document.querySelector(
    'input[type="checkbox"]:checked'
    );
    if (!terms) {
    errorMessage += "Terms and Conditions are required.\n";
    }


    
    if (errorMessage !== "") {
        alert(errorMessage);
        return false;
    }

     alert("Registration Successful!");

     

}