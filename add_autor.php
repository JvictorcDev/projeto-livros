<?php
include('conn.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE tbAutor SET nome = ? WHERE id = ?');
            $stmt->execute([$nome, $id]);
            $response['message'] = 'Autor atualizado com sucesso';
        } else {
            $stmt = $pdo->prepare('INSERT INTO tbAutor (nome) VALUES (?)');
            $stmt->execute([$nome]);
            $response['message'] = 'Autor adicionado com sucesso';
        }
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Por favor, preencha o nome do autor.';
    }
    echo json_encode($response);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tbAutor WHERE id = ?');
    $stmt->execute([$delete_id]);

    $response['status'] = 'success';
    $response['message'] = 'Autor excluído com sucesso';
    echo json_encode($response);
    exit();
}

$autor_editar = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int) $_GET['edit_id'];
    $stmt = $pdo->prepare('SELECT id, nome FROM tbAutor WHERE id = ?');
    $stmt->execute([$edit_id]);
    $autor_editar = $stmt->fetch();
}

$autores = $pdo->query('SELECT id, nome FROM tbAutor')->fetchAll();
?>

<div class="table-responsive">
    <h2>Autores Cadastrados</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Autor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($autores as $autor): ?>
                <tr>
                    <td><?= $autor['id'] ?></td>
                    <td><?= htmlspecialchars($autor['nome']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="loadContent('add_autor.php?edit_id=<?= $autor['id'] ?>', 'Cadastro de Autor')">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $autor['id'] ?>, 'add_autor')">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="form-container">
    <h2><?= isset($autor_editar) ? 'Editar Autor' : 'Adicionar Novo Autor' ?></h2>
    <form id="autor-form" method="POST" action="add_autor.php" class="form-inline">
        <input type="hidden" name="id" value="<?= $autor_editar['id'] ?? '' ?>">
        <div class="form-group mb-2">
            <label for="nome" class="sr-only">Nome do Autor</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($autor_editar['nome'] ?? '') ?>" placeholder="Nome do Autor" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><?= isset($autor_editar) ? 'Atualizar' : 'Adicionar' ?></button>
        <?php if (isset($autor_editar)): ?>
            <button type="button" class="btn btn-secondary mb-2" onclick="loadContent('add_autor.php', 'Cadastro de Autor')">Cancelar</button>
        <?php endif; ?>
    </form>
</div>
