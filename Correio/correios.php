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

    echo "<pre>";
    print_r($response);
    echo "</pre>";exit;
  }

}

//Nova instancia 
$obCorreios = new Correios();

//Dados Calcular Frete
$codigoServico = Correios::SERVICO_SEDEX;
$cepOrigem = '87045005';
$cepDestino = '87303022';
$peso = 1;
$formato = Correios::FORMATO_CAIXA_PACOTE;
$comprimento = 15;
$altura = 15;
$largura = 15;
$diamentro = 0;
$maoPropria = false;
$valorDeclarado = 0;
$avisoRecebimento = false;

//Executa calculo de frete
$frete = $obCorreios->calcularFrete($codigoServico,$cepOrigem,$cepDestino,$peso,$formato,$comprimento,$altura,$largura,$diamentro,$maoPropria,$valorDeclarado,$avisoRecebimento);

echo "<pre>";
print_r($frete);
echo "</pre>";exit;