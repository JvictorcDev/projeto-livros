<?php
include('conn.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeLivro = htmlspecialchars($_POST['nomeLivro']);
    $edicao = isset($_POST['edicao']) ? (int) $_POST['edicao'] : null;
    $genero_id = (int) $_POST['genero_id'];
    $autor_id = (int) $_POST['autor_id'];
    $editora_id = (int) $_POST['editora_id'];

    if ($nomeLivro && $genero_id && $autor_id && $editora_id) {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            // Update existing book
            $stmt = $pdo->prepare('UPDATE tbLivro SET nomeLivro = ?, edicao = ?, genero_id = ?, autor_id = ?, editora_id = ? WHERE id = ?');
            $stmt->execute([$nomeLivro, $edicao, $genero_id, $autor_id, $editora_id, (int)$_POST['id']]);
            $response['message'] = 'Livro atualizado com sucesso';
        } else {
            // Insert new book
            $stmt = $pdo->prepare('INSERT INTO tbLivro (nomeLivro, edicao, genero_id, autor_id, editora_id) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nomeLivro, $edicao, $genero_id, $autor_id, $editora_id]);
            $response['message'] = 'Livro adicionado com sucesso';
        }
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Por favor, preencha todos os campos obrigatórios.';
    }
    echo json_encode($response);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tbLivro WHERE id = ?');
    $stmt->execute([$delete_id]);

    $response['status'] = 'success';
    $response['message'] = 'Livro excluído com sucesso';
    echo json_encode($response);
    exit();
}

$livro_editar = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int) $_GET['edit_id'];
    $stmt = $pdo->prepare('SELECT id, nomeLivro, edicao, genero_id, autor_id, editora_id FROM tbLivro WHERE id = ?');
    $stmt->execute([$edit_id]);
    $livro_editar = $stmt->fetch();
}

$livros = $pdo->query('SELECT l.id, l.nomeLivro, l.edicao, g.nome AS genero, a.nome AS autor, e.nome AS editora
                       FROM tbLivro l
                       JOIN tbGenero g ON l.genero_id = g.id
                       JOIN tbAutor a ON l.autor_id = a.id
                       JOIN tbEditora e ON l.editora_id = e.id')->fetchAll();

$generos = $pdo->query('SELECT id, nome FROM tbGenero')->fetchAll();
$autores = $pdo->query('SELECT id, nome FROM tbAutor')->fetchAll();
$editoras = $pdo->query('SELECT id, nome FROM tbEditora')->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Livro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #007bff;
            color: white;
            padding: 15px;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }

        .card-title-custom {
            font-size: 1.25rem;
            font-weight: 500;
        }

        .card-subtitle-custom {
            font-size: 1rem;
            color: #6c757d;
        }

        .card-text-custom {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .btn-custom {
            font-size: 0.85rem;
            padding: 0.4rem 0.75rem;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <h2>Livros Cadastrados</h2>
            <div class="row">
                <?php foreach ($livros as $livro): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title card-title-custom"><strong>Nome do Livro:</strong> <?= htmlspecialchars($livro['nomeLivro']) ?></h5>
                                <h6 class="card-subtitle mb-2 card-subtitle-custom"><strong>Gênero:</strong> <?= htmlspecialchars($livro['genero']) ?></h6>
                                <p class="card-text card-text-custom"><strong>Edição:</strong> <?= htmlspecialchars($livro['edicao']) ?></p>
                                <p class="card-text card-text-custom"><strong>Autor:</strong> <?= htmlspecialchars($livro['autor']) ?></p>
                                <p class="card-text card-text-custom"><strong>Editora:</strong> <?= htmlspecialchars($livro['editora']) ?></p>
                                <div class="d-flex justify-content-between">
                                    <a href="#" class="btn btn-primary btn-custom" onclick="loadContent('add_livro.php?edit_id=<?= $livro['id'] ?>', 'Cadastro de Livro'); return false;">Editar</a>
                                    <a href="#" class="btn btn-danger btn-custom" onclick="confirmDelete(<?= $livro['id'] ?>, 'add_livro'); return false;">Excluir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-3">
            <h2>Cadastrar Livro</h2>
            <div class="card card-custom">
                <div class="card-body">
                    <form id="livro-form" method="POST" action="add_livro.php">
                        <input type="hidden" name="id" value="<?= $livro_editar['id'] ?? '' ?>">

                        <div class="mb-3">
                            <label for="nomeLivro" class="form-label">Nome do Livro</label>
                            <input type="text" id="nomeLivro" name="nomeLivro" class="form-control" value="<?= htmlspecialchars($livro_editar['nomeLivro'] ?? '') ?>" placeholder="Nome do Livro" required>
                        </div>

                        <div class="mb-3">
                            <label for="edicao" class="form-label">Edição</label>
                            <input type="number" id="edicao" name="edicao" class="form-control" value="<?= htmlspecialchars($livro_editar['edicao'] ?? '') ?>" placeholder="Edição">
                        </div>

                        <div class="mb-3">
                            <label for="genero_id" class="form-label">Gênero</label>
                            <select id="genero_id" name="genero_id" class="form-select" required>
                                <?php foreach ($generos as $genero): ?>
                                    <option value="<?= $genero['id'] ?>" <?= isset($livro_editar) && $livro_editar['genero_id'] == $genero['id'] ? 'selected' : '' ?>><?= htmlspecialchars($genero['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="autor_id" class="form-label">Autor</label>
                            <select id="autor_id" name="autor_id" class="form-select" required>
                                <?php foreach ($autores as $autor): ?>
                                    <option value="<?= $autor['id'] ?>" <?= isset($livro_editar) && $livro_editar['autor_id'] == $autor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($autor['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editora_id" class="form-label">Editora</label>
                            <select id="editora_id" name="editora_id" class="form-select" required>
                                <?php foreach ($editoras as $editora): ?>
                                    <option value="<?= $editora['id'] ?>" <?= isset($livro_editar) && $livro_editar['editora_id'] == $editora['id'] ? 'selected' : '' ?>><?= htmlspecialchars($editora['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><?= isset($livro_editar) ? 'Atualizar' : 'Adicionar' ?></button>
                        <?php if (isset($livro_editar)): ?>
                            <button type="button" class="btn btn-secondary w-100 mt-2" onclick="loadContent('add_livro.php', 'Cadastro de Livro')">Cancelar</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
