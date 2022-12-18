<?php
session_start();
include('../../protect/Protect.php');
include('../../bd_conexao/conexao.php');


$id_prof = $_SESSION['Id_prof'];
$itens_pagina = 2; // quantidade por página
$pagina = 0; //página atual

if(!isset($_GET['busca'])){
}
$nome = "%".trim($_GET['busca'])."%";
$dbh = new PDO('mysql:host=localhost; dbname=streamline', 'root', '');

//FILTRAGEM
$dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);

if(!empty($dados['dataPublicacao'])){
  $sth = $dbh->prepare ("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia, C.ativo,
  A.profissao, S.preco_final, S.data_contratacao 
  FROM servico S  
  INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
  INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
  WHERE C.ativo = 1 and S.data_finalizacao > 0 and S.Id_prof = $id_prof ORDER BY S.data_contratacao and C.nome_cli LIKE :nome  DESC LIMIT $pagina, $itens_pagina;");
  $sth->bindParam(':nome', $nome, PDO::PARAM_STR);
  $sth->execute();
  $resultados = $sth->fetchAll(PDO::FETCH_ASSOC);

}
elseif(!empty($dados['avali'])){ 
  $sth = $dbh->prepare ("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia,  E.Avaliacao_cli,
  A.profissao, S.preco_final, S.data_contratacao 
  FROM servico S  
  INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
  INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
  INNER JOIN avaliacoescliente E ON S.Id_cli = E.Id_cli
  WHERE S.data_finalizacao > 0 and S.Id_prof = $id_prof ORDER BY A.avaliacao_cli and C.nome_cli LIKE :nome  DESC LIMIT $pagina, $itens_pagina;");
  $sth->bindParam(':nome', $nome, PDO::PARAM_STR);
  $sth->execute();
  $resultados = $sth->fetchAll(PDO::FETCH_ASSOC);
}
elseif(!empty($dados['cid'])){ 
  $sth = $dbh->prepare("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia, 
  A.profissao, S.preco_final, S.data_contratacao 
  FROM servico S  
  INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
  INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
  WHERE S.data_finalizacao > 0 and S.Id_prof = $id_prof and C.cidade = A.cidade and C.nome_cli LIKE :nome LIMIT $pagina, $itens_pagina;");
  $sth->bindParam(':nome', $nome, PDO::PARAM_STR);
  $sth->execute();
  $resultados = $sth->fetchAll(PDO::FETCH_ASSOC);

}elseif(!empty($dados['estado'])){ 
  $sth = $dbh->prepare ("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia, 
  A.profissao, S.preco_final, S.data_contratacao 
  FROM servico S  
  INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
  INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
  WHERE S.data_finalizacao > 0 and S.Id_prof = $id_prof and C.estado = A.estado and C.nome_cli LIKE :nome LIMIT $pagina, $itens_pagina;");
  $sth->bindParam(':nome', $nome, PDO::PARAM_STR);
  $sth->execute();
  $resultados = $sth->fetchAll(PDO::FETCH_ASSOC);
}
else{
  //CONSULTA BANCO SEM FILTRO
  $sth = $dbh->prepare("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia, 
  A.profissao, S.preco_final, S.data_contratacao 
  FROM servico S  
  INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
  INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
  WHERE S.data_finalizacao > 0 and S.Id_prof = $id_prof and C.nome_cli LIKE :nome LIMIT $pagina, $itens_pagina");
  $sth->bindParam(':nome', $nome, PDO::PARAM_STR);
  $sth->execute();
  $resultados = $sth->fetchAll(PDO::FETCH_ASSOC); 
  }
//FIM DO FILTRO

//PAGINAÇÃO
//quantidade de valores no banco de dados
$num_total = $con->query("SELECT C.Id_cli, C.nome_cli, C.genero, C.estado, C.cidade, C.foto_perfil, C.biografia,
A.profissao, S.preco_final, S.data_contratacao 
FROM servico S  
INNER JOIN cadastroprofissional A ON S.Id_prof = A.Id_prof 
INNER JOIN cadastrocliente C ON S.Id_cli = C.Id_cli 
WHERE S.data_finalizacao > 0 and S.Id_prof = $id_prof")->num_rows;
$num_paginas = ceil($num_total / $itens_pagina);
//FIM PAGINAÇÃO
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
  <meta name="format-detection" content="telephone=no" />
  <meta name="msapplication-tap-highlight" content="no" />
  <meta name="viewport" content="initial-scale=1, width=device-width, viewport-fit=cover" />
  <link rel="stylesheet" href="../../../node_modules/bootstrap/dist/css/bootstrap.css" />
  <link rel="stylesheet" href="../../../node_modules/bootstrap/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../../css/bodyContract.css" />
  <link rel="stylesheet" href="../../../css/navbar.css" />
  <link href="../../node_modules/jquery/dist/jquery.js" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
  <script src="../../../node_modules/bootstrap/dist/js/bootstrap.js"></script>
  <script src="../../../node_modules/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../../../js/function.js"></script>
  <title>Trabalhos Realizados</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-faded" style="background-color: #152c40">
        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 min-vh-100">
          <br />
          <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-2 d-none d-sm-inline" style="font-weight: bold; color: #f2f2f2">StreamLine</span>
          </a>
          <!-- BUSCA -->
          <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <form action="buscaTrabalhosRealizados.php" method="GET" name="buscaAutonomo">
              <div class="search-boxData">
                <input type="text" name="busca" class="search-textData" placeholder="Buscar">
                <a href="javascript:buscaAutonomo.submit()" class="search-btnData">
                  <img src="../../../img/search.svg" alt="lupa" height="20" width="20" style="margin-right: 5px;">
                </a>
              </div>
            </form>
            <!-- FIM DA BUSCA -->
            <li class="nav-item">
              <a href="../InicialAutonomoTrabPendentes.php" class="nav-link homenav align-middle px-0 pt-0" style="color: #BF9052">
                <i class="ms-2 fs-4 fa fa-home"></i> <span class="ms-1 d-none d-sm-inline"
                  style="font-weight: bold; font-size: 17px;">Home</span>
              </a>
            </li>
            <li>
              <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle" style="color: #BF9052">
                <i class="ms-2 fs-5 fa fa-user"></i> <span class="ms-1 d-none d-sm-inline" style="font-weight: bold;">Perfil</span>
                <i class="ms-1 fa fa-caret-down" aria-hidden="true"></i> </a>
              <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                <li class="w-100">
                  <a href="dadosPessoaisAutonomo.php" style="color: cornflowerblue;" class="nav-link px-0"><i
                      class="ms-3 fs-6 fa fa-user"></i> <span class="ms-1 d-none d-sm-inline">Dados pessoais</span></a>
                </li>
                <li>
                  <a href="dadosPublicosAutonomo.php" style="color: cornflowerblue;" class="nav-link px-0"><i
                      class="ms-3 fs-6 fa fa-users"></i> <span class="ms-1 d-none d-sm-inline">Dados públicos</span></a>
                </li>
              </ul>
            </li>
            <li>
              <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle" style="color: #BF9052">
                <i class="ms-2 fs-5 fa fa-briefcase"></i> <span class="ms-1 d-none d-sm-inline" style="font-weight: bold;">Trabalhos</span> <i
                  class="ms-1 fa fa-caret-down" aria-hidden="true"></i> </a>
              <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
                <li class="w-100">
                  <a href="TrabalhosPendentes.php" style="color: cornflowerblue;" class="nav-link px-0"><i
                      class="ms-3 fs-6 fa fa-unlock"></i> <span class="ms-1 d-none d-sm-inline">Pendentes</span></a>
                </li>
                <li>
                  <a href="TrabalhosRealizados.php" style="color: cornflowerblue;" class="nav-link px-0"><i
                      class="ms-3 fs-6 fa fa-lock"></i> <span class="ms-1 d-none d-sm-inline">Realizados</span></a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div class="col py-3">
        <div class="container">

          <!-- filtro -->
          <form action="../TrabalhosRealizados.php" method="get" name="filtrobusca">
            <div class="accordion-color accordion-flush" id="accordionFlush">
              <div class="accordion-item">
                <a class="accordion collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                  aria-expanded="false" aria-controls="flush-collapseOne"
                  style="text-decoration: none; color: black; font-family: Arial, Helvetica, sans-serif;">
                  <h5 class="accordion-header" id="flush-headingOne"
                    style="padding-left: 2%; padding-top: 2%; padding-bottom: 1%;">
                    <img src="../../../img/filter.png" alt="filter"
                      style="height: 40px; width: 40px; margin-right: 1%; cursor: pointer;">Perfis de Clientes
                  </h5>
                </a>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                  data-bs-parent="#accordionFlush">
                  <div class="accordion-body">
                    <div class="row">

                      <div class="col-md-6">
                        <h6 style="text-align: left; margin-bottom: 15px">Ordenar Por</h6>
                        <center>
                          <div>

                            <label for="dtpubli" class="l-radio">
                              <input type="radio" id="dtpubli" name="dataPublicacao" value="dataPublicacao"
                                tabindex="5">
                              <span>Data da publicação</span>
                            </label>

                            <label for="avali" class="l-radio">
                              <input type="radio" id="avali" name="avali" value="avali" tabindex="6">
                              <span>Avaliações</span>
                            </label>
                          </div>
                        </center>
                      </div>
                      <div class="col">
                        <h6 style="text-align: left; margin-bottom: 10px">Características</h6>
                        <center>
                          <div>
                            <label for="estado" class="l-radio">
                              <input type="radio" id="estado" name="estado" value="estado" tabindex="8">
                              <span>Estado</span>
                            </label>

                            <label for="cid" class="l-radio">
                              <input type="radio" id="cid" name="cid" value="cid" tabindex="9">
                              <span>Cidade</span>
                            </label>
                          </div>
                        </center>
                        <button type="submit" value="filtro" name="filtro"
                          style="text-align: center; margin: 10px; padding: 10px"
                          class='btn pull-right'>Filtrar</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <!-- Fim do filtro de busca -->
          
          <div class="card">
            <div class="card-body">

        <?php 
        if(count($resultados)) {
          foreach($resultados as $resultados) { 
            //AVALIAÇÕES
      $id_cli = $resultados['Id_cli'];
      $subconsulta = "SELECT * FROM `avaliacoescliente` where Id_cli = $id_cli;";
      $avaliacoes = $con->query($subconsulta);

      while ($avali = mysqli_fetch_assoc($avaliacoes)) { 
        $id_cli = $avali['Avaliacao_cli'];
        $aval_cli = $avali['Avaliacao_cli'];
        
        $aval_contagem = count((array)$aval_cli);
        $soma_avaliacao = array_sum((array)$aval_cli);
        $aval_valor = $soma_avaliacao/$aval_contagem;
      }
          ?>
          <form method="POST" action="AutonomoFinalizarServico.php" id="form" name="form">
            <div class="row">
              <div class="col-sm-4">
                <div class="estrelas">
                <label for="cm_star-1"><?php echo substr($aval_valor,0,3); ?>  <i class="fa"></i></label>
                </div>
                <center>
                  <img src="<?php echo '../../cliente/perfil/' . $resultados['foto_perfil'] ?>" class="img-fluid" alt="user" 
                  style="width: 150px; height: 150px; object-fit: cover; margin-top: 3%; margin-bottom: 5%; border-radius: 50%;">
                </center>
              </div>
              <div class="col-md-4" id="infocima">
                <div class="form-group">
                  <label class="label" for="Nome">Nome:</label>
                  <input type="text" class="form-control" required name="nome" id="nome" value="<?php echo $resultados['nome_cli']; ?>" disabled />
                </div>
                <div class="form-group">
                  <label class="label" for="TrabPendente">Trabalho Pendente:</label>
                  <input type="text" class="form-control" required name="TrabPendente" id="TrabPendente" value="<?php echo $resultados['profissao']; ?>" disabled />
                </div>
              </div>
              <div class="col-md-4" id="infocima">
                <div class="form-group">
                  <label class="label" for="DataContract">Data da Contratação:</label>
                  <input class="form-control" required name="DataContract" id="DataContract" value="<?php echo date('d/m/Y', strtotime($resultados['data_contratacao'])); ?>" disabled />
                </div>
                <div class="form-group">
                  <label class="label" for="Preco">Valor Total:</label>
                  <input type="text" class="form-control" required name="Preco" id="Preco" value="<?php echo $resultados['preco_final'] ?>" disabled />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" style="padding-top: 3px;">
                  <label class="label" for="Gênero">Gênero:</label>
                  <input type="text" class="form-control" required name="Gênero" id="Gênero" value="<?php echo $resultados['genero']; ?>" disabled />
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label class="label" for="Cidade">Cidade:</label>
                  <input type="text" class="form-control" required name="Cidade" id="Cidade" value="<?php echo $resultados['cidade']; ?>" disabled />
                </div>
              </div>
              <div class="col-md-3" style="padding-top: 5px;">
                <div class="form-group">
                  <label class="label" for="UF">Estado:</label>
                  <input type="text" class="form-control" required name="UF" id="UF" value="<?php echo $resultados['estado']; ?>" disabled />
                </div>
              </div>
              <div class="col-md-12" style="padding-top: 4%; margin-bottom: 10px;">
                <div class="form-group1">
                  <label class="label" for="Biografia">Biografia:</label>
                  <textarea class="form-control" id="Biografia" disabled rows="3"><?php echo $resultados['biografia']; ?></textarea>
                </div>
              </div>
              <input type="hidden" class="form-control" name="cliente" id="cliente" value="<?php echo $resultados['Id_cli']; ?>" />
            </div>
            <div class="col-md-3" style="padding-top: 5px;">
              <div class="form-group">
              </div>
            </div>
            <div class="buttons">
              <button type="submit" id="btnEntrar" class="btnEntrar">
                Descartar
              </button>
              <button type="submit" name="btnPress" id="btnPress" class="btnPress">
                Ver Mais
              </button>
              <br>
              <br>
            </div>
          </form>
        <?php } 
      }else{ ?>
      <!-- **PRECISA ESTILIZAR -->
    <h3>Nenhum Resultado foi encontrado!</h3> 
        <?php   }
        ?>
      </div>
    </div>


          <div class="page-content page-container" id="page-content">
            <div class="padding">
              <div class="row container-row d-flex justify-content-center">

                <div class="col-md-5 col-sm-6 grid-margin stretch-card">
                  <nav>
                    <ul
                      class="pagination d-flex justify-content-center flex-wrap pagination-rounded-flat pagination-success">
                      <li class="page-item"><a class="page-link" href="TrabalhosRealizados.php?pagina=0"
                          data-abc="true"><i class="fa fa-angle-left"></i></a>
                        <?php for ($i = 0; $i < $num_paginas; $i++) {
                    $estilo = "";
                    if ($pagina == $i)
                      $estilo = "class=\"page-item active\"";
                  ?>
                      </li>
                      <li <?php echo $estilo; ?> class="page-item active"><a class="page-link"
                          href="TrabalhosRealizados.php?pagina=<?php echo $i; ?>" data-abc="true">
                          <?php echo $i + 1; ?>
                        </a></li>
                      <?php } ?>
                      <!-- Fim da numeração -->
                      <li class="page-item"><a class="page-link"
                          href="TrabalhosRealizados.php?pagina=<?php echo $num_paginas - 1; ?>" data-abc="true"><i
                            class="fa fa-angle-right"></i></a></li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</body>


</html>
