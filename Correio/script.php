<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">

<?php
class Correios{
  //URL base API
  const URL_BASE = 'http://ws.correios.com.br';

  //Codigos de Serviços dos correios
  //Com contrator valores podem ser diferentes
  const SERVICO_SEDEX = '04014';
  const SERVICO_SEDEX_12 = '04782';
  const SERVICO_SEDEX_10 = '04790';
  const SERVICO_SEDEX_HOJE = '04804';
  const SERVICO_PAC = '04510';
  
  //Codigos do formatos dos correios
  const FORMATO_CAIXA_PACOTE = 1;
  const FORMATO_ROLO_PRISMA = 2;
  const FORMATO_ENVELOPE = 3;


  private $codigoEmpresa = '';
  private $senhaEmpresa = '';

  //dados de empresas com contrato do correio
  //dados unicos,não dinamicos
  public function __contruct($codigoEmpresa = '',$senhaEmpresa = ''){
    $this->codigoEmpresa = $codigoEmpresa;
    $this->senhaEmpresa = $senhaEmpresa;
  }

  //dados dinamicos
  public function calcularFrete($codigoServico,$cepOrigem,$cepDestino,$peso,$formato,$comprimento,$altura,$largura,$diamentro = 0,$maoPropria = false,$valorDeclarado = 0,$avisoRecebimento = false){
    
    //Pametros da URL calculo de frete

    $parametros = [
      'nCdEmpresa' => $this->codigoEmpresa,
      'sDsSenha' => $this->senhaEmpresa,
      'nCdServico' => $codigoServico,
      'sCepOrigem' => $cepOrigem,
      'sCepDestino' => $cepDestino,
      'nVlPeso' => $peso,
      'nCdFormato' => $formato,
      'nVlComprimento' => $comprimento,
      'nVlAltura' => $altura,
      'nVlLargura' => $largura,
      'nVlDiametro' => $diamentro,
      'sCdMaoPropria' => $maoPropria?'S':'N',
      'nVlValorDeclarado' => $valorDeclarado,
      'sCdAvisoRecebimento' => $avisoRecebimento?'S':'N',
      'StrRetorno' => 'xml'
    ];

    //metodo para criar query , solicitação para os correios, busca valor
    $QUERY = http_build_query($parametros);
    
    //Fazer consulta de frete
    $result = $this->get('/calculador/CalcPrecoPrazo.aspx?'.$QUERY);

    //retorna os dados do frete calculado
    return $result? $result->cServico: null;
  }

  //Executa requisição GET no webService dos Correios
  public function get($resource){

    //ENDPOINT Completo
    $endPoint = self::URL_BASE.$resource;

    //Inicia o CURL
    $curl = curl_init();

    //Configurações do curl
    //ARRAY de configurações
    //retorna o conteudo, não direto apresentar na tela
    curl_setopt_array($curl,[
      CURLOPT_URL => $endPoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    //Executa a consulta
    $response = curl_exec($curl);

    //fecha connecção com curl
    curl_close($curl);

    //se existir retorna um xml
    return strlen($response)?simplexml_load_string($response):null;
  }

}

//Valor default para MAO_PROPRIA
if(isset($_POST['MAO_PROPRIA'])){
  $MAO_PROPRIA = $_POST['MAO_PROPRIA'];
}else{
  $MAO_PROPRIA = false;
}

//Valor default para RECEBIMENTO
if(isset($_POST['RECEBIMENTO'])){
  $RECEBIMENTO = $_POST['RECEBIMENTO'];
}else{
  $RECEBIMENTO = false;
}

  //requisição de dados do formulario via metodo POST
  $CEP_DESTINO = $_POST['CEP_DESTINO'];
  $CEP_ORIGEM = $_POST['CEP_ORIGEM'];
  $SERVICOS = $_POST['SERVICOS'];
  $PESO = $_POST['PESO'];
  $COMPRIMENTO = $_POST['COMPRIMENTO'];
  $ALTURA = $_POST['ALTURA'];
  $LARGURA = $_POST['LARGURA'];
  $DIAMETRO = $_POST['DIAMETRO'];
  $FORMATO = $_POST['FORMATO'];

  
  $obCorreios = new Correios();

  //Envia dados para o metodo calcular frete
  $frete = $obCorreios->calcularFrete($SERVICOS,
                                      $CEP_ORIGEM,
                                      $CEP_DESTINO,
                                      $PESO,
                                      $FORMATO,
                                      $COMPRIMENTO,
                                      $ALTURA,
                                      $LARGURA,
                                      $DIAMETRO,
                                      $MAO_PROPRIA,
                                      0,
                                      $RECEBIMENTO);

  //Verifica o resultado 
  //Se algum dado estiver incorreto ou faltando
    if(!$frete){
      die('Problemas ao calcular o frete');
    }

    //Verifica se possui erros e apresenta dentro do XML
    if(strlen($frete->MsgErro)){
      die('Erro: ' . $frete->MsgErro);
    }

    // echo "<pre>";
    // print_r($frete);
    // echo "</pre>";

    //Facilitar a visualização do tipo de formato
    if($FORMATO == 1){
      $FORMATO_TXT =  "Caixa/Pacote";
    }else if($FORMATO == 2){
      $FORMATO_TXT = "Rolo/Prisma";
    }else{
      $FORMATO_TXT = "Envelope";
    }
    
?>

<!--Apresentação dos resultados e as informações adicionadas no formulário-->
<body class="bg-info text-dark bg-opacity-10">
<main >
    <div class="container-sm mt-5">
        <div class="alert alert-info" role="alert">
          <h4 class="alert-heading">Frete Calculado com sucesso !!!</h4>
          <p>Valores Calculados a partir das Taxas do dia <b><?php echo date('d/m/Y')?></b></p>
            <hr>
              <p class="mb-0"><b>SERVIÇO: </b>  <?php echo $SERVICOS; ?> </p>
              <p class="mb-0"><b>CEP DE ORIGEM: </b>  <?php echo $CEP_ORIGEM; ?> </p>
              <p class="mb-0"><b>CEP DE DESTINO: </b>  <?php echo $CEP_DESTINO; ?> </p>
              <p class="mb-0"><b>FORMATO: </b> <?php echo $FORMATO_TXT ?> </p>
              <p class="mb-0"><b>PESO (kg): </b>  <?php echo $PESO; ?> kg</p>
              <br>
              <p class="mb-0"><b>DADOS DA EMBALAGEM</b></p>
              <p class="mb-0"><b>- COMPRIMENTO (cm): </b>  <?php echo $COMPRIMENTO; ?> cm</p>
              <p class="mb-0"><b>- LARGURA (cm): </b>  <?php echo $LARGURA; ?> cm</p>
              <p class="mb-0"><b>- ALTURA (cm): </b>  <?php echo $ALTURA; ?> cm</p>
              <p class="mb-0"><b>- DIAMETRO (cm): </b>  <?php echo $DIAMETRO; ?> cm</p>
            <hr>
          <p class="mb-0"><b>VALOR TOTAL: </b> R$<?php echo $frete->Valor; ?></p>
          <p class="mb-0"><b>PRAZO DE ENTREGA: </b>  <?php echo $frete->PrazoEntrega;?> dias</p>
      </div>
      <a href="index.html"><button type="button" class="btn btn-info">Voltar</button></a>
      
    </div>
</main>

</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>