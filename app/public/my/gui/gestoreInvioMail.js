let spanInfo = document.querySelector('#info');
let btnInviaProspetti = document.querySelector('input[name="inviaProspetti"]');

btnInviaProspetti.addEventListener('click', richiediInvioMail);

function richiediInvioMail() {
    fetch("/my/src/InterfacciaUtente.php", {
        method: 'POST',
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body:
            "CdL=" + document.querySelector('select[name="CdL"]').value
            + "&inviaProspetti"
    })
    .then(response => {
        if (!response.ok) {
            spanInfo.classList.add("errore");
        } else {
            spanInfo.classList.remove("errore");
            if (response.status !== 201) {
                setTimeout(richiediInvioMail, 10000);
            }
        }
        return response.text();
    })
    .then(text => {
        spanInfo.innerText = text;
    })
}