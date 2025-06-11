// Form
const form = document.querySelector('#uploadForm')
// Progress Bar
const progressBar = document.getElementById('uploadProgressBar')
 
// Event Listner for Uploading the File
form.addEventListener('submit', uploadFile)
 
function uploadFile(e){
    e.preventDefault()
    // Remove previous upload message
    if(!!form.querySelector('.upload-msg')){
        form.querySelector('.upload-msg').remove()
    }
    // Display Progress Bar
    progressBar.style.setProperty('display', 'block')
    // Disable form's File Input and Button
    form.querySelector('#file').setAttribute('disabled', true)
    form.querySelector('button').setAttribute('disabled', true)
 
    // Start Upload Ajax
    const reqst = new XMLHttpRequest();
    // Event Listener for monitoring the progress
    reqst.upload.addEventListener("progress", uploadProgress);
    // Event Listener when Ajax is completed
    reqst.addEventListener("load", function(e){
        progressBar.after(document.createRange().createContextualFragment(reqst.response))
        form.reset();
        form.querySelector('#file').removeAttribute('disabled')
        form.querySelector('button').removeAttribute('disabled')
    });
    // Event Listener when Ajax throws an error
    reqst.addEventListener("error", function(){
        alert("Uploading file failed due to unknown reason.")
    });
    // Opeing the request
    reqst.open("POST", 'uploadpdffile.php', true);
    // Creating the formdata
    var formData = new FormData(this);
    formData.append('file', form.querySelector('#file').files[0])
    // Sending the request's data
    reqst.send(formData);
 
}
 
function uploadProgress(e){
    // bytes equevalent to 1 MB in binary
    var bytes = 1048576
    // Getting the current loaded data in MB
    var loaded = (parseFloat(e.loaded) / bytes);
    // Getting the Total data in MB
    var total = (parseFloat(e.total) / bytes);
    // Getting the percentage of the loaded data over the total data
    var percentage = ((loaded / total) * 100).toLocaleString('en-US',{'style':'decimal', maximumFractionDigits:2})
   
    // Update Progress Bar
    progressBar.querySelector('.bar').style.setProperty('--progress-width',percentage + '%');
    progressBar.querySelector('.progress-percentage').innerText = percentage + '%';
}