<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Biblioteca</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            padding: 15px;
            position: sticky;
            top: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .btn, .sidebar .search-bar input {
            background-color: white;
            color: black;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        .sidebar .btn:hover, .sidebar .search-bar input:focus {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }
        .container {
            max-width: 1200px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .content h1, .content h2 {
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            background: linear-gradient(135deg, #007bff, #00c6ff);
            border-radius: 8px;
            padding: 10px;
            display: inline-block;
        }
        .content h1 span, .content h2 span {
            display: inline;
        }
        .form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .hidden {
            display: none;
        }
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        body.dark-mode .sidebar {
            background: linear-gradient(135deg, #333, #555);
            color: white;
        }
        body.dark-mode .sidebar .btn, body.dark-mode .sidebar .search-bar input {
            background-color: #444;
            color: white;
        }
        body.dark-mode .content h1, body.dark-mode .content h2 {
            background: linear-gradient(135deg, #333, #555);
        }
        body.dark-mode .card {
            background-color: #1e1e1e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        body.dark-mode .form-control, body.dark-mode .form-select {
            background-color: #333;
            color: white;
            border: 1px solid #555;
        }
        body.dark-mode .table {
            color: white;
        }
        body.dark-mode .table thead th {
            background-color: #333;
            color: white;
        }
        body.dark-mode .table tbody tr {
            background-color: #444;
        }
        body.dark-mode .table tbody tr:nth-of-type(odd) {
            background-color: #3a3a3a;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar d-flex flex-column align-items-start">
            <div class="search-bar mb-4 w-100">
                <input type="text" id="search" name="search" class="form-control mb-2" placeholder="Pesquisar 🔍" oninput="searchBooks()" onfocus="updatePageTitle('Pesquisar Livros')" onblur="updatePageTitle('Menu Biblioteca')">
            </div>
            <button id="toggle-dark-mode" class="btn mb-2 w-100 btn-dark">🌙</button>
            <a onclick="loadContent('add_autor.php', 'Cadastro de Autor')" class="btn mb-2 w-100">Cadastrar Autor 📚</a>
            <a onclick="loadContent('add_editora.php', 'Cadastro de Editora')" class="btn mb-2 w-100">Cadastrar Editora 🏢</a>
            <a onclick="loadContent('add_genero.php', 'Cadastro de Gênero')" class="btn mb-2 w-100">Cadastrar Gênero 🏷️</a>
            <a onclick="loadContent('add_livro.php', 'Cadastro de Livro')" class="btn mb-2 w-100">Cadastrar Livro 📖</a>
        </div>
        <div class="content flex-grow-1 p-4">
            <h1 id="page-title" class="text-center mb-4"><span>📚</span> Menu Biblioteca <span>📖</span></h1>
            <div id="results" class="mb-4 hidden">
                <!-- Resultados serão exibidos aqui -->
            </div>
            <div id="dynamic-content">
                <!-- Conteúdo dinâmico será carregado aqui -->
            </div>
            <div id="form-response" class="mt-4"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Mensagem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <!-- Mensagem de resposta será inserida aqui -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function updatePageTitle(title) {
            document.getElementById('page-title').innerHTML = `<span>📚</span> ${title} <span>📖</span>`;
        }

        async function searchBooks() {
            const query = document.getElementById('search').value;
            if (query.length > 0) {
                const response = await fetch(`search.php?query=${query}`);
                const results = await response.json();
                displayResults(results);
                document.getElementById('results').classList.remove('hidden');
                document.getElementById('dynamic-content').classList.add('hidden');
            } else {
                document.getElementById('results').innerHTML = '<div class="alert alert-info">Nenhum livro encontrado.</div>';
                document.getElementById('results').classList.add('hidden');
                document.getElementById('dynamic-content').classList.remove('hidden');
            }
        }

        function displayResults(results) {
            const resultsContainer = document.getElementById('results');
            resultsContainer.innerHTML = '';
            if (results.length > 0) {
                const table = document.createElement('table');
                table.classList.add('table', 'table-striped', 'table-bordered');
                const thead = document.createElement('thead');
                const tbody = document.createElement('tbody');

                thead.innerHTML = `
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Edição</th>
                        <th>Gênero</th>
                        <th>Autor</th>
                        <th>Editora</th>
                    </tr>
                `;

                results.forEach(result => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${result.id}</td>
                        <td>${result.nomeLivro}</td>
                        <td>${result.edicao}</td>
                        <td>${result.genero}</td>
                        <td>${result.autor}</td>
                        <td>${result.editora}</td>
                    `;
                    tbody.appendChild(tr);
                });

                table.appendChild(thead);
                table.appendChild(tbody);
                resultsContainer.appendChild(table);
            } else {
                resultsContainer.innerHTML = '<div class="alert alert-info">Nenhum livro encontrado.</div>';
            }
        }

        async function loadContent(page, title) {
            document.querySelector('title').textContent = title;
            document.getElementById('page-title').innerHTML = `<span>📚</span> ${title} <span>📖</span>`;
            const response = await fetch(page);
            const content = await response.text();
            document.getElementById('dynamic-content').innerHTML = content;
            attachFormHandler();
        }

        function attachFormHandler() {
            const form = document.querySelector('form[id$="-form"]');
            if (form) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    const formData = new FormData(form);
                    const action = form.getAttribute('action');
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    $('#modal-body-content').text(result.message);
                    $('#responseModal').modal('show');
                    if (result.status === 'success') {
                        setTimeout(function () {
                            $('#responseModal').modal('hide');
                            loadContent('add_livro.php', 'Cadastro de Livro');
                        }, 2000);
                    }
                });
            }
        }

        document.getElementById('toggle-dark-mode').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            var toggleBtn = document.getElementById('toggle-dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                toggleBtn.innerHTML = '🌞';
                toggleBtn.classList.remove('btn-dark');
                toggleBtn.classList.add('btn-light');
            } else {
                toggleBtn.innerHTML = '🌙';
                toggleBtn.classList.remove('btn-light');
                toggleBtn.classList.add('btn-dark');
            }
        });

        function confirmDelete(id, page) {
            if (confirm('Tem certeza de que deseja excluir este item?')) {
                fetch(`${page}.php?delete_id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            loadContent(`${page}.php`, `Cadastro de ${page.charAt(0).toUpperCase() + page.slice(1)}`);
                        }
                    })
                    .catch(error => console.error('Erro:', error));
            }
        }
    </script>
</body>
</html>
        