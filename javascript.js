function httpGet(url)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", url, false ); // false for synchronous request
    xmlHttp.send( null );
    return xmlHttp.responseText;
}
function httpGetAsync(url, callback, callbackfail)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText);
        else
            callbackfail();
    }
    xmlHttp.open("GET", url, true); // true for asynchronous 
    xmlHttp.send(null);
}
function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}
function ChangeSelectedByNameOptionByValue(name, value)
{
    document.getElementsByName(name)[0].value = value;
}
function ChangeSelectedByNameOptionByIndex(name, index)
{
    document.getElementsByName(name)[0].selectedIndex = index;
}

function postmonCallback(response){
    if(response) {
        try {
            
            jsonDecoded = JSON.parse(response);
            
            document.getElementsByName('logradouro')[0].value = jsonDecoded.logradouro;
            document.getElementsByName('bairro')[0].value = jsonDecoded.bairro;
            document.getElementsByName('cidade')[0].value = jsonDecoded.cidade;
            document.getElementsByName('estado')[0].value = jsonDecoded.estado;
            return;
        } catch(e) {
            //do nothing
        }
    }

}
function postmonCallbackFail(){
    document.getElementsByName('logradouro')[0].value = "CEP não encontrado!";
    document.getElementsByName('bairro')[0].value = "CEP não encontrado!";
    document.getElementsByName('cidade')[0].value = "CEP não encontrado!";
    document.getElementsByName('estado')[0].value = "CEP não encontrado!";
}
function unmaskCEP(cep){
    return cep.replace(/[.-]/g, '');
}
function validateCEP(cep){
    return cep.match(/^([0-9]{2}.[0-9]{3}-[0-9]{3}|[0-9]{5}-[0-9]{3}|[0-9]{8})$/);
}
const postmon_url = 'https://api.postmon.com.br/v1/cep/';
function getPostmon(cep){
    if(validateCEP(cep))
        httpGetAsync(postmon_url + unmaskCEP(cep), postmonCallback, postmonCallbackFail)    
}
function confirmar_deletar_professor(id){
    $result = confirm('Tem certeza disso?');
    if($result)
        post('', {id_professor: id, ExluirProfessor: 'true'});
}
function confirmar_deletar_curso(id){
    $result = confirm('Tem certeza disso?');
    if($result)
        post('', {id_curso: id, ExluirCurso: 'true'});
}
function confirmar_deletar_aluno(id){
    $result = confirm('Tem certeza disso?');
    if($result)
        post('', {id_aluno: id, ExluirAluno: 'true'});
}