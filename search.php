<?php
include('conn.php');

// Verifica se o parâmetro de pesquisa foi enviado
if (isset($_GET['query'])) {
    $search = htmlspecialchars($_GET['query']);

    // Consulta para buscar livros pelo nome ou ID
    $stmt = $pdo->prepare('SELECT tbLivro.id, tbLivro.nomeLivro, tbGenero.nome AS genero, tbAutor.nome AS autor, tbEditora.nome AS editora, tbLivro.edicao FROM tbLivro
        JOIN tbGenero ON tbLivro.genero_id = tbGenero.id
        JOIN tbAutor ON tbLivro.autor_id = tbAutor.id
        JOIN tbEditora ON tbLivro.editora_id = tbEditora.id
        WHERE tbLivro.nomeLivro LIKE ? OR tbLivro.id = ?');
    $stmt->execute(['%' . $search . '%', $search]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($search_results);
}
?>
