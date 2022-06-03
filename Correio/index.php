<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<!--Interface do Sistema-->
<nav class="navbar navbar-light bg-light">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Calcular Frete</span>
  </div>
</nav>

<div class="container">
  <div class="row align-items-start">
    <div class="col"></div>
    <div class="col">
  <form>
    <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">CEP Origem</label>
    <input type="email" class="form-control" id="CEP_ORIGEM" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">CEP Destino</label>
    <input type="email" class="form-control" id="CEP_ORIGEM" aria-describedby="emailHelp">
  </div>
  
  <div class="mb-3">
  <select class="form-select" aria-label="Default select example">
  <option selected>Selecione o Serviço</option>
  <option value="1">SEDEX</option>
  <option value="2">SEDEX 10</option>
  <option value="3">SEDEX 12</option>
  <option value="3">SEDEX HOJE</option>
  <option value="3">PAC</option>
</select>
</div>

<div class="mb-3">
<select class="form-select" aria-label="Default select example">
  <option selected>Selecione com Formato da Embalagem</option>
  <option value="1">Caixa ou Pacote</option>
  <option value="2">Rolo ou Prisma</option>
  <option value="3">Envelope</option>
</select>
</div>

<div class="mb-3">
    <label for="PESO" class="form-label">Peso do Produto (Com a embalagem)</label>
    <input type="text" class="form-control" id="PESO">
  </div>
  <div class="mb-3">
    <label for="COMPRIMENTO" class="form-label">Comprimento da Embalagem</label>
    <input type="text" class="form-control" id="COMPRIMENTO">
  </div>
  <div class="mb-3">
    <label for="ALTURA" class="form-label">Altura da Embalagem</label>
    <input type="text" class="form-control" id="ALTURA">
  </div>
  <div class="mb-3">
    <label for="LARGURA" class="form-label">Largura da embalagem</label>
    <input type="text" class="form-control" id="LARGURA">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
    </div>
    <div class="col"></div>
</div>


<!---->
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

//Nova instancia 
$obCorreios = new Correios();

//Dados Calcular Frete
$codigoServico = Correios::SERVICO_SEDEX;
$cepOrigem = '87303070';
$cepDestino = '65050883';
$peso = 1;
$formato = Correios::FORMATO_CAIXA_PACOTE;
$comprimento = 30;
$altura = 10;
$largura = 30;
$diamentro = 0;
$maoPropria = false;
$valorDeclarado = 0;
$avisoRecebimento = false;

//Executa calculo de frete
$frete = $obCorreios->calcularFrete($codigoServico,$cepOrigem,$cepDestino,$peso,$formato,$comprimento,$altura,$largura,$diamentro,$maoPropria,$valorDeclarado,$avisoRecebimento);

//Verifica o resultado 
if(!$frete){
  die('Problemas ao calcular o frete');
}

//Verifica se possui dentro do XML
if(strlen($frete->MsgErro)){
  die('Erro: ' . $frete->MsgErro);
}

// echo "CEP ORIGEM: " . $cepOrigem . "<br>";
// echo "CEP DESTINO: " . $cepDestino . "<br>";
// echo "VALOR: " . $frete->Valor . "<br>";
// echo "TEMPO ENTREGA: " . $frete->PrazoEntrega . " dias<br>";

// echo "<pre>";
// print_r($frete);
// echo "</pre>";
?>

