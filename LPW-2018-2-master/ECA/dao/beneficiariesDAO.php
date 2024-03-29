<?php

require_once "db/connection.php";
require_once "classes/beneficiaries.php";

class beneficiariesDAO
{

    public function remover($beneficiaries)
    {
        global $pdo;
        try {
            $statement = $pdo->prepare("DELETE FROM tb_beneficiaries WHERE id_beneficiaries = :id");
            $statement->bindValue(":id", $beneficiaries->getIdBeneficiaries());
            if ($statement->execute()) {
                return "<script> alert('Registo foi excluído com êxito !'); </script>";
            } else {
                throw new PDOException("<script> alert('Não foi possível executar a declaração SQL !'); </script>");
            }
        } catch (PDOException $erro) {
            return "Erro: " . $erro->getMessage();
        }
    }

    public function salvar($beneficiaries)
    {
        global $pdo;
        try {
            if ($beneficiaries->getIdBeneficiaries() != "") {
                $statement = $pdo->prepare("UPDATE tb_beneficiaries SET str_nis=:str_nis, str_name_person=:str_name_person, str_cpf=:str_cpf, int_rgp:int_rgp WHERE id_beneficiaries = :id;");
                $statement->bindValue(":id", $beneficiaries->getIdBeneficiaries());
            } else {
                $statement = $pdo->prepare("INSERT INTO tb_beneficiaries (str_nis, str_name_person,str_cpf,int_rgp) VALUES (:str_nis, :str_name_person, :str_cpf, :int_rgp)");
            }
            $statement->bindValue(":str_nis", $beneficiaries->getStrNis());
            $statement->bindValue(":str_name_person", $beneficiaries->getStrNamePerson());
            $statement->bindValue(":str_cpf", $beneficiaries->getStrCpf());
            $statement->bindValue(":int_rgp", $beneficiaries->getIntRgp());
            if ($statement->execute()) {
                if ($statement->rowCount() > 0) {
                    return "<script> alert('Dados cadastrados com sucesso !'); </script>";
                } else {
                    return "<script> alert('Erro ao tentar efetivar cadastro !'); </script>";
                }
            } else {
                throw new PDOException("<script> alert('Não foi possível executar a declaração SQL !'); </script>");
            }
        } catch (PDOException $erro) {
            return "Erro: " . $erro->getMessage();
        }
    }

    public function atualizar($beneficiaries)
    {
        global $pdo;
        try {
            $statement = $pdo->prepare("SELECT id_beneficiaries, str_nis, str_name_person, str_cpf,int_rgp FROM tb_beneficiaries WHERE id_beneficiaries = :id");
            $statement->bindValue(":id", $beneficiaries->getIdBeneficiaries());
            if ($statement->execute()) {
                $rs = $statement->fetch(PDO::FETCH_OBJ);
                $beneficiaries->setIdBeneficiaries($rs->id_beneficiaries);
                $beneficiaries->setStrNis($rs->str_nis);
                $beneficiaries->setStrNamePerson($rs->str_name_person);
                $beneficiaries->setStrCpf($rs->str_cpf);
                $beneficiaries->setIntRgp($rs->int_rgp);


                return $beneficiaries;
            } else {
                throw new PDOException("<script> alert('Não foi possível executar a declaração SQL !'); </script>");
            }
        } catch (PDOException $erro) {
            return "Erro: " . $erro->getMessage();
        }
    }

    public function tabelapaginada()
    {

        //carrega o banco
        global $pdo;

        //endereço atual da página
        $endereco = $_SERVER ['PHP_SELF'];

        /* Constantes de configuração */
        define('QTDE_REGISTROS', 10);
        define('RANGE_PAGINAS', 2);

        /* Recebe o número da página via parâmetro na URL */
        $pagina_atual = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

        /* Calcula a linha inicial da consulta */
        $linha_inicial = ($pagina_atual - 1) * QTDE_REGISTROS;

        /* Instrução de consulta para paginação com MySQL */
        $sql = "SELECT id_beneficiaries, str_nis, str_name_person, str_cpf, int_rgp FROM tb_beneficiaries LIMIT {$linha_inicial}, " . QTDE_REGISTROS;
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $dados = $statement->fetchAll(PDO::FETCH_OBJ);

        /* Conta quantos registos existem na tabela */
        $sqlContador = "SELECT COUNT(*) AS total_registros FROM tb_beneficiaries";
        $statement = $pdo->prepare($sqlContador);
        $statement->execute();
        $valor = $statement->fetch(PDO::FETCH_OBJ);

        /* Idêntifica a primeira página */
        $primeira_pagina = 1;

        /* Cálcula qual será a última página */
        $ultima_pagina = ceil($valor->total_registros / QTDE_REGISTROS);

        /* Cálcula qual será a página anterior em relação a página atual em exibição */
        $pagina_anterior = ($pagina_atual > 1) ? $pagina_atual - 1 : 0;

        /* Cálcula qual será a pŕoxima página em relação a página atual em exibição */
        $proxima_pagina = ($pagina_atual < $ultima_pagina) ? $pagina_atual + 1 : 0;

        /* Cálcula qual será a página inicial do nosso range */
        $range_inicial = (($pagina_atual - RANGE_PAGINAS) >= 1) ? $pagina_atual - RANGE_PAGINAS : 1;

        /* Cálcula qual será a página final do nosso range */
        $range_final = (($pagina_atual + RANGE_PAGINAS) <= $ultima_pagina) ? $pagina_atual + RANGE_PAGINAS : $ultima_pagina;

        /* Verifica se vai exibir o botão "Primeiro" e "Pŕoximo" */
        $exibir_botao_inicio = ($range_inicial < $pagina_atual) ? 'mostrar' : 'esconder';

        /* Verifica se vai exibir o botão "Anterior" e "Último" */
        $exibir_botao_final = ($range_final > $pagina_atual) ? 'mostrar' : 'esconder';

        if (!empty($dados)):
            echo "
     <table class='table table-striped table-bordered'>
     <thead>
       <tr style='text-transform: uppercase;' class='active'>
        <th style='text-align: center; font-weight: bolder;'>Code</th>
        <th style='text-align: center; font-weight: bolder;'>Nis</th>
        <th style='text-align: center; font-weight: bolder;'>Name</th>
        <th style='text-align: center; font-weight: bolder;'>CPF</th>
        <th style='text-align: center; font-weight: bolder;'>RGP</th>
        <th style='text-align: center; font-weight: bolder;' colspan='2'>Actions</th>
       </tr>
     </thead>
     <tbody>";
            foreach ($dados as $bene):
                echo "<tr>
        <td style='text-align: center'>$bene->id_beneficiaries</td>
        <td style='text-align: center'>$bene->str_nis</td>
        <td style='text-align: center'>$bene->str_name_person</td>
        <td style='text-align: center'>$bene->str_cpf</td>
        <td style='text-align: center'>$bene->int_rgp</td>
        <td style='text-align: center'><a href='?act=upd&id=$bene->id_beneficiaries' title='Alterar'><i class='ti-reload'></i></a></td>
        <td style='text-align: center'><a href='?act=del&id=$bene->id_beneficiaries' title='Remover'><i class='ti-close'></i></a></td>
       </tr>";
            endforeach;
            echo "
</tbody>
     </table>

    <div class='box-paginacao' style='text-align: center'>
       <a class='box-navegacao  $exibir_botao_inicio' href='$endereco?page=$primeira_pagina' title='Primeira Página'> FIRST  |</a>
       <a class='box-navegacao  $exibir_botao_inicio' href='$endereco?page=$pagina_anterior' title='Página Anterior'> PREVIOUS  |</a>
";

            /* Loop para montar a páginação central com os números */
            for ($i = $range_inicial; $i <= $range_final; $i++):
                $destaque = ($i == $pagina_atual) ? 'destaque' : '';
                echo "<a class='box-numero $destaque' href='$endereco?page=$i'> ( $i ) </a>";
            endfor;

            echo "<a class='box-navegacao $exibir_botao_final' href='$endereco?page=$proxima_pagina' title='Próxima Página'>| NEXT  </a>
                  <a class='box-navegacao $exibir_botao_final' href='$endereco?page=$ultima_pagina'  title='Última Página'>| LAST  </a>
     </div>";
        else:
            echo "<p class='bg-danger'>Nenhum registro foi encontrado!</p>
     ";
        endif;

    }


}