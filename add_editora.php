<?php
include('conn.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE tbEditora SET nome = ? WHERE id = ?');
            $stmt->execute([$nome, $id]);
            $response['message'] = 'Editora atualizada com sucesso';
        } else {
            $stmt = $pdo->prepare('INSERT INTO tbEditora (nome) VALUES (?)');
            $stmt->execute([$nome]);
            $response['message'] = 'Editora adicionada com sucesso';
        }
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Por favor, preencha o nome da editora.';
    }
    echo json_encode($response);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tbEditora WHERE id = ?');
    $stmt->execute([$delete_id]);

    $response['status'] = 'success';
    $response['message'] = 'Editora excluída com sucesso';
    echo json_encode($response);
    exit();
}

$editora_editar = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int) $_GET['edit_id'];
    $stmt = $pdo->prepare('SELECT id, nome FROM tbEditora WHERE id = ?');
    $stmt->execute([$edit_id]);
    $editora_editar = $stmt->fetch();
}

$editoras = $pdo->query('SELECT id, nome FROM tbEditora')->fetchAll();
?>

<div class="table-responsive">
    <h2>Editoras Cadastradas</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome da Editora</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($editoras as $editora): ?>
                <tr>
                    <td><?= $editora['id'] ?></td>
                    <td><?= htmlspecialchars($editora['nome']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="loadContent('add_editora.php?edit_id=<?= $editora['id'] ?>', 'Cadastro de Editora')">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $editora['id'] ?>, 'add_editora')">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="form-container">
    <h2><?= isset($editora_editar) ? 'Editar Editora' : 'Adicionar Nova Editora' ?></h2>
    <form id="editora-form" method="POST" action="add_editora.php" class="form-inline">
        <input type="hidden" name="id" value="<?= $editora_editar['id'] ?? '' ?>">
        <div class="form-group mb-2">
            <label for="nome" class="sr-only">Nome da Editora</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($editora_editar['nome'] ?? '') ?>" placeholder="Nome da Editora" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><?= isset($editora_editar) ? 'Atualizar' : 'Adicionar' ?></button>
        <?php if (isset($editora_editar)): ?>
            <button type="button" class="btn btn-secondary mb-2" onclick="loadContent('add_editora.php', 'Cadastro de Editora')">Cancelar</button>
        <?php endif; ?>
    </form>
</div>
