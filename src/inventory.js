const inventarianteStatus = {};
const inventariantes = {}; 

function updateInventariantes() {
    const filialSelect = document.getElementById('filial');
    const filialValue = filialSelect.value;

    const inventariantesSelect = document.getElementById('inventariantes');
    inventariantesSelect.innerHTML = '<option value="0">Carregando...</option>';

    fetch(`http://localhost/CDU_Absoluta/Absoluta_CDU/src/get_inventariantes.php?filial=${filialValue}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na resposta do servidor: ' + response.statusText);
        }
        return response.json();
    })
    .then(inventariantesData => {
        // Armazena os dados de inventariantes em um objeto
        inventariantesData.forEach(inventariante => {
            inventariantes[inventariante.id] = inventariante; // Mapeia o id para o objeto colaborador
        });

        inventariantesSelect.innerHTML = '';
        inventariantesData.forEach(inventariante => {
            const option = document.createElement('option');
            option.value = inventariante.id;
            // Exibe também as datas de faltas se existirem
            const faltasInfo = inventariante.data_falta ? ` - Falta em: ${inventariante.data_falta}` : '';
            option.text = `${inventariante.nome} - ${inventariante.cargo}${faltasInfo}`;
            inventariantesSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Erro ao buscar inventariantes:', error);
        inventariantesSelect.innerHTML = '<option value="0">Erro ao carregar</option>';
    });
}


function updateInventarianteStatus() {
    const inventariantesSelect = document.getElementById('inventariantes');
    const selectedOptions = Array.from(inventariantesSelect.selectedOptions).map(option => option.value);

    Array.from(inventariantesSelect.options).forEach(option => {
        const id = option.value;
        if (id !== "0") {
            // Verifica o status do inventariante
            inventarianteStatus[id] = selectedOptions.includes(id) ? "Presente" : inventarianteStatus[id] || "Ausente";
            // Atualiza o texto da opção corretamente
            option.text = `${option.text.split(' - ')[0]} - ${option.text.split(' - ')[1]} - ${inventarianteStatus[id]}`; // Mantém o nome, cargo e adiciona o status
        }
    });

    console.log("Status atual dos inventariantes:", inventarianteStatus);
}

function exportToJson() {
    if (Object.keys(inventarianteStatus).length === 0) {
        alert('Nenhum dado para exportar.');
        return;
    }
    
    const json = JSON.stringify(inventarianteStatus, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = 'inventariantes_status.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

document.getElementById('filial').addEventListener('change', updateInventariantes);
document.getElementById('inventariantes').addEventListener('change', updateInventarianteStatus);

const exportButton = document.createElement('button');
exportButton.textContent = 'Exportar Status como JSON';
exportButton.addEventListener('click', exportToJson);
document.body.appendChild(exportButton);


