<?php
include('conn.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE tbGenero SET nome = ? WHERE id = ?');
            $stmt->execute([$nome, $id]);
            $response['message'] = 'Gênero atualizado com sucesso';
        } else {
            $stmt = $pdo->prepare('INSERT INTO tbGenero (nome) VALUES (?)');
            $stmt->execute([$nome]);
            $response['message'] = 'Gênero adicionado com sucesso';
        }
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Por favor, preencha o nome do gênero.';
    }
    echo json_encode($response);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tbGenero WHERE id = ?');
    $stmt->execute([$delete_id]);

    $response['status'] = 'success';
    $response['message'] = 'Gênero excluído com sucesso';
    echo json_encode($response);
    exit();
}

$genero_editar = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int) $_GET['edit_id'];
    $stmt = $pdo->prepare('SELECT id, nome FROM tbGenero WHERE id = ?');
    $stmt->execute([$edit_id]);
    $genero_editar = $stmt->fetch();
}

$generos = $pdo->query('SELECT id, nome FROM tbGenero')->fetchAll();
?>

<div class="table-responsive">
    <h2>Gêneros Cadastrados</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Gênero</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($generos as $genero): ?>
                <tr>
                    <td><?= $genero['id'] ?></td>
                    <td><?= htmlspecialchars($genero['nome']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="loadContent('add_genero.php?edit_id=<?= $genero['id'] ?>', 'Cadastro de Gênero')">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $genero['id'] ?>, 'add_genero')">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="form-container">
    <h2><?= isset($genero_editar) ? 'Editar Gênero' : 'Adicionar Novo Gênero' ?></h2>
    <form id="genero-form" method="POST" action="add_genero.php" class="form-inline">
        <input type="hidden" name="id" value="<?= $genero_editar['id'] ?? '' ?>">
        <div class="form-group mb-2">
            <label for="nome" class="sr-only">Nome do Gênero</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($genero_editar['nome'] ?? '') ?>" placeholder="Nome do Gênero" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><?= isset($genero_editar) ? 'Atualizar' : 'Adicionar' ?></button>
        <?php if (isset($genero_editar)): ?>
            <button type="button" class="btn btn-secondary mb-2" onclick="loadContent('add_genero.php', 'Cadastro de Gênero')">Cancelar</button>
        <?php endif; ?>
    </form>
</div>
