<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">

<?php

// phpinfo();
//Envia requisição para a pagina e retorna Html
$content = file_get_contents("http://www.infomoney.com.br/mercados/cambio");

//domDocument carregar a página Html para capturar seu elementos
//@ serve para ignorar erros do HTML
$dom = new domDocument();
@$dom->loadHTML($content);

//Com a classe DOMXPath podemos criar queries e assim encontrar informações mais facilmente
//Expressões XPath: 
// - nodename -> seleciona todos os elementos com nodename
// - / (barra) -> seleciona o elemento root 
// - "//" (duas barras) -> seleciona o elemento a partir do nó atual
// - . (ponto) -> seleciona o elemento atual
// - .. (dois pontos) -> seleciona o elemento pai
// - @ (Arroba) -> Seleciona a partir dos atributos

$xpath = new DOMXPath($dom);
//Busca uma TABELA com o ATRIBUTO classe = table-general 
$tables = $xpath->query("//table[1]");
//Busca na tabela encontrada um elemento "pai" chamado TBODY e "filho" TR da primeira tabela
$values = $xpath->query(".//tbody/tr", $tables->item(0));


//Guardar valores para apresentar ao usuário
$currencies = [];

//Percorre todos os itens da tabela 
//para cada item de $VALUES atribua a $VALUE
foreach($values as $value) {    
    
    //Busca valores da td (table data) de cada tr(table row)
    $currency = $xpath->query(".//td", $value);
    //Pega o valor , retira os espaços vazios do incio e fim com o metodo trim
    $name= trim($currency->item(0)->textContent);

    //Armazena valor de compra -> Terceiro filho da tr(Table Row);
    $purchasePrice = trim($currency->item(2)->textContent);

    //Armazena o valor de venda -> Quarto filho da tr(Table Row)
    $salePrice= trim($currency->item(3)->textContent);

    //Armazena no array/Vetor criado anteriormente
    $currencies[] = [
        "name"=> $name,
        "purchasePrice"=> $purchasePrice,
        "salePrice"=> $salePrice,
    ];
}

?>

<table class="pure-table">
    <thead>
        <tr>
            <th>Moeda</th>
            <th>Compra</th>
            <th>Venda</th>
        </tr>
    </thead>
    <tbody>
        <!-- Percorre todo o array criado -->
        <?php foreach($currencies as $currency): ?>
        <tr>
            <!-- Exibe nome da moeda -->
            <td><?php echo $currency['name'] ?></td>
            <!-- Exibe valor da compra -->
            <td><?php echo $currency['purchasePrice'] ?></td>
            <!-- Exibe valor da venda -->
            <td><?php echo $currency['salePrice'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>