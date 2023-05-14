var Formulario = document.getElementById('formulario-de-reclamação');
let popup1 = document.getElementById('popup-1');

function ConsoleFormulario(event){
    event.preventDefault();
    console.log("Tentativa de envio realizada");
}
Formulario.addEventListener('submit', ConsoleFormulario);

function AbrirPopup(event){
    event.preventDefault();
    openPopup();
}
function openPopup(){
    popup1.classList.add("open-popup");
}
function closePopup(){
    popup1.classList.remove("open-popup");
}