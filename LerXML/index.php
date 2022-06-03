<?php 
$xml = simplexml_load_file('vendas.xml');

echo "Titulo " . $xml->titulo . "<br>";

echo "Data Atualizacao:" . $xml->data_atualizacao . "<br>";

foreach ($xml->venda as $info){
  echo "<b>Código Venda: </b>" . $info->cod_venda . "<br>";
  echo "<b>Cliente: </>" . $info->cliente . "<br>";
  echo "<b>E-mail: </b>" .$info->email . "<br>";
    foreach ($info->itens->item as $item){
      echo "<ul>";
      echo "<li> <b>Código Produto: </b> " . $item->cod_produto . "</li>";
      echo "<li> <b>Quantidade: </b> " . $item->qtde . "</li>";
      echo "<li> <b>Descrição: </b> " . $item->descricao . "</li>";
      echo "</ul>";
      echo '<hr>';
    }
}