const messages = document.querySelector('.messages');
const errors = document.querySelector('.errors');

const selectFile = document.querySelector('#selectFile');
const previewFile = document.querySelector('#preview');
const btnUpload = document.querySelector('.upload-field__upload');

const infoName = document.querySelector('#infoName');
const infoType = document.querySelector('#infoType');
const infoSize = document.querySelector('#infoSize');

const prevContext = previewFile.getContext('2d');

//Показ сообщений и ошибок
if (messages.innerText != ""){
    messages.classList.add('show');
}

if (errors.innerText != ""){
    errors.classList.add('show');
}

//Вывод превью изображения и информации о нем. Проверки на соответствие параметров. 
selectFile.addEventListener('change', function(){
    
    prevContext.clearRect(0, 0, previewFile.clientWidth, previewFile.clientHeight);
    btnUpload.removeAttribute("disabled");

    infoName.innerText = this.files[0].name;
    infoType.innerText = this.files[0].type;  
    infoSize.innerText = (this.files[0].size/1000000).toFixed(2) + ' Мб';

    if (!this.files[0].type.includes('image')){
        btnUpload.setAttribute("disabled", "true");
        return;
    }
    
    const fReader = new FileReader();
    fReader.readAsDataURL(this.files[0]);
    fReader.onloadend = (event) =>{
        const img = new Image();
        img.onload = () =>{
            const resHeight = 170;
            const resWidth = Math.round(img.width/(img.height/resHeight));
            previewFile.width = resWidth;
            
            prevContext.drawImage(img, 0, 0, resWidth, resHeight);
        }
        img.src = event.target.result;        
    }
    
})