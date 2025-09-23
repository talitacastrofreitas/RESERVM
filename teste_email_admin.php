<?php
include './conexao/conexao.php';

$admin_email = null;

try {
    // CORREÇÃO AQUI: Use aspas simples para o valor '1'
    // porque admin_id é CHAR(32)
    // Se o admin_id for um GUID completo (ex: '099023f595d1c09977db86efb499aae5'),
    // você precisaria usar o GUID correto entre aspas.
    $sql_get_admin_email = "SELECT admin_email FROM admin WHERE admin_id = '1'";
    // ^^^^^^ Use aspas aqui!

    $stmt_get_admin_email = $conn->prepare($sql_get_admin_email);
    // Não precisa de bindParam aqui se você está colocando o valor diretamente na string SQL
    // Mas é uma boa prática usar bindParam:
    // $adminIdParaBuscar = '1'; // Se o ID do admin é a string '1'
    // $stmt_get_admin_email->bindParam(':admin_id', $adminIdParaBuscar, PDO::PARAM_STR);
    // E a consulta seria: "SELECT admin_email FROM admin WHERE admin_id = :admin_id"

    $stmt_get_admin_email->execute();
    $result_admin = $stmt_get_admin_email->fetch(PDO::FETCH_ASSOC);

    if ($result_admin && !empty($result_admin['admin_email'])) {
        $admin_email = $result_admin['admin_email'];
    } else {
        echo "Aviso: E-mail do administrador **NÃO ENCONTRADO** no banco de dados com o critério especificado (`admin_id = '1'`).<br>";
    }
} catch (PDOException $e) {
    echo "Erro de banco de dados (corrigido?): " . $e->getMessage() . "<br>";
}

echo "--- Resultado da Verificação ---<br>";
echo "Valor da variável \$admin_email: **" . htmlspecialchars($admin_email) . "**<br>";
echo "A variável \$admin_email está vazia? " . (empty($admin_email) ? "**Sim**" : "**Não**") . "<br>";
echo "Tipo da variável \$admin_email: " . gettype($admin_email) . "<br>";
?>